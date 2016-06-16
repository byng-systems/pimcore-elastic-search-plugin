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
 * Range Query
 *
 * Allows searching for data which falls within a certain range. Useful for
 * dates etc.
 *
 * @author Asim Liaquat <asim@byng.co>
 */
final class RangeQuery implements Query
{
    const OPERATOR_GT = "gt";
    const OPERATOR_GTE = "gte";
    const OPERATOR_LT = "lt";
    const OPERATOR_LTE = "lte";

    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $ranges = [];

    /**
     * RangeQuery constructor.
     *
     * @param string $field
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * Add a new range
     * 
     * @param string     $operator
     * @param int|string $value
     */
    public function addRange($operator, $value)
    {
        switch ($operator) {
            case self::OPERATOR_GT:
            case self::OPERATOR_GTE:
            case self::OPERATOR_LT:
            case self::OPERATOR_LTE:
                break;
            default:
                throw new \InvalidArgumentException(sprintf(
                    "Unexpected operator found: '%s'",
                    $operator
                ));
        }
        
        $this->ranges[$operator] = $value;
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
     * Get ranges
     *
     * @return array
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "range";
    }
    
}
