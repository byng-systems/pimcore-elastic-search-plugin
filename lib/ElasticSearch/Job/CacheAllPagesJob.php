<?php

namespace ElasticSearch\Job;

use Document_Page;
use ElasticSearch\Repository\PageRepository;
use Exception;
use Logger;

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
        $documentCount = Document_Page::getTotalCount();

        for ($documentIndex = 0; $documentIndex < $documentCount; $documentIndex += self::PAGE_PROCESSING_LIMIT) {
            $documents = Document_Page::getList([
                "limit" => self::PAGE_PROCESSING_LIMIT,
                "offset" => $documentIndex
            ]);

            foreach ($documents as $document) {
                if ($document instanceof Document_Page && $document->isPublished()) {
                    $this->rebuildDocumentCache($document);
                }
            }
        }
    }

    /**
     * Rebuild the document cache of a specific document.
     *
     * @param Document_Page $document
     *
     * @return void
     */
    protected function rebuildDocumentCache(Document_Page $document)
    {
        try {
            $this->pageRepository->save($document);
        } catch (Exception $ex) {
            Logger::error("Failed to update document with ID: " . $document->getId());
            Logger::error($ex->getMessage());
        }
    }
}
