<?php
/**
 *
 * @author      Michal Maszkiewicz
 * @package     Elastic Search Plugin
 */

namespace ElasticSearch;

use Zend_Config_Xml;
use Elasticsearch\Client as ElasticSearchClient;
use NF\HtmlToText as HtmlToTextFilter;

class PageRepositoryFactory
{
    /**
     * @param Zend_Config_Xml $configuration
     * @return PageRepository
     */
    public function build(Zend_Config_Xml $configuration)
    {
        $elasticSearchClient = new ElasticSearchClient(array(
            'hosts' => $configuration->hosts->toArray()
        ));

        $elasticSearchIndex = new PageRepository(
            array(
                'index' => $configuration->index,
                'type' => $configuration->type
            ),
            $elasticSearchClient,
            new HtmlToTextFilter()
        );

        return $elasticSearchIndex;
    }
}
