<?php

namespace ElasticSearch\Job;

use ElasticSearch\Repository\PageRepository;
use Exception;
use Logger;
use Pimcore\Model\Document\Listing;
use Pimcore\Model\Document\Page;

/**
 * Class CacheAllPagesJob
 *
 * A job to cache all pages in the Pimcore Document structure.
 *
 * @author Matt Ward <matt@byng.co>
 * @author Callum Jones <callum@byng.co>
 */
class CacheAllPagesJob
{
    /**
     * Number of pages to process at once
     *
     * @var int
     */
    const PAGE_PROCESSING_LIMIT = 100;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * CacheAllPagesJob constructor.
     * @param PageRepository $pageRepository
     */
    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * Rebuild all page caches.
     *
     * @return void
     */
    public function rebuildPageCache()
    {
        $documentCount = Page::getTotalCount();

        for ($documentIndex = 0; $documentIndex < $documentCount; $documentIndex += self::PAGE_PROCESSING_LIMIT) {
            $documentListing = new Listing();
            $documentListing->setOffset($documentIndex);
            $documentListing->setLimit(self::PAGE_PROCESSING_LIMIT);
            $documentListing->setCondition("type = ?", [ "page" ]);

            foreach ($documentListing->load() as $document) {
                if ($document instanceof Page && $document->isPublished()) {
                    $this->rebuildDocumentCache($document);
                }
            }
        }
    }

    /**
     * Rebuild the document cache of a specific document.
     *
     * @param Page $document
     *
     * @return void
     */
    protected function rebuildDocumentCache(Page $document)
    {
        try {
            $this->pageRepository->save($document);
        } catch (Exception $ex) {
            Logger::error("Failed to update document with ID: " . $document->getId());
            Logger::error($ex->getMessage());
        }
    }
}
