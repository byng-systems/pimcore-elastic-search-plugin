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
use Byng\Pimcore\Elasticsearch\Filter\TagKeyFilter;
use Byng\Pimcore\Elasticsearch\Processor\Page\PageProcessorFactory;
use Elasticsearch\ClientBuilder;
use NF\HtmlToText as HtmlToTextFilter;
use Zend_Config;
use Pimcore;

/**
 * PageGateway Factory
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Asim Liaquat <asim@byng.co>
 */
final class PageGatewayFactory
{
    /**
     * @var PageProcessorFactory
     */
    private $processorFactory;


    /**
     * PageGatewayFactory constructor.
     *
     * @param PageProcessorFactory|null $processorFactory
     */
    public function __construct(PageProcessorFactory $processorFactory = null)
    {
        $this->processorFactory = ($processorFactory ?: new PageProcessorFactory());
    }

    /**
     * Build the PageGateway
     *
     * @param Zend_Config $hosts
     * @param Zend_Config $configuration
     * @param FilterInterface|null $filter
     *
     * @return PageGateway
     */
    public function build(
        Zend_Config $hosts,
        Zend_Config $configuration,
        FilterInterface $filter = null
    ) {
        $client = ClientBuilder::fromConfig([
            "hosts" => $hosts->toArray()
        ]);

        $gateway = new PageGateway(
            [
                "index" => $configuration->get("indexName"),
                "type" => $configuration->get("typeName")
            ],
            $client,
            new HtmlToTextFilter(),
            $this->processorFactory->build(($filter = $filter ?: new TagKeyFilter())),
            $filter,
            Pimcore::getEventManager()
        );

        return $gateway;
    }
}
