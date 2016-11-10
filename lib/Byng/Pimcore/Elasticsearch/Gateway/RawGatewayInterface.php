<?php

namespace Byng\Pimcore\Elasticsearch\Gateway;

use Byng\Pimcore\Elasticsearch\Model\ResultsList;
use Byng\Pimcore\Elasticsearch\Query\QueryBuilder;

/**
 * Raw Gateway Interface
 * @author Max Baldanza <mbaldanza@inviqa.com>
 */
interface RawGatewayInterface
{
    /**
     * @param string       $index
     * @param string       $type
     * @param QueryBuilder $query
     *
     * @return ResultsList
     */
    public function findByQueryBuilder($index, $type, QueryBuilder $query);

    /**
     * @param string $index
     * @param string $type
     * @param array  $query
     *
     * @return ResultsList
     */
    public function findByArray($index, $type, array $query);

    /**
     * @param array $data
     */
    public function save(array $data);

    /**
     * @param string $index
     * @param string $type
     * @param int    $id
     */
    public function get($index, $type, $id);
    
    /**
     * @param string $index
     * @param string $type
     * @param int $id
     */
    public function delete($index, $type, $id);
}
