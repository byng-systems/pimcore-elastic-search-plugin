<?php
/**
 * ObjectTagProcessor.php
 * Definition of class ObjectTagProcessor
 * 
 * Created 16-Mar-2015 12:25:12
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 * @copyright (c) 2015, Byng Services Ltd
 */
namespace ElasticSearch\Processor\Page;

use Object_Tag;



/**
 * ObjectTagProcessor
 * 
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class ObjectTagProcessor
{
    
    /**
     * 
     * @param Object_Tag $tag
     * @return string|null
     */
    public function processTag(Object_Tag $tag)
    {
        return str_replace(' ', '_', $tag->getName());
    }
    
}
