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

namespace Byng\Pimcore\Elasticsearch\Job;

use Byng\Pimcore\Elasticsearch\Gateway\AssetGateway;
use Exception;
use Logger;
use Pimcore\Model\Asset;

/**
 * Maintenance job to cache all assets
 *
 * @author Elliot Wright <elliot@byng.co>
 */
final class CacheAllAssetsJob
{
    /**
     * @var AssetGateway
     */
    private $assetGateway;


    /**
     * Constructor
     *
     * @param AssetGateway $assetGateway
     */
    public function __construct(AssetGateway $assetGateway)
    {
        $this->assetGateway = $assetGateway;
    }

    /**
     * Rebuilds the asset cache (non-destructive)
     *
     * @return void
     */
    public function rebuildAssetsCache()
    {
        foreach (Asset::getList() as $asset) {
            if ($asset instanceof Asset) {
                $this->rebuildAssetCache($asset);
            }
        }
    }

    /**
     * Rebuild a specific asset
     *
     * @param Asset $asset
     *
     * @return void
     */
    protected function rebuildAssetCache(Asset $asset)
    {
        try {
            $this->assetGateway->save($asset);
        } catch (Exception $ex) {
            Logger::error(sprintf("Failed to update document with ID: '%s'", $asset->getId()));
            Logger::error($ex->getMessage());
        }
    }
}
