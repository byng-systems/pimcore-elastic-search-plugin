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
 * QueryBuilder defines the query to send to elasticsearch.
 *
 * @author Asim Liaquat <asimlqt22@gmail.com>
 */
class QueryBuilder
{
    /**
     * @var Query
     */
    private $query;
    
    /**
     * @var Filter
     */
    private $filter;
    
    /**
     * @var int
     */
    private $size = 10;

    /**
     * @var int
     */
    private $from;
    
    /**
     * @var Sort
     */
    private $sort;
    
    /**
     * QueryBuilder constructor
     * 
     * @param Query  $query
     * @param Filter $filter
     */
    public function __construct(Query $query = null, Filter $filter = null)
    {
        $this->query = $query;
        $this->filter = $filter;
    }

    /**
     * Get query
     * 
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get filter
     * 
     * @return Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set query
     * 
     * @param Query $query
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Set filter
     * 
     * @param Filter $filter
     */
    public function setFilter(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Get the number or results to return
     * 
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the number of results to return
     * 
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Get the offset to fetch the results from
     * 
     * @return int|null
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set the offset to fetch the results from
     * 
     * @param int $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * Get sort criteria
     * 
     * @return Sort
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set sort criteria
     * 
     * @param Sort $sort
     */
    public function setSort(Sort $sort)
    {
        $this->sort = $sort;
    }

}
