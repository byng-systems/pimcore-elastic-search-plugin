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

use Byng\Pimcore\Elasticsearch\Query\QueryInterface;

/**
 *
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
     * @var array
     */
    private $fields;

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
     * @return array|null
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array|null $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
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

    /**
     * Converts the query builder into an array
     *
     * @return array
     */
    public function toArray()
    {
        $body = [];

        if ($query = $this->getQuery()) {
            $body = $this->processQuery($query);
        }

        if ($filter = $this->getFilter()) {
            $body = array_merge($body, $this->processQuery($filter));
        }

        if ($from = $this->getFrom()) {
            $body["from"] = $from;
        }

        if ($size = $this->getSize()) {
            $body["size"] = $size;
        }

        if ($sort = $this->getSort()) {
            $body["sort"] = $this->processQuery($sort);
        }

        if (is_array($this->fields)) {
            $body["fields"] = $this->fields;
        }

        return $body;
    }

    /**
     * Process a query into an array usable by Elasticsearch
     *
     * @param QueryInterface $query
     *
     * @return array
     */
    protected function processQuery(QueryInterface $query)
    {
        switch ($query->getType()) {

            case "query":
                $result["query"] = $this->processQuery($query->getBoolQuery());
                break;

            case "filter":
                $result["filter"] = $this->processQuery($query->getQuery());
                break;

            case "bool":
                $boolResult = [];

                foreach ($query->getMust() as $must) {
                    $boolResult["must"][] = $this->processQuery($must);
                }

                foreach ($query->getShould() as $should) {
                    $boolResult["should"][] = $this->processQuery($should);
                }

                foreach ($query->getMustNot() as $mustNot) {
                    $boolResult["must_not"][] = $this->processQuery($mustNot);
                }

                if ($filter = $query->getFilter()) {
                    $boolResult = $this->processQuery($filter);
                }

                $result = [];
                $result["bool"] = $boolResult;

                break;

            case "match":
                $result = [];

                if ($operator = $query->getOperator()) {
                    $result["match"][$query->getField()] = [
                        "query" => $query->getQuery(),
                        "operator" => $operator
                    ];
                } else {
                    $result["match"][$query->getField()] = $query->getQuery();
                }
                break;

            case "range":
                $result = [];
                $result["range"][$query->getField()] = $query->getRanges();
                break;

            case "sort":
                $result = [];
                foreach ($query->getCriteria() as $sorting) {
                    $result[] = $sorting;
                }
                break;

            case "terms":
                $result["terms"] = [
                    $query->getField() => $query->getTerms()
                ];
                break;

            case "ids":
                $result["ids"] = [
                    "type" => $query->getIndexType(),
                    "values" => $query->getValues()
                ];
                break;

            case "prefix":
                $result["prefix"] = [
                    $query->getField() => $query->getQuery()
                ];
                break;

            case "regexp":
                $result["regexp"] = [
                    $query->getField() => $query->getQuery()
                ];
                break;

            case "wildcard":
                $result["wildcard"] = [
                    $query->getField() => $query->getQuery()
                ];
                break;

            case "constant_score":
                $result["constant_score"] = $this->processQuery($query->getFilter());
                break;

            case "nested":
                $result["nested"] = [
                    "path" => $query->getPath(),
                    "query" => $this->processQuery($query->getQuery())
                ];
                break;

            default:
                throw new \InvalidArgumentException(sprintf(
                    "Unknown query type '%s' given.",
                    $query->getType()
                ));
        }

        return $result;
    }

}
