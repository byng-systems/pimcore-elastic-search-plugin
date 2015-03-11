<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ElasticSearch;

use Document_Page;

/**
 * Description of CacheAllPagesJob
 *
 * @author matt
 */
class CacheAllPagesJob
{
    
    /**
     *
     * @var type 
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
                $this->pageRepository->save($document);
            }
        }
    }
    
}
