<?php

namespace ElasticSearch\Repository;

use Elasticsearch\Client;
use ElasticSearch\Filter\FilterInterface;
use ElasticSearch\Filter\TagKeyFilter;
use ElasticSearch\Processor\Page\PageProcessorFactory;
use NF\HtmlToText as HtmlToTextFilter;
use Zend_Config_Xml;

/**
 * PageRepositoryFactory
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class PageRepositoryFactory
{
    /**
     * @var PageProcessorFactory
     */
    protected $processorFactory;


    /**
     * PageRepositoryFactory constructor.
     *
     * @param PageProcessorFactory|null $processorFactory
     */
    public function __construct(PageProcessorFactory $processorFactory = null)
    {
        $this->processorFactory = ($processorFactory ?: new PageProcessorFactory());
    }

    /**
     * Build the PageRepository.
     *
     * @param Zend_Config_Xml $configuration
     * @param FilterInterface|null $filter
     * @return PageRepository
     */
    public function build(Zend_Config_Xml $configuration, FilterInterface $filter = null)
    {
        $elasticSearchClient = new Client(array(
            "hosts" => $configuration->hosts->toArray()
        ));

        $elasticSearchRepository = new PageRepository(
            array(
                "index" => $configuration->index,
                "type" => $configuration->type
            ),
            $elasticSearchClient,
            new HtmlToTextFilter(),
            $this->processorFactory->build(($filter = $filter ?: new TagKeyFilter())),
            $filter
        );

        return $elasticSearchRepository;
    }
}
