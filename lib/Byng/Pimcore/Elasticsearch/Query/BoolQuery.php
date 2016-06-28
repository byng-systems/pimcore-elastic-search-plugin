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
 * @author Asim Liaquat <asimlqt22@gmail.com>
 */
final class BoolQuery implements QueryInterface
{
    /**
     * @var array
     */
    private $must = [];

    /**
     * @var array
     */
    private $should = [];

    /**
     * @var array
     */
    private $mustNot = [];

    /**
     * BoolQuery constructor.
     *
     * @param array $must
     * @param array $should
     * @param array $mustNot
     */
    public function __construct(
        array $must = [],
        array $should = [],
        array $mustNot = []
    ) {
        $this->must = $must;
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
     * Add a "must" clause
     * 
     * @param QueryInterface $must
     */
    public function addMust(QueryInterface $must)
    {
        $this->must[] = $must;
    }

    /**
     * Add a "should" clause
     * 
     * @param QueryInterface $should
     */
    public function addShould(QueryInterface $should)
    {
        $this->should[] = $should;
    }

    /**
     * Add a "must_not"
     * 
     * @param QueryInterface $mustNot
     */
    public function addMustNot(QueryInterface $mustNot)
    {
        $this->mustNot[] = $mustNot;
    }

    /**
     * Resets all data which has been added
     * 
     * @return null
     */
    public function clear()
    {
        $this->must = [];
        $this->mustNot = [];
        $this->should = [];
    }
    
    /**
     * Checkls whether any "must", "must_not" or "should" claues have been added.
     * 
     * @return bool
     */
    public function isEmpty()
    {
        return
            count($this->must) === 0 &&
            count($this->mustNot) === 0 &&
            count($this->should) === 0;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "bool";
    }
}
