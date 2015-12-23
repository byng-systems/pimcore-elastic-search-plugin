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

namespace Byng\Pimcore\Elasticsearch\Repository;

use Byng\Pimcore\Elasticsearch\Filter\FilterInterface;
use Byng\Pimcore\Elasticsearch\Filter\TagKeyFilter;
use Byng\Pimcore\Elasticsearch\Processor\Page\PageProcessorFactory;
use Elasticsearch\Client;
use NF\HtmlToText as HtmlToTextFilter;
use Zend_Config_Xml;

/**
 * PageRepository Factory
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class PageRepositoryFactory
{
    /**
     * @var PageProcessorFactory
     */
    private $processorFactory;


    /**
     * Constructor
     *
     * @param PageProcessorFactory|null $processorFactory
     */
    public function __construct(PageProcessorFactory $processorFactory = null)
    {
        $this->processorFactory = ($processorFactory ?: new PageProcessorFactory());
    }

    /**
     * Build the PageRepository
     *
     * @param Zend_Config_Xml $configuration
     * @param FilterInterface|null $filter
     *
     * @return PageRepository
     */
    public function build(
        Zend_Config_Xml $configuration,
        FilterInterface $filter = null
    ) {
        $elasticSearchClient = new Client([
            "hosts" => $configuration->hosts->toArray()
        ]);

        $elasticSearchRepository = new PageRepository(
            [
                "index" => $configuration->index,
                "type" => $configuration->type
            ],
            $elasticSearchClient,
            new HtmlToTextFilter(),
            $this->processorFactory->build(($filter = $filter ?: new TagKeyFilter())),
            $filter
        );

        return $elasticSearchRepository;
    }
}
