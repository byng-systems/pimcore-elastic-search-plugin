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
 * Match Query
 *
 * Encapsulates a "match" query's data.
 *
 * @author Elliot Wright <elliot@elliotwright.co>
 * @author Asim Liaquat <asimlqt22@gmail.com>
 */
final class MatchQuery implements QueryInterface
{
    const OPERATOR_AND = "and";
    const OPERATOR_OR = "or";

    /**
     * @var string
     */
    private $field;

    /**
     * @var string|array
     */
    private $query;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var int
     */
    private $boost;

    /**
     * MatchQuery constructor.
     *
     * @param string       $field
     * @param string|array $query
     * @param string       $operator
     * @param int          $boost
     */
    public function __construct($field, $query, $operator = null, $boost = null)
    {
        $this->field = $field;
        $this->query = $query;
        $this->operator = $operator;
        $this->boost = $boost;

        if ($operator !== null) {
            switch ($this->operator) {
                case self::OPERATOR_AND:
                case self::OPERATOR_OR:
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf(
                        "Unexpected operator found: '%s'",
                        $this->operator
                    ));
            }
        }
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
     * @return array|string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Get boost
     *
      @return int
     */
    public function getBoost()
    {
        return $this->boost;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "match";
    }
}
