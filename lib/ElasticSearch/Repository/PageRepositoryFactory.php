<?php
/**
 *
 * @author      Michal Maszkiewicz
 * @package     Elastic Search Plugin
 */

namespace ElasticSearch\Repository;

use Elasticsearch\Client;
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
     * @return PageRepository
     */
    public function build(Zend_Config_Xml $configuration)
    {
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
            $this->processorFactory->build()
        );

        return $elasticSearchRepository;
    }
}
