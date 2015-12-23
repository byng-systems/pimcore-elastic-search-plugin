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

use Byng\Pimcore\Elasticsearch\Job\CacheAllPagesJob;
use Byng\Pimcore\Elasticsearch\Repository\PageRepository;
use Closure;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Schedule\Maintenance\Job as MaintenanceJob;
use Pimcore\Model\Schedule\Manager\Procedural as ProceduralScheduleManager;
use Zend_EventManager_Event as ZendEvent;
use Zend_EventManager_EventManager as ZendEventManager;
use Zend_EventManager_Exception_InvalidArgumentException as ZendEventManagerInvalidArgumentException;

/**
 * Event Manager
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Michal Maszkiewicz
 */
final class EventManager
{
    const MAINTENANCE_JOB_REBUILD_PAGES = "elasticsearch-recache-pages";

    /**
     * @var ZendEventManager
     */
    private $pimcoreEventManager;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var CacheAllPagesJob
     */
    private $cacheAllPagesJob;


    /**
     * Constructor
     *
     * @param ZendEventManager $pimcoreEventManager
     * @param PageRepository $pageRepository
     * @param CacheAllPagesJob $cacheAllPagesJob
     */
    public function __construct(
        ZendEventManager $pimcoreEventManager,
        PageRepository $pageRepository,
        CacheAllPagesJob $cacheAllPagesJob
    ) {
        $this->pimcoreEventManager = $pimcoreEventManager;
        $this->pageRepository = $pageRepository;
        $this->cacheAllPagesJob = $cacheAllPagesJob;
    }

    /**
     * Attaches system maintenance event handler
     *
     * @return void
     */
    public function attachMaintenance()
    {
        $this->pimcoreEventManager->attach(
            'system.maintenance',
            Closure::bind(
                function(ZendEvent $event) {
                    /* @var $target ProceduralScheduleManager */
                    $target = $event->getTarget();

                    $target->registerJob(
                        new MaintenanceJob(
                            self::MAINTENANCE_JOB_REBUILD_PAGES,
                            $this->cacheAllPagesJob,
                            'rebuildPageCache'
                        )
                    );
                },
                $this
            )
        );
    }

    /**
     * Attaches indexing/deleting in Elastic Search index to document post update event
     *
     * @return void
     *
     * @throws ZendEventManagerInvalidArgumentException
     */
    public function attachPostUpdate()
    {
        // Hook into document update event.
        $this->pimcoreEventManager->attach("document.postUpdate", function (ZendEvent $event) {
            /** @var Page $document */
            $document = $event->getTarget();

            if ($document instanceof Page) {
                if ($document->isPublished()) {
                    $this->pageRepository->save($document);
                } else {
                    // When un-publishing a document remove it from the index
                    $this->pageRepository->delete($document);
                }
            }
        });
    }

    /**
     * Attaches deleting documents from Elastic Search index to post delete event
     *
     * @return void
     *
     * @throws ZendEventManagerInvalidArgumentException
     */
    public function attachPostDelete()
    {
        $this->pimcoreEventManager->attach('document.postDelete', function (ZendEvent $event) {
            /** @var Page $document */
            $document = $event->getTarget();

            if ($document instanceof Page) {
                $this->pageRepository->delete($document);
            }
        });
    }
}
