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

namespace Byng\Pimcore\Elasticsearch\Gateway;

use Byng\Pimcore\Elasticsearch\Model\ResultsList;
use Byng\Pimcore\Elasticsearch\Query\Query;
use Byng\Pimcore\Elasticsearch\Query\QueryBuilder;
use Byng\Pimcore\Elasticsearch\Query\QueryInterface;
use Elasticsearch\Client;

/**
 * AbstractGateway
 *
 * @author Elliot Wright <elliot@elliotwright.co>
 * @author Asim Liaquat <asimlqt22@gmail.com>
 */
abstract class AbstractGateway
{
    /**
     * Finds documents by text and term filters
     *
     * @param QueryBuilder $queryBuilder
     * @param array        $additionalOptions
     * @param array        $resultOptions
     *
     * @return ResultsList
     */
    public function query(
        QueryBuilder $queryBuilder,
        array $additionalOptions = [],
        array $resultOptions = []
    ) {
        $body = [];

        if ($query = $queryBuilder->getQuery()) {
            $body = $this->processQuery($query);
        }

        if ($filter = $queryBuilder->getFilter()) {
            $body = array_merge($body, $this->processQuery($filter));
        }

        if ($from = $queryBuilder->getFrom()) {
            $body["from"] = $from;
        }

        if ($size = $queryBuilder->getSize()) {
            $body["size"] = $size;
        }

        if ($sort = $queryBuilder->getSort()) {
            $body["sort"] = $this->processQuery($sort);
        }

        return $this->findBy(
            $body,
            $additionalOptions,
            $resultOptions
        );
    }

    /**
     * Executes an Elasticsearch "bool" query
     *
     * @param array $query
     * @param array $additionalOptions
     * @param array $resultOptions
     *
     * @return mixed
     */
    abstract public function findBy(
        array $query,
        array $additionalOptions = [],
        array $resultOptions = []
    );

    /**
     * Perform a search using Elasticsearch
     *
     * @param Client       $client
     * @param string       $index
     * @param string       $type
     * @param array        $query
     * @param array        $additionalOptions
     *
     * @return array
     */
    protected function doSearch(
        Client $client,
        $index,
        $type,
        $query,
        $additionalOptions = []
    ) {
        $body = $additionalOptions + $query;

        return $client->search([
            "index" => $index,
            "type" => $type,
            "body" => $body
        ]);
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
                } else if ($boost = $query->getBoost()) {
                    $result["match"][$query->getField()] = [
                        "query" => $query->getQuery(),
                        "boost" => $boost
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
