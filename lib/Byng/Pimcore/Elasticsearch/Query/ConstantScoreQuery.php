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
 * ConstantScore Query
 *
 * Encapsulates a "constant_score" query's data.
 *
 * @author Asim Liaquat <asim@byng.co>
 */
final class ConstantScoreQuery implements QueryInterface
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * ConstantScoreQuery constructor.
     *
     * @param Filter $filter
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
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
     * {@inheritdoc}
     */
    public function getType()
    {
        return "constant_score";
    }
}
