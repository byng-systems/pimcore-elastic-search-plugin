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

namespace Byng\Pimcore\Elasticsearch\Event;

use Byng\Pimcore\Elasticsearch\Job\CacheAllAssetsJob;
use Byng\Pimcore\Elasticsearch\Gateway\AssetGateway;
use Closure;
use Pimcore\Model\Asset;
use Pimcore\Model\Schedule\Maintenance\Job as MaintenanceJob;
use Pimcore\Model\Schedule\Manager\Procedural as ProceduralScheduleManager;
use Zend_EventManager_Event as ZendEvent;
use Zend_EventManager_EventManager as ZendEventManager;
use Zend_EventManager_Exception_InvalidArgumentException as ZendEventManagerInvalidArgumentException;

/**
 * Asset Event Manager
 *
 * @author Elliot Wright <elliot@byng.co>
 */
final class AssetEventManager implements EventManagerInterface
{
    const MAINTENANCE_JOB_REBUILD_ASSETS = "elasticsearch-recache-assets";

    /**
     * @var ZendEventManager
     */
    private $pimcoreEventManager;

    /**
     * @var AssetGateway
     */
    private $assetGateway;

    /**
     * @var CacheAllAssetsJob
     */
    private $cacheAllAssetsJob;


    /**
     * Constructor
     *
     * @param ZendEventManager  $pimcoreEventManager
     * @param AssetGateway      $assetGateway
     * @param CacheAllAssetsJob $cacheAllAssetsJob
     */
    public function __construct(
        ZendEventManager $pimcoreEventManager,
        AssetGateway $assetGateway,
        CacheAllAssetsJob $cacheAllAssetsJob
    ) {
        $this->pimcoreEventManager = $pimcoreEventManager;
        $this->assetGateway = $assetGateway;
        $this->cacheAllAssetsJob = $cacheAllAssetsJob;
    }

    /**
     * {@inheritdoc}
     */
    public function attachEvents()
    {
        $this->attachMaintenance();
        $this->attachPostAdd();
        $this->attachPostDelete();
        $this->attachPostUpdate();
    }

    /**
     * Attaches system maintenance event handler
     *
     * @return void
     */
    public function attachMaintenance()
    {
        $this->pimcoreEventManager->attach(
            "system.maintenance",
            Closure::bind(
                function(ZendEvent $event) {
                    /** @var ProceduralScheduleManager $target */
                    $target = $event->getTarget();

                    $target->registerJob(
                        new MaintenanceJob(
                            self::MAINTENANCE_JOB_REBUILD_ASSETS,
                            $this->cacheAllAssetsJob,
                            "rebuildAssetsCache"
                        )
                    );
                },
                $this
            )
        );
    }

    /**
     * Attaches indexing/deleting in Elastic Search index to asset post add event
     *
     * @return void
     *
     * @throws ZendEventManagerInvalidArgumentException
     */
    public function attachPostAdd()
    {
        // Hook into asset add event.
        $this->pimcoreEventManager->attach("asset.postAdd", function (ZendEvent $event) {
            /** @var Asset $asset */
            $asset = $event->getTarget();

            if ($asset instanceof Asset) {
                $this->assetGateway->save($asset);
            }
        });
    }

    /**
     * Attaches indexing/deleting in Elastic Search index to asset post update event
     *
     * @return void
     *
     * @throws ZendEventManagerInvalidArgumentException
     */
    public function attachPostUpdate()
    {
        // Hook into asset update event.
        $this->pimcoreEventManager->attach("asset.postUpdate", function (ZendEvent $event) {
            /** @var Asset $asset */
            $asset = $event->getTarget();

            if ($asset instanceof Asset) {
                $this->assetGateway->save($asset);
            }
        });
    }

    /**
     * Attaches deleting assets from Elastic Search index to post delete event
     *
     * @return void
     *
     * @throws ZendEventManagerInvalidArgumentException
     */
    public function attachPostDelete()
    {
        $this->pimcoreEventManager->attach("asset.postDelete", function (ZendEvent $event) {
            /** @var Asset $asset */
            $asset = $event->getTarget();

            if ($asset instanceof Asset) {
                $this->assetGateway->delete($asset);
            }
        });
    }
}
