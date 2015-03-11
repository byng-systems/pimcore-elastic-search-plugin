<?php
/**
 *
 * @author      Michal Maszkiewicz
 * @package     Elastic Search Plugin
 */

namespace ElasticSearch;

use Closure;
use Document_Page;
use Schedule_Maintenance_Job;
use Schedule_Manager_Procedural;
use Zend_EventManager_Event as Event;
use Zend_EventManager_EventManager;
use Zend_EventManager_Exception_InvalidArgumentException;


class EventManager
{
    /**
     * 
     */
    const MAINTENANCE_JOB_REBUILD_PAGES = 'elasticsearch-recache-pages';
    
    /**
     * @var Zend_EventManager_EventManager
     */
    protected $pimcoreEventManager;

    /**
     * @var PageRepository
     */
    protected $pageRepository;
    
    /**
     *
     * @var CacheAllPagesJob
     */
    protected $cacheAllPagesJob;
    
    
    
    /**
     * @param Zend_EventManager_EventManager $pimcoreEventManager
     * @param PageRepository $pageRepository
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
     * Attached a maintenance 
     */
    public function attachMaintenance()
    {
        $this->pimcoreEventManager->attach(
            'system.maintenance',
            Closure::bind(
                function(Event $event) {
                    /* @var $target Schedule_Manager_Procedural */
                    $target = $event->getTarget();

                    $target->registerJob(
                        new Schedule_Maintenance_Job(
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
     * @throws Zend_EventManager_Exception_InvalidArgumentException
     */
    public function attachPostUpdate()
    {
        // Hook into document update event.
        $this->pimcoreEventManager->attach('document.postUpdate', function ($event) {
            /** @var Document_Page $document */
            $document = $event->getTarget();
            // We do not want to index snippets.
            if ($document instanceof Document_Page) {

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
        $this->pimcoreEventManager->attach('document.postDelete', function ($event) {
            /** @var Document_Page $document */
            $document = $event->getTarget();
            // Disregard snippets.
            if ($document instanceof Document_Page) {

                $this->pageRepository->delete($document);

            }
        });
    }
}
