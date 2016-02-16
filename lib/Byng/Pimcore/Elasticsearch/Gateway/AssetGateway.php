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
use Byng\Pimcore\Elasticsearch\Processor\Asset\AssetProcessor;
use Elasticsearch\Client;
use InvalidArgumentException;
use Pimcore\Model\Asset;

/**
 * Asset Gateway
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Michal Maszkiewicz
 */
final class AssetGateway extends AbstractGateway
{
    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $type;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var AssetProcessor
     */
    private $processor;


    /**
     * Constructor
     *
     * @param array          $configuration
     * @param Client         $client
     * @param AssetProcessor $assetProcessor
     */
    public function __construct(
        array $configuration,
        Client $client,
        AssetProcessor $assetProcessor
    ) {
        if (!isset($configuration["index"])) {
            throw new InvalidArgumentException("Missing configuration setting: index");
        }

        if (!isset($configuration["type"])) {
            throw new InvalidArgumentException("Missing configuration setting: type");
        }

        $this->index = (string) $configuration["index"];
        $this->type = (string) $configuration["type"];
        $this->client = $client;
        $this->processor = $assetProcessor;
    }

    /**
     * Delete a given asset
     *
     * @param Asset $asset
     *
     * @return array|bool
     */
    public function delete(Asset $asset)
    {
        $params = [
            "id" => $asset->getId(),
            "index" => $this->index,
            "type" => $this->type
        ];

        if (!$this->exists($asset)) {
            return false;
        }

        return $this->client->delete($params);
    }

    /**
     * Clears all entries from this index
     *
     * @return array
     */
    public function clear()
    {
        $this->client->indices()->deleteMapping([
            "index" => $this->index,
            "type" => $this->type
        ]);
    }

    /**
     * Save a given asset
     *
     * @param Asset $asset
     *
     * @return void
     */
    public function save(Asset $asset)
    {
        $this->client->index($this->assetToArray($asset));
    }

    /**
     * Check if a given asset exists
     *
     * @param Asset $asset
     *
     * @return array
     */
    public function exists(Asset $asset)
    {
        $params = [
            "id" => $asset->getId(),
            "index" => $this->index,
            "type" => $this->type
        ];

        return $this->client->exists($params);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(
        array $mustCriteria = [],
        array $filterCriteria = [],
        array $mustNotCriteria = [],
        array $shouldCriteria = [],
        $offset = null,
        $limit = null,
        $sorting = [],
        $additionalOptions = []
    ) {
        $result = $this->doSearch(
            $this->client,
            $this->index,
            $this->type,
            $mustCriteria,
            $filterCriteria,
            $mustNotCriteria,
            $shouldCriteria,
            $offset,
            $limit,
            $sorting,
            $additionalOptions
        );

        if (!isset($result["hits"]["hits"])) {
            return new ResultsList([], 0);
        }

        $assets = [];

        // Fetch list of documents based on results from Elastic Search
        // TODO optimize to use list
        foreach ($result["hits"]["hits"] as $page) {
            $id = (int) $page["_id"];

            if (($asset = Asset::getById($id)) instanceof Asset) {
                $assets[] = $asset;
            }
        }

        return new ResultsList($assets, $result["hits"]["total"]);
    }

    /**
     * Convert an asset to an array usable by Elasticsearch
     *
     * @param Asset $asset
     *
     * @return array
     */
    private function assetToArray(Asset $asset)
    {
        return [
            "id" => $asset->getId(),
            "body" => [
                "asset" => $this->processor->processAsset($asset)
            ],
            "index" => $this->index,
            "type" => $this->type,
            "timestamp" => $asset->getModificationDate()
        ];
    }
}
