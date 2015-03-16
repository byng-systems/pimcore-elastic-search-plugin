<?php
/**
 * HrefElementProcessor.php
 * Definition of class HrefElementProcessor
 * 
 * Created 16-Mar-2015 11:51:09
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 * @copyright (c) 2015, Byng Services Ltd
 */
namespace ElasticSearch\Processor\Page;

use Document_Tag_Href;
use Document_Tag_Multihref;
use Object_Tag;



/**
 * HrefElementProcessor
 * 
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class HrefElementProcessor
{
    
    /**
     *
     * @var ObjectTagProcessor
     */
    protected $objectTagProcessor;
    
    
    
    /**
     * 
     * @param ObjectTagProcessor $objectTagProcessor
     */
    public function __construct(ObjectTagProcessor $objectTagProcessor)
    {
        $this->objectTagProcessor = $objectTagProcessor;
    }
    
    /**
     * 
     * @param Document_Tag_Multihref $element
     * @return array
     */
    public function processElement(Document_Tag_Multihref $element)
    {
        $tags = [];
        
        foreach ($element->getElements() as $childElement) {
            if ($childElement instanceof Object_Tag) {
                $tags[] = $this->objectTagProcessor->processTag($childElement);
            }
        }
        
        return $tags;
    }
    
}
