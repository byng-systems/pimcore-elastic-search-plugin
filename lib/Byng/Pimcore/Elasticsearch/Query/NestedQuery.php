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

namespace Byng\Pimcore\Elasticsearch\Query;

/**
 * Nested Query
 *
 * Encapsulates a "nested" query's data.
 *
 * @author Asim Liaquat <asim@byng.co>
 */
final class NestedQuery implements QueryInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var Query
     */
    private $query;

    /**
     * NestedQuery constructor.
     *
     * @param string $path
     * @param array  $query
     */
    public function __construct($path, Query $query)
    {
        $this->path = $path;
        $this->query = $query;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get query
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "nested";
    }
}
