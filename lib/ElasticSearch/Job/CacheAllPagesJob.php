<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ElasticSearch\Job;

use Document;
use Document_Page;
use ElasticSearch\Repository\PageRepository;
use Exception;
use Logger;



/**
 * Description of CacheAllPagesJob
 *
 * @author matt
 */
class CacheAllPagesJob
{
    
    /**
     *
     * @var PageRepository 
     */
    protected $pageRepository;
    
    
    
    /**
     * 
     * @param PageRepository $pageRepository
     */
    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }
    
    /**
     * 
     */
    public function rebuildPageCache()
    {
        foreach (Document_Page::getList() as $document) {
            if ($document instanceof Document_Page) {
                $this->rebuildDocumentCache($document);
            }
        }
    }
    
    /**
     * 
     * @param Document_Page $document
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
