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
     * @var DateElementProcessor
     */
    protected $dateElementProcessor;
    
    /**
     *
     * @var SelectElementProcessor
     */
    protected $selectElementProcessor;
    
    /**
     *
     * @var HrefElementProcessor
     */
    protected $hrefElementProcessor;
    
    
    
    /**
     * 
     * @param ElementProcessor $elementProcessor
     * @param DateElementProcessor $dateElementProcessor
     * @param SelectElementProcessor $selectElementProcessor
     * @param HrefElementProcessor $hrefElementProcessor
     */
    public function __construct(
        ElementProcessor $elementProcessor,
        DateElementProcessor $dateElementProcessor,
        SelectElementProcessor $selectElementProcessor,
        HrefElementProcessor $hrefElementProcessor
    ) {
        $this->elementProcessor = $elementProcessor;
        $this->dateElementProcessor = $dateElementProcessor;
        $this->selectElementProcessor = $selectElementProcessor;
        $this->hrefElementProcessor = $hrefElementProcessor;
    }
    
    /**
     * 
     * @param Document_Page $document
     * @return array
     */
    public function processPage(Document_Page $document)
    {
        $body = [
            'controller'    =>  $document->getController(),
            'action'        =>  $document->getAction(),
            'created'       =>  $document->getCreationDate(),
            'title'         =>  $document->getTitle(),
            'description'   =>  $document->getDescription()
        ];
        
        /* @var Document_Tag $element */
        foreach ($document->getElements() as $key => $element) {
            
            if (!($element instanceof Document_Tag)) {
                continue;
            }
            
            try {
                $this->processPageElement($body, $key, $element);
            } catch (ProcessorException $ex) {
                continue;
            }
        }
        
        return $body;
    }
    
    /**
     * 
     * @param array $body
     * @param string $elementKey
     * @param Document_Tag $element
     */
    protected function processPageElement(
        array &$body,
        $elementKey,
        Document_Tag $element
    ) {
        if ($elementKey === 'article-tags-drop') {
            $a = 'b';
        }
        
        switch (ltrim(get_class($element), '\\')) {
            case 'Document_Tag_Multihref':
                $this->hrefElementProcessor->processElement(
                    $body,
                    $elementKey,
                    $element
                );
                return;
            
            case 'Document_Tag_Select':
                $this->selectElementProcessor->processElement(
                    $body,
                    $elementKey,
                    $element
                );
                return;
                
            case 'Document_Tag_Date':
                $body[$elementKey] = $this->dateElementProcessor->processElement($element);
                return;
                
            case 'Document_Tag':
            default:
                $body[$elementKey] = $this->elementProcessor->processElement($element);
                return;
        }
    }
    
}
