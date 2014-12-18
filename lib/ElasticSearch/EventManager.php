<?php
/**
 *
 * @author      Michal Maszkiewicz
 * @package     
 */

namespace ElasticSearch;


class EventManager
{
    /**
     * @var \Zend_EventManager_EventManager
     */
    protected $pimcoreEventManager;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @param \Zend_EventManager_EventManager $pimcoreEventManager
     * @param PageRepository $pageRepository
     */
    public function __construct(
        \Zend_EventManager_EventManager $pimcoreEventManager,
        PageRepository $pageRepository
    ) {
        $this->pimcoreEventManager = $pimcoreEventManager;
        $this->pageRepository = $pageRepository;
    }

    /**
     * Attaches indexing/deleting in Elastic Search index to document post update event
     *
     * @throws \Zend_EventManager_Exception_InvalidArgumentException
     */
    public function attachPostUpdate()
    {
        // Hook into document update event.
        $this->pimcoreEventManager->attach('document.postUpdate', function ($event) {
            /** @var \Document_Page $document */
            $document = $event->getTarget();
            // We do not want to index snippets.
            if ($document instanceof \Document_Page) {

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
     * @throws \Zend_EventManager_Exception_InvalidArgumentException
     */
    public function attachPostDelete()
    {
        $this->pimcoreEventManager->attach('document.postDelete', function ($event) {
            /** @var \Document_Page $document */
            $document = $event->getTarget();
            // Disregard snippets.
            if ($document instanceof \Document_Page) {

                $this->pageRepository->delete($document);

            }
        });
    }
}
