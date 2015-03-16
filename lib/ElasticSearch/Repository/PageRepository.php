<?php

/**
 *
 * @author      Michal Maszkiewicz
 * @package     
 */

namespace ElasticSearch\Repository;

use Document_Page;
use Elasticsearch\Client;
use ElasticSearch\Processor\Page\PageProcessor;
use InvalidArgumentException;
use NF\HtmlToText;



class PageRepository
{
    /**
     * 
     * @var string
     */
    protected $index;

    /**
     * 
     * @var string
     */
    protected $type;

    /**
     * 
     * @var Client
     */
    protected $client;

    /**
     * 
     * @var HtmlToText
     */
    protected $htmlToTextFilter;
    
    /**
     *
     * @var PageProcessor 
     */
    protected $processor;
    
    

    /**
     * @param $configuration
     * @param Client $client
     * @param $htmlToTextFilter
     * @param PageProcessor $processor
     */
    public function __construct(
        array $configuration,
        Client $client,
        HtmlToText $htmlToTextFilter,
        PageProcessor $processor
    ) {
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
        $this->processor = $processor;
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
     * 
     * @param array $mustCriteria
     * @param array $shouldCriteria
     * @param array $mustNotCriteria
     * @return Document_Page[]
     */
    public function findBy(
        array $mustCriteria = [],
        array $shouldCriteria = [],
        array $mustNotCriteria = []
    ) {
        $result = $this->client->search([
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $mustCriteria,
                        'should' => $shouldCriteria,
                        'must_not' => $mustNotCriteria
                    ]
                ]
            ]
        ]);

        $documents = [];

        if (!isset($result['hits']['hits'])) {
            return [];
        }

        // Fetch list of documents based on results from Elastic Search
        // TODO optimize to use list
        foreach ($result['hits']['hits'] as $page) {
            $id = (int) $page['_id'];
            
            if (($document = Document_Page::getById($id)) instanceof Document_Page) {
                $documents[] = $document;
            }
        }

        return $documents;
    }
    
    /**
     * Finds documents by text
     *
     * @param string $text
     * @param array $filters
     * @param array $terms
     * @return Document_Page[]
     */
    public function find($text, array $filters = [], array $terms = [])
    {
        $mustCriteria = [];
        
        if (!empty($text)) {
            $mustCriteria[]['match']['_all'] = ['query' => (string) $text];
        }
        
        foreach ($filters as $name => $term) {
            $mustCriteria[]['match'][$name] = ['query' => (string) $term];
        }
        
        foreach ($terms as $name => $term) {
            $mustCriteria[]['terms'] = [
                $name => [str_replace(' ', '_', strtolower($term))],
                'minimum_should_match' => 1
            ];
        }
        
        return $this->findBy($mustCriteria);
    }

    /**
     * @param Document_Page $document
     * @return array
     */
    protected function pageToArray(Document_Page $document)
    {
        return [
            'id' => $document->getId(),
            'body' => ['doc' => $this->processor->processPage($document)],
            'index' => $this->index,
            'type' => $this->type,
            'timestamp' => $document->getModificationDate()
        ];
    }
    
}
