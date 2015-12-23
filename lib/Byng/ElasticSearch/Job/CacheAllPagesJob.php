<?php

namespace Byng\ElasticSearch\Job;

use Byng\ElasticSearch\Repository\PageRepository;
use Exception;
use Logger;
use Pimcore\Model\Document\Page;

/**
 * Description of CacheAllPagesJob
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class CacheAllPagesJob
{
    /**
     * @var PageRepository
     */
    private $pageRepository;


    /**
     * Constructor
     *
     * @param PageRepository $pageRepository
     */
    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * Rebuilds the page cache (non-destructive)
     *
     * @return void
     */
    public function rebuildPageCache()
    {
        foreach (Page::getList() as $document) {
            if ($document instanceof Page && $document->isPublished()) {
                $this->rebuildDocumentCache($document);
            }
        }
    }

    /**
     * Rebuild a specific document
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
            Logger::error(sprintf("Failed to update document with ID: '%s'", $document->getId()));
            Logger::error($ex->getMessage());
        }
    }
}
