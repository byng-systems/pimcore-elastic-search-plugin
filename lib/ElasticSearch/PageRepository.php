<?php
/**
 *
 * @author      Michal Maszkiewicz
 * @package     
 */

namespace ElasticSearch;

use Document_Page;
use Document_Tag;
use Elasticsearch\Client;
use InvalidArgumentException;
use NF\HtmlToText;

class PageRepository
{
    /** @var string */
    protected $index;

    /** @var string */
    protected $type;

    /** @var Client */
    protected $client;

    /** @var HtmlToText  */
    protected $htmlToTextFilter;

    /**
     * @param $configuration
     * @param Client $client
     * @param $htmlToTextFilter
     */
    public function __construct(array $configuration, Client $client, HtmlToText $htmlToTextFilter)
    {
        if (! isset($configuration['index'])) {
            throw new InvalidArgumentException('Missing configuration setting: index');
        }

        if (! isset($configuration['type'])) {
            throw new InvalidArgumentException('Missing configuration setting: type');
        }

        $this->index = (string) $configuration['index'];
        $this->type = (string) $configuration['type'];
        $this->client = $client;
        $this->htmlToTextFilter = $htmlToTextFilter;
    }

    /**
     * @param Document_Page $document
     * @return array
     */
    public function delete(Document_Page $document)
    {
        $params = array(
            'id' => $document->getId(),
            'index' => $this->index,
            'type' => $this->type
        );

        if (! $this->exists($document)) {

            return false;

        }

        return $this->client->delete($params);
    }

    /**
     * @param Document_Page $document
     */
    public function save(Document_Page $document)
    {
        $params = $this->pageToArray($document);

        if ($this->exists($document)) {
            $this->client->update($params);
        } else {
            $this->client->create($params);
        }
    }

    /**
     * @param Document_Page $document
     * @return array
     */
    public function exists(Document_Page $document)
    {
        $params = array(
            'id' => $document->getId(),
            'index' => $this->index,
            'type' => $this->type
        );

        return $this->client->exists($params);
    }

    /**
     * Finds documents by text
     *
     * @param string $text
     * @param array $filters
     * @return array
     */
    public function find($text, array $filters = [])
    {
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'body' => ['query' => ['match' => []]]
        ];
        
        $params['body']['query']['match']['_all'] = [
            'query'     =>  (string) $text
        ];

        $result = $this->client->search($params);

        $documents = array();

        if (! isset($result['hits']['hits'])) {
            return array();
        }

        // Fetch list of documents based on results from Elastic Search
        // TODO optimize to use list
        foreach ($result['hits']['hits'] as $page) {
            $id = (int) $page['_id'];
            $documents[] = Document_Page::getById($id);
        }

        return $documents;
    }

    /**
     * @param Document_Page $document
     * @return array
     */
    protected function pageToArray(Document_Page $document)
    {
        $body = array();

        /** @var Document_Tag $element */
        foreach ($document->getElements() as $key => $element) {

            if (
                // Skip processing an empty snippet.
                ! $element->getData() ||
                // Image tag would return an array of properties
                ! is_string($element->getData())
            ) {
                continue;
            }

            $body['doc'][$key] = $this->htmlToTextFilter->convert($element->getData());

        }

        return array(
            'id' => $document->getId(),
            'body' => $body,
            'index' => $this->index,
            'type' => $this->type,
            'timestamp' => $document->getModificationDate()
        );
    }
}
