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
 * Prefix Query
 *
 * Allows to do prefix queries
 *
 * @author Asim Liaquat <asimlqt22@gmail.com>
 */
final class Prefix implements QueryInterface
{

    /**
     * @var string
     */
    private $field;
    
    /**
     * @var string
     */
    private $query;

    /**
     * Prefix constructor.
     *
     * @param string $field
     * @param string $query
     */
    public function __construct($field, $query)
    {
        $this->field = $field;
        $this->query = $query;
    }
    
    /**
     * Get field
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get query
     * 
     * @return string
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
        return "prefix";
    }
    
}
