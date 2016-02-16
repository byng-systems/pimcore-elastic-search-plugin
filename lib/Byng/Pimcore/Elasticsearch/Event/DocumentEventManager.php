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

use Byng\Pimcore\Elasticsearch\Gateway\PageGateway;
use Byng\Pimcore\Elasticsearch\Job\CacheAllPagesJob;
use Closure;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Schedule\Maintenance\Job as MaintenanceJob;
use Pimcore\Model\Schedule\Manager\Procedural as ProceduralScheduleManager;
use Zend_EventManager_Event as ZendEvent;
use Zend_EventManager_EventManager as ZendEventManager;
use Zend_EventManager_Exception_InvalidArgumentException as ZendEventManagerInvalidArgumentException;

/**
 * Document Event Manager
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Michal Maszkiewicz
 */
final class DocumentEventManager implements EventManagerInterface
{
    const MAINTENANCE_JOB_REBUILD_PAGES = "elasticsearch-recache-pages";

    /**
     * @var ZendEventManager
     */
    private $pimcoreEventManager;

    /**
     * @var PageGateway
     */
    private $pageGateway;

    /**
     * @var CacheAllPagesJob
     */
    private $cacheAllPagesJob;


    /**
     * Constructor
     *
     * @param ZendEventManager $pimcoreEventManager
     * @param PageGateway $pageGateway
     * @param CacheAllPagesJob $cacheAllPagesJob
     */
    public function __construct(
        ZendEventManager $pimcoreEventManager,
        PageGateway $pageGateway,
        CacheAllPagesJob $cacheAllPagesJob
    ) {
        $this->pimcoreEventManager = $pimcoreEventManager;
        $this->pageGateway = $pageGateway;
        $this->cacheAllPagesJob = $cacheAllPagesJob;
    }

    /**
     * {@inheritdoc}
     */
    public function attachEvents()
    {
        $this->attachPostDelete();
        $this->attachPostUpdate();
        $this->attachMaintenance();
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
                    $this->pageGateway->save($document);
                } else {
                    // When un-publishing a document remove it from the index
                    $this->pageGateway->delete($document);
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
                $this->pageGateway->delete($document);
            }
        });
    }
}
