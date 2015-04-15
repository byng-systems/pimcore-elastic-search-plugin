<?php
/**
 * DateElementProcessor.php
 * Definition of class DateElementProcessor
 * 
 * Created 15-Apr-2015 10:39:28
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 * @copyright (c) 2015, Byng Services Ltd
 */
namespace ElasticSearch\Processor\Page;

use Document_Tag_Date;



/**
 * DateElementProcessor
 * 
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class DateElementProcessor
{
    
    /**
     * 
     * @param Document_Tag_Date $tag
     * @return string
     */
    public function processElement(Document_Tag_Date $tag)
    {
        return (string) $tag->getDataForResource();
    }
    
}
