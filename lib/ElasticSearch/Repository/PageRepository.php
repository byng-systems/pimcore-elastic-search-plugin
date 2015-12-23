<?php

namespace ElasticSearch\Repository;

use Elasticsearch\Client;
use ElasticSearch\Filter\FilterInterface;
use ElasticSearch\Model\ResultsList;
use ElasticSearch\Processor\Page\PageProcessor;
use InvalidArgumentException;
use NF\HtmlToText;
use Pimcore\Model\Document\Page;
use UnexpectedValueException;

/**
 * Page Repository
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Michal Maszkiewicz
 */
final class PageRepository
{
    const MATCH_QUERY_OPERATOR_AND = "and";
    const MATCH_QUERY_OPERATOR_OR = "or";

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
     * Constructor
     *
     * @param array $configuration
     * @param Client $client
     * @param HtmlToText $htmlToTextFilter
     * @param PageProcessor $processor
     * @param FilterInterface $inputFilter
     */
    public function __construct(
        array $configuration,
        Client $client,
        HtmlToText $htmlToTextFilter,
        PageProcessor $processor,
        FilterInterface $inputFilter
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
        $this->client->index($this->pageToArray($document));
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
     * Executes an ElasticSearch "bool" query
     *
     * @param array $mustCriteria
     * @param array $shouldCriteria
     * @param array $mustNotCriteria
     * @param integer|null $offset
     * @param integer|null $limit
     * @param array $sorting
     * @param array $additionalOptions
     *
     * @return ResultsList
     */
    public function findBy(
        array $mustCriteria = [],
        array $shouldCriteria = [],
        array $mustNotCriteria = [],
        $offset = null,
        $limit = null,
        $sorting = [],
        $additionalOptions = []
    ) {
        $body = $additionalOptions + [
            "query" => [
                "bool" => [
                    "must" => $mustCriteria,
                    "should" => $shouldCriteria,
                    "must_not" => $mustNotCriteria
                ]
            ],
        ];

        foreach ([ "offset", "limit" ] as $constraint) {
            $constraintValue = $$constraint;

            if ($constraintValue !== null) {
                $body[$constraint] = $constraintValue;
            }
        }

        if (!empty($sorting)) {
            $body["sort"] = $sorting;
        }

        $result = $this->client->search([
            "index" => $this->index,
            "type" => $this->type,
            "body" => $body
        ]);

        $documents = [];

        if (!isset($result["hits"]["hits"])) {
            return [];
        }

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
     * Finds documents by text and term filters
     *
     * @param string $text
     * @param array $filters
     * @param array $negationFilters
     * @param integer|null $offset
     * @param integer|null $limit
     * @param array $sorting
     * @param array $additionalOptions
     * @param string $matchOperator
     *
     * @return ResultsList
     *
     * @throws UnexpectedValueException
     */
    public function query(
        $text,
        array $filters = [],
        array $negationFilters = [],
        $offset = null,
        $limit = null,
        $sorting = [],
        $additionalOptions = [],
        $matchOperator = self::MATCH_QUERY_OPERATOR_AND
    ) {
        $mustCriteria = [];
        $mustNotCriteria = [];

        if (!empty($text)) {
            switch ($matchOperator) {
                case self::MATCH_QUERY_OPERATOR_AND:
                case self::MATCH_QUERY_OPERATOR_OR:
                    break;
                default:
                    throw new UnexpectedValueException(
                        "Invalid query operator specified; expected one of: "and", "or""
                    );
            }

            $mustCriteria[]["match"]["_all"] = [
                "query" => (string) $text,
                "operator" => $matchOperator
            ];
        }

        foreach ($filters as $name => $term) {
            $mustCriteria[]["terms"] = [
                $name => (is_array($term) ? $term : [$this->inputFilter->filter($term)]),
                "minimum_should_match" => 1
            ];
        }

        foreach ($negationFilters as $name => $term) {
            $mustNotCriteria[]["terms"] = [
                $name => (is_array($term) ? $term : [$this->inputFilter->filter($term)]),
                "minimum_should_match" => 1
            ];
        }

        return $this->findBy(
            $mustCriteria,
            [],
            $mustNotCriteria,
            $offset,
            $limit,
            $sorting,
            $additionalOptions
        );
    }

    /**
     * Convert a page to an array usable by ElasticSearch
     *
     * @param Page $document
     *
     * @return array
     */
    private function pageToArray(Page $document)
    {
        return [
            "id" => $document->getId(),
            "body" => [ "doc" => $this->processor->processPage($document) ],
            "index" => $this->index,
            "type" => $this->type,
            "timestamp" => $document->getModificationDate()
        ];
    }
}
