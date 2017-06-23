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
 * Sort
 *
 * Encapsulates "sort" data.
 *
 * @author Asim Liaquat <asimlqt22@gmail.com>
 */
class Sort implements QueryInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Sort constructor.
     */
    public function __construct()
    {
    }

    /**
     * Add a sorting criteria. Can be called multiple times to sort by more than
     * one column.
     *
     * @param string $column
     * @param string $order
     * @param mixed  $unmappedType
     */
    public function addCriteria($column, $order, $unmappedType = false)
    {
        $sort = [
            "order" => $order
        ];

        if ($unmappedType) {
            $sort["unmapped_type"] = $unmappedType;
        }

        $this->data[] = [
            $column => $sort
        ];
    }

    /**
     * Get sort criteria
     *
     * @return array
     */
    public function getCriteria()
    {
        return $this->data;
    }

    /**
     * Resets the previously added criteria
     */
    public function clear()
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "sort";
    }
}
