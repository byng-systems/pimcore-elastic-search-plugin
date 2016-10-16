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

use Byng\Pimcore\Elasticsearch\Model\ResultsList;
use Elasticsearch\Client;
use Zend_EventManager_EventManager as ZendEventManager;
use Byng\Pimcore\Elasticsearch\Query\QueryBuilder;

/**
 * Raw Gateway
 *
 * @author Asim Liaquat <asim@byng.co>
 * @author Max Baldanza <mbaldanza@inviqa.com>
 */
final class RawGateway extends AbstractGateway implements RawGatewayInterface
{
    /**
     * @var RawGateway
     */
    private static $instance;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var ZendEventManager
     */
    private $pimcoreEventManager;

    /**
     * Constructor
     *
     * @param Client           $client
     * @param ZendEventManager $pimcoreEventManager
     */
    public function __construct(
        Client $client,
        ZendEventManager $pimcoreEventManager
    ) {
        $this->client = $client;
        $this->pimcoreEventManager = $pimcoreEventManager;

        static::$instance = $this;
    }

    /**
     * Get an instance of this gateway after plugin initialisation
     *
     * @return RawGateway
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            throw new \RuntimeException("No instance of RawGateway available, did the Elasticsearch plugin initialise correctly?");
        }

        return static::$instance;
    }

    /**
     * Save the given data
     *
     * @param array $data
     *
     * @return void
     */
    public function save(array $data)
    {
        $this->client->index($data);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(
        array $query,
        array $additionalOptions = [],
        array $resultOptions = []
    ) {
        throw new \Exception("Not implemented");
    }

    /**
     * Perform a search on the given index and type.
     * 
     * @param string $index
     * @param string $type
     * @param array  $query
     * 
     * @return ResultsList
     */
    public function findByArray($index, $type, array $query)
    {
        return $this->doFind($index, $type, $query);
    }
    
    /**
     * Perform a search on the given index and type.
     * 
     * @param string       $index
     * @param string       $type
     * @param QueryBuilder $query
     * 
     * @return ResultsList
     */
    public function findByQueryBuilder($index, $type, QueryBuilder $query)
    {
        return $this->doFind($index, $type, $query->toArray());
    }
    
    /**
     * Perform a search on the given index and type.
     * 
     * @param string $index
     * @param string $type
     * @param array  $query
     * 
     * @return ResultsList
     */
    private function doFind($index, $type, array $query)
    {
        $result = $this->doSearch(
            $this->client,
            $index,
            $type,
            $query
        );

        return new ResultsList($result, $result["hits"]["total"]);
    }
    
}
