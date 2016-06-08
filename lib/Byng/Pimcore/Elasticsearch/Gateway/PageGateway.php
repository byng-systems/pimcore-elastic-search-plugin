<?php

/**
 * This file is part of the "byng/pimcore-elasticsearch-plugin" project.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the LICENSE is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Byng\Pimcore\Elasticsearch\Gateway;

use Byng\Pimcore\Elasticsearch\Filter\FilterInterface;
use Byng\Pimcore\Elasticsearch\Model\ResultsList;
use Byng\Pimcore\Elasticsearch\Processor\Page\PageProcessor;
use Elasticsearch\Client;
use InvalidArgumentException;
use NF\HtmlToText;
use Pimcore\Model\Document\Page;
use Zend_EventManager_EventManager as ZendEventManager;

/**
 * Page Gateway
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Michal Maszkiewicz
 * @author Asim Liaquat <asim@byng.co>
 */
final class PageGateway extends AbstractGateway
{
    /**
     * @var PageGateway
     */
    private static $instance;

    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $type;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var HtmlToText
     */
    private $htmlToTextFilter;

    /**
     * @var PageProcessor
     */
    private $processor;

    /**
     * @var FilterInterface
     */
    private $inputFilter;

    /**
     * @var ZendEventManager
     */
    private $pimcoreEventManager;

    /**
     * Constructor
     *
     * @param array $configuration
     * @param Client $client
     * @param HtmlToText $htmlToTextFilter
     * @param PageProcessor $processor
     * @param FilterInterface $inputFilter
     * @param ZendEventManager $pimcoreEventManager
     */
    public function __construct(
        array $configuration,
        Client $client,
        HtmlToText $htmlToTextFilter,
        PageProcessor $processor,
        FilterInterface $inputFilter,
        ZendEventManager $pimcoreEventManager
    ) {
        if (!isset($configuration["index"])) {
            throw new InvalidArgumentException("Missing configuration setting: index");
        }

        if (!isset($configuration["type"])) {
            throw new InvalidArgumentException("Missing configuration setting: type");
        }

        $this->index = (string) $configuration["index"];
        $this->type = (string) $configuration["type"];
        $this->client = $client;
        $this->htmlToTextFilter = $htmlToTextFilter;
        $this->processor = $processor;
        $this->inputFilter = $inputFilter;
        $this->pimcoreEventManager = $pimcoreEventManager;

        static::$instance = $this;
    }

    /**
     * Get an instance of this gateway after plugin initialisation
     *
     * @return PageGateway
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            throw new \RuntimeException("No instance of PageGateway available, did the Elasticsearch plugin initialise correctly?");
        }

        return static::$instance;
    }

    /**
     * Delete a given document
     *
     * @param Page $document
     *
     * @return array|bool
     */
    public function delete(Page $document)
    {
        $params = [
            "id" => $document->getId(),
            "index" => $this->index,
            "type" => $this->type
        ];

        if (!$this->exists($document)) {
            return false;
        }

        return $this->client->delete($params);
    }

    /**
     * Clears all entries from this index
     *
     * @return array
     */
    public function clear()
    {
        $this->client->indices()->deleteMapping([
            "index" => $this->index,
            "type" => $this->type
        ]);
    }

    /**
     * Save a given document
     *
     * @param Page $document
     *
     * @return void
     */
    public function save(Page $document)
    {
        $pageArray = $this->pimcoreEventManager->prepareArgs(
            $this->pageToArray($document)
        );

        $this->pimcoreEventManager->trigger("document.elasticsearch.preIndex", $document, $pageArray);

        $this->client->index($pageArray->getArrayCopy());
    }

    /**
     * Check if a given document exists
     *
     * @param Page $document
     *
     * @return array
     */
    public function exists(Page $document)
    {
        $params = [
            "id" => $document->getId(),
            "index" => $this->index,
            "type" => $this->type
        ];

        return $this->client->exists($params);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(
        array $mustCriteria = [],
        array $filterCriteria = [],
        array $mustNotCriteria = [],
        array $shouldCriteria = [],
        $offset = null,
        $limit = null,
        $sorting = [],
        $additionalOptions = []
    ) {
        $result = $this->doSearch(
            $this->client,
            $this->index,
            $this->type,
            $mustCriteria,
            $filterCriteria,
            $mustNotCriteria,
            $shouldCriteria,
            $offset,
            $limit,
            $sorting,
            $additionalOptions
        );

        if (!isset($result["hits"]["hits"])) {
            return new ResultsList([], 0);
        }

        $documents = [];

        // Fetch list of documents based on results from Elastic Search
        // TODO optimize to use list
        foreach ($result["hits"]["hits"] as $page) {
            $id = (int) $page["_id"];

            if (($document = Page::getById($id)) instanceof Page) {
                $documents[] = $document;
            }
        }

        return new ResultsList($documents, $result["hits"]["total"]);
    }

    /**
     * Convert a page to an array usable by Elasticsearch
     *
     * @param Page $document
     *
     * @return array
     */
    private function pageToArray(Page $document)
    {
        return [
            "id" => $document->getId(),
            "body" => [
                "page" => $this->processor->processPage($document)
            ],
            "index" => $this->index,
            "type" => $this->type,
            "timestamp" => $document->getModificationDate()
        ];
    }
}
