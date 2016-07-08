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
 * Terms Query
 *
 * Encapsulates a "terms" query's data.
 *
 * @author Asim Liaquat <asim@byng.co>
 */
final class TermsQuery implements QueryInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $terms;

    /**
     * TermsQuery constructor.
     *
     * @param string $field
     * @param array  $terms
     */
    public function __construct($field, array $terms)
    {
        $this->field = $field;
        $this->terms = $terms;
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
     * @return array
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "terms";
    }
}
