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

use Elasticsearch\ClientBuilder;

/**
 * RawGateway Factory
 *
 * @author Asim Liaquat <asim@byng.co>
 */
final class RawGatewayFactory
{
    /**
     * Build the RawGateway
     *
     * @param Zend_Config $hosts
     *
     * @return PageGateway
     */
    public function build(\Zend_Config $hosts)
    {
        $client = ClientBuilder::fromConfig([
            "hosts" => $hosts->toArray()
        ]);

        return new RawGateway($client, \Pimcore::getEventManager());
    }
}
