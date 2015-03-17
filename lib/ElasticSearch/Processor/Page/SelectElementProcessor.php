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
     * @param FilterInterface $filter
     */
    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    /**
     * 
     * @param Document_Tag_Select $select
     * @return string
     * @throws ProcessorException
     */
    public function processElement(Document_Tag_Select $select)
    {
        $elementData = $select->getData();
        
        if (
            is_numeric(($elementData = trim($elementData)))
            && ($object = Object_Abstract::getById($elementData)) instanceof Object_Abstract
        ) {
            return [
                $elementData,
                $this->filter->filter($object->getKey())
            ];
        }
        
        throw new ProcessorException(
            'This processor only accepts tags with immediate string data'
        );
    }
    
}
