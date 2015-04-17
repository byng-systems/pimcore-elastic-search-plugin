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

use Document_Tag_Multihref;
use ElasticSearch\Filter\FilterInterface;
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
     * @var FilterInterface
     */
    protected $tagKeyFilter;
    
    
    
    /**
     * 
     * @param ObjectTagProcessor $objectTagProcessor
     * @param FilterInterface $tagKeyFilter
     */
    public function __construct(
        ObjectTagProcessor $objectTagProcessor,
        FilterInterface $tagKeyFilter
    ) {
        $this->objectTagProcessor = $objectTagProcessor;
        $this->tagKeyFilter = $tagKeyFilter;
    }
    
    /**
     * 
     * @param array $body
     * @param string $elementKey
     * @param Document_Tag_Multihref $element
     * @return array
     */
    public function processElement(
        array &$body,
        $elementKey,
        Document_Tag_Multihref $element
    ) {
        $tagKeys = [];
        $tagValues = [];
        
        foreach ($element->getElements() as $childElement) {
            if ($childElement instanceof Object_Tag) {
                $tagKeys[] = $this->tagKeyFilter->filter($childElement->getKey());
                $tagKeys[] = $this->objectTagProcessor->processTag($childElement);
                
                $tagValues[] = $childElement->getName();
            }
        }
        
        $body[$elementKey] = $tagKeys;
        $body[$elementKey . '-collated'] = implode(' ', $tagValues);
        
        return $tagKeys;
    }
    
}
