<?php
/**
 *
 * @author      Michal Maszkiewicz
 * @package     Elastic Search Plugin
 */

namespace ElasticSearch\Repository;

use Elasticsearch\Client;
use ElasticSearch\Filter\FilterInterface;
use ElasticSearch\Filter\TagKeyFilter;
use ElasticSearch\Processor\Page\PageProcessorFactory;
use NF\HtmlToText as HtmlToTextFilter;
use Zend_Config_Xml;

class PageRepositoryFactory
{
    
    /**
     *
     * @var PageProcessorFactory
     */
    protected $processorFactory;
    
    
    /**
     * 
     * @param PageProcessorFactory|null $processorFactory
     */
    public function __construct(PageProcessorFactory $processorFactory = null)
    {
        $this->processorFactory = ($processorFactory ?: new PageProcessorFactory());
    }
    
    /**
     * @param Zend_Config_Xml $configuration
     * @param FilterInterface|null $filter
     * @return PageRepository
     */
    public function build(
        Zend_Config_Xml $configuration,
        FilterInterface $filter = null
    ) {
        $elasticSearchClient = new Client(array(
            'hosts' => $configuration->hosts->toArray()
        ));

        $elasticSearchRepository = new PageRepository(
            array(
                'index' => $configuration->index,
                'type' => $configuration->type
            ),
            $elasticSearchClient,
            new HtmlToTextFilter(),
            $this->processorFactory->build(($filter = $filter ?: new TagKeyFilter())),
            $filter
        );

        return $elasticSearchRepository;
    }
}
