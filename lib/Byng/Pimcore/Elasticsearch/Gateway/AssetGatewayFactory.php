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

use Byng\Pimcore\Elasticsearch\Processor\Asset\AssetProcessor;
use Elasticsearch\ClientBuilder;
use Zend_Config;
use Pimcore;

/**
 * AssetGateway Factory
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Asim Liaquat <asim@byng.co>
 */
final class AssetGatewayFactory
{
    /**
     * Build the AssetGateway
     *
     * @param Zend_Config $hosts
     * @param Zend_Config $configuration
     *
     * @return AssetGateway
     */
    public function build(Zend_Config $hosts, Zend_Config $configuration)
    {
        $client = ClientBuilder::fromConfig([
            "hosts" => $hosts->toArray()
        ]);

        $gateway = new AssetGateway(
            [
                "index" => $configuration->get("indexName"),
                "type" => $configuration->get("typeName")
            ],
            $client,
            new AssetProcessor(),
            Pimcore::getEventManager()
        );

        return $gateway;
    }
}
