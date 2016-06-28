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
 * Filter
 *
 * Encapsulates "filter" data.
 *
 * @author Asim Liaquat <asimlqt22@gmail.com>
 */
class Filter implements QueryInterface
{
    /**
     * @var BoolQuery
     */
    private $boolQuery;

    /**
     * Filter constructor.
     *
     * @param BoolQuery $boolQuery
     */
    public function __construct(BoolQuery $boolQuery = null)
    {
        $this->boolQuery = $boolQuery;
    }

    /**
     * Get bool query
     *
     * @return BoolQuery
     */
    public function getBoolQuery()
    {
        return $this->boolQuery;
    }

    /**
     * Set bool query
     * 
     * @param BoolQuery $boolQuery
     */
    public function setBoolQuery(BoolQuery $boolQuery)
    {
        $this->boolQuery = $boolQuery;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "filter";
    }
}
