<?php
/**
 * PageProcessor.php
 * Definition of class PageProcessor
 * 
 * Created 16-Mar-2015 12:17:52
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 * @copyright (c) 2015, Byng Services Ltd
 */
namespace ElasticSearch\Processor\Page;

use Document_Page;
use Document_Tag;
use ElasticSearch\Processor\ProcessorException;



/**
 * PageProcessor
 * 
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class PageProcessor
{
    
    /**
     *
     * @var ElementProcessor
     */
    protected $elementProcessor;
    
    /**
     *
     * @var HrefElementProcessor
     */
    protected $hrefElementProcessor;
    
    
    
    /**
     * 
     * @param ElementProcessor $elementProcessor
     * @param HrefElementProcessor $hrefElementProcessor
     */
    public function __construct(
        ElementProcessor $elementProcessor,
        HrefElementProcessor $hrefElementProcessor
    ) {
        $this->elementProcessor = $elementProcessor;
        $this->hrefElementProcessor = $hrefElementProcessor;
    }
    
    /**
     * 
     * @param \ElasticSearch\Processor\Page\Document_Page $document
     * @return array
     */
    public function processPage(Document_Page $document)
    {
        $body = [];
        
        /* @var Document_Tag $element */
        foreach ($document->getElements() as $key => $element) {
            
            if (!($element instanceof Document_Tag)) {
                continue;
            }
            
            try {
                $body[$key] = $this->processPageElement($element);
            } catch (ProcessorException $ex) {
                continue;
            }
        }
        
        return $body;
    }
    
    protected function processPageElement(Document_Tag $element)
    {
        switch (ltrim(get_class($element), '\\')) {
            case 'Document_Tag_Multihref':
                return $this->hrefElementProcessor->processElement($element);
            
            case 'Document_Tag':
            default:
                return $this->elementProcessor->processElement($element);
        }
    }
    
}
