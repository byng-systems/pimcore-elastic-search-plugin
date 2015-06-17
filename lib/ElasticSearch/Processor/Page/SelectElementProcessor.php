<?php
/**
 * SelectElementProcessor.php
 * Definition of class SelectElementProcessor
 * 
 * Created 16-Mar-2015 15:24:55
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 * @copyright (c) 2015, Byng Services Ltd
 */
namespace ElasticSearch\Processor\Page;

use Document_Tag_Multiselect;
use Document_Tag_Select;
use ElasticSearch\Filter\FilterInterface;
use ElasticSearch\Processor\ProcessorException;
use Object_Abstract;



/**
 * SelectElementProcessor
 * 
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class SelectElementProcessor
{
    
    /**
     *
     * @var FilterInterface
     */
    protected $filter;
    
    /**
     *
     * @var ElementProcessor
     */
    protected $fallbackProcessor;
    
    
    
    /**
     * 
     * @param FilterInterface $filter
     * @param ElementProcessor $fallbackProcessor
     */
    public function __construct(
        FilterInterface $filter,
        ElementProcessor $fallbackProcessor
    ) {
        $this->filter = $filter;
        $this->fallbackProcessor = $fallbackProcessor;
    }

    /**
     * 
     * @param array $body
     * @param type $key
     * @param Document_Tag_Select $select
     * @throws ProcessorException
     */
    public function processElement(
        array &$body,
        $key,
        Document_Tag_Select $select
    ) {
        $elementData = $select->getData();
        
        if (
            is_numeric(($elementData = trim($elementData)))
            && ($object = Object_Abstract::getById($elementData)) instanceof Object_Abstract
        ) {
            $rawElementData = $object->getKey();
            
            $body[$key] = [
                $elementData,
                $this->filter->filter($rawElementData)
            ];
            
            return ($body[$key . '-collated'] = $rawElementData);
        }
        
        return $body[$key] = $this->fallbackProcessor->processElement($select);
    }
    
    public function processMultiSelectElement(
        array &$body,
        $key,
        Document_Tag_Multiselect $select
    ) {
        return ($body[$key] = $select->getData());
    }
    
}
