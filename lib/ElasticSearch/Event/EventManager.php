<?php

namespace ElasticSearch\Event;

use ElasticSearch\Job\CacheAllPagesJob;
use ElasticSearch\Repository\PageRepository;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Schedule\Maintenance\Job;
use Pimcore\Model\Schedule\Manager\Procedural;
use Zend_EventManager_Event as Event;
use Zend_EventManager_EventManager;
use Zend_EventManager_Exception_InvalidArgumentException;

/**
 * EventManager
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class EventManager
{
    const MAINTENANCE_JOB_REBUILD_PAGES = "elasticsearch-recache-pages";

    /**
     * @var Zend_EventManager_EventManager
     */
    protected $pimcoreEventManager;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var CacheAllPagesJob
     */
    protected $cacheAllPagesJob;


    /**
     * EventManager constructor.
     *
     * @param Zend_EventManager_EventManager $pimcoreEventManager
     * @param PageRepository                 $pageRepository
     * @param CacheAllPagesJob               $cacheAllPagesJob
     */
    public function __construct(
        Zend_EventManager_EventManager $pimcoreEventManager,
        PageRepository $pageRepository,
        CacheAllPagesJob $cacheAllPagesJob
    ) {
        $this->pimcoreEventManager = $pimcoreEventManager;
        $this->pageRepository = $pageRepository;
        $this->cacheAllPagesJob = $cacheAllPagesJob;
    }

    /**
     * Add the maintenance job to Pimcore, so it will run as part of the maintenance script.
     *
     * @return void
     */
    public function attachMaintenance()
    {
        $this->pimcoreEventManager->attach(
            "system.maintenance",
            \Closure::bind(function(Event $event) {
                /* @var Procedural $target */
                $target = $event->getTarget();
                $target->registerJob(new Job(
                    self::MAINTENANCE_JOB_REBUILD_PAGES,
                    $this->cacheAllPagesJob,
                    "rebuildPageCache"
                ));
            }, $this)
        );
    }

    /**
     * Attaches indexing/deleting in Elastic Search index to document post update event
     *
     * @throws Zend_EventManager_Exception_InvalidArgumentException
     */
    public function attachPostUpdate()
    {
        // Hook into document update event.
        $this->pimcoreEventManager->attach("document.postUpdate", function ($event) {
            /** @var Page $document */
            $document = $event->getTarget();

            // We do not want to index snippets.
            if ($document instanceof Page) {
                // Index only published documents.
                if ($document->isPublished()) {
                    $this->pageRepository->save($document);
                } else {
                    // When un-publishing a document remove it from the index.
                    $this->pageRepository->delete($document);
                }
            }
        });
    }

    /**
     * Attaches deleting documents from Elastic Search index to post delete event
     *
     * @throws Zend_EventManager_Exception_InvalidArgumentException
     */
    public function attachPostDelete()
    {
        $this->pimcoreEventManager->attach("document.postDelete", function ($event) {
            /** @var Page $document */
            $document = $event->getTarget();

            // Disregard snippets.
            if ($document instanceof Page) {
                $this->pageRepository->delete($document);
            }
        });
    }
}
