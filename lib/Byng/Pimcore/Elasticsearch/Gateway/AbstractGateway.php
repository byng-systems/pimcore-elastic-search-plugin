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
use Byng\Pimcore\Elasticsearch\Query\BoolQuery;
use Byng\Pimcore\Elasticsearch\Query\MatchQuery;
use Byng\Pimcore\Elasticsearch\Query\Query;
use Elasticsearch\Client;

/**
 * AbstractGateway
 *
 * @author Elliot Wright <elliot@elliotwright.co>
 */
abstract class AbstractGateway
{
    /**
     * Finds documents by text and term filters
     *
     * @param string       $text
     * @param array        $filters
     * @param array        $negationFilters
     * @param integer|null $offset
     * @param integer|null $limit
     * @param array        $sorting
     * @param array        $additionalOptions
     * @param array        $resultOptions
     *
     * @return ResultsList
     */
    public function query(
        $text,
        array $filters = [],
        array $negationFilters = [],
        $offset = null,
        $limit = null,
        array $sorting = [],
        array $additionalOptions = [],
        array $resultOptions = []
    ) {
        $mustCriteria = [];
        $filterCriteria = [];
        $mustNotCriteria = [];

        if (!empty($text)) {
            $mustCriteria[]["match"]["_all"] = [
                "query" => (string) $text,
                "operator" => MatchQuery::OPERATOR_AND
            ];
        }

        foreach ($filters as $filter) {
            $filterCriteria[] = $this->processQuery($filter);
        }

        foreach ($negationFilters as $filter) {
            /** @var MatchQuery $filter */
            $mustNotCriteria[]["match"][$filter->getField()] = [
                "query" => $filter->getQuery(),
                "operator" => $filter->getOperator()
            ];
        }

        return $this->findBy(
            $mustCriteria,
            $filterCriteria,
            $mustNotCriteria,
            [],
            $offset,
            $limit,
            $sorting,
            $additionalOptions,
            $resultOptions
        );
    }

    /**
     * Executes an Elasticsearch "bool" query
     *
     * @param array $mustCriteria
     * @param array $filterCriteria
     * @param array $mustNotCriteria
     * @param array $shouldCriteria
     * @param null  $offset
     * @param null  $limit
     * @param array $sorting
     * @param array $additionalOptions
     * @param array $resultOptions
     *
     * @return mixed
     */
    abstract public function findBy(
        array $mustCriteria = [],
        array $filterCriteria = [],
        array $mustNotCriteria = [],
        array $shouldCriteria = [],
        $offset = null,
        $limit = null,
        array $sorting = [],
        array $additionalOptions = [],
        array $resultOptions = []
    );

    /**
     * Perform a search using Elasticsearch
     *
     * @param Client       $client
     * @param string       $index
     * @param string       $type
     * @param array        $mustCriteria
     * @param array        $filterCriteria
     * @param array        $mustNotCriteria
     * @param array        $shouldCriteria
     * @param null|integer $offset
     * @param null|integer $limit
     * @param array        $sorting
     * @param array        $additionalOptions
     *
     * @return array
     */
    protected function doSearch(
        Client $client,
        $index,
        $type,
        array $mustCriteria = [],
        array $filterCriteria = [],
        array $mustNotCriteria = [],
        array $shouldCriteria = [],
        $offset = null,
        $limit = null,
        $sorting = [],
        $additionalOptions = []
    ) {
        $query = [
            "query" => [
                "bool" => [
                    "must" => $mustCriteria,
                    "filter" => $filterCriteria,
                    "must_not" => $mustNotCriteria,
                    "should" => $shouldCriteria
                ]
            ],
        ];

        $body = $additionalOptions + $query;

        if ($offset) {
            $body["from"] = $offset;
        }

        if ($limit) {
            $body["size"] = $limit;
        }

        if (!empty($sorting)) {
            $body["sort"] = $sorting;
        }

        return $client->search([
            "index" => $index,
            "type" => $type,
            "body" => $body
        ]);
    }

    /**
     * Process a query into an array usable by Elasticsearch
     *
     * @param Query $query
     *
     * @return array
     */
    protected function processQuery(Query $query)
    {
        switch ($query->getType()) {
            case "bool":
                /** @var BoolQuery $query */
                $boolResult = [];

                foreach ($query->getMust() as $must) {
                    $boolResult["must"][] = $this->processQuery($must);
                }

                foreach ($query->getFilter() as $filter) {
                    $boolResult["filter"][] = $this->processQuery($filter);
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
                /** @var MatchQuery $query */
                $result = [];
                $result["match"][$query->getField()] = [
                    "query" => $query->getQuery(),
                    "operator" => $query->getOperator()
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
