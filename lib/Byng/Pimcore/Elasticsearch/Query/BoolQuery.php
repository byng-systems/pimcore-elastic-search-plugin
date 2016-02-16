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
 * BoolQuery
 *
 * Encapsulates a "bool" query's data.
 *
 * @author Elliot Wright <elliot@elliotwright.co>
 */
final class BoolQuery implements Query
{
    /**
     * @var array
     */
    private $must;

    /**
     * @var array
     */
    private $filter;

    /**
     * @var array
     */
    private $should;

    /**
     * @var array
     */
    private $mustNot;


    /**
     * BoolQuery constructor.
     *
     * @param array $must
     * @param array $filter
     * @param array $should
     * @param array $mustNot
     */
    public function __construct(
        array $must = [],
        array $filter = [],
        array $should = [],
        array $mustNot = []
    ) {
        $this->must = $must;
        $this->filter = $filter;
        $this->should = $should;
        $this->mustNot = $mustNot;
    }

    /**
     * Get must
     *
     * @return array
     */
    public function getMust()
    {
        return $this->must;
    }

    /**
     * Get filter
     *
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Get should
     *
     * @return array
     */
    public function getShould()
    {
        return $this->should;
    }

    /**
     * Get mustNot
     *
     * @return array
     */
    public function getMustNot()
    {
        return $this->mustNot;
    }

    /**
     * Add a must clause
     *
     * @param Query $must
     *
     * @return BoolQuery
     */
    public function withMust(Query $must)
    {
        return new BoolQuery(
            array_merge($this->must, $must),
            $this->filter,
            $this->should,
            $this->mustNot
        );
    }

    /**
     * Add a filter clause
     *
     * @param Query $filter
     *
     * @return BoolQuery
     */
    public function withFilter(Query $filter)
    {
        return new BoolQuery(
            $this->must,
            array_merge($this->filter, [ $filter ]),
            $this->should,
            $this->mustNot
        );
    }

    /**
     * Add a should clause
     *
     * @param Query $should
     *
     * @return BoolQuery
     */
    public function withShould(Query $should)
    {
        return new BoolQuery(
            $this->must,
            $this->filter,
            array_merge($this->should, [ $should ]),
            $this->mustNot
        );
    }

    /**
     * Add a must_not clause
     *
     * @param Query $mustNot
     *
     * @return BoolQuery
     */
    public function withMustNot(Query $mustNot)
    {
        return new BoolQuery(
            $this->must,
            $this->filter,
            $this->should,
            array_merge($this->mustNot, [ $mustNot ])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "bool";
    }
}
