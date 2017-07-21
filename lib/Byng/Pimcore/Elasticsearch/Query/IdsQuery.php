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
 * Ids Query
 *
 * Allows you to search only within documents that have the specified ids
 *
 * @author Asim Liaquat <asimlqt22@gmail.com>
 */
final class IdsQuery implements QueryInterface
{
    /**
     * @var string
     */
    private $indexType;

    /**
     * @var array
     */
    private $values;

    /**
     * IdsQuery constructor.
     *
     * @param string $indexType
     * @param array  $values
     */
    public function __construct($indexType, array $values)
    {
        $this->indexType = $indexType;
        $this->values = $values;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getIndexType()
    {
        return $this->indexType;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "ids";
    }

}
