<?php
/**
 * FilterInterface.php
 * Definition of interface FilterInterface
 * 
 * Created 16-Mar-2015 16:01:03
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 * @copyright (c) 2015, Byng Services Ltd
 */
namespace ElasticSearch\Filter;



/**
 * FilterInterface
 * 
 * @author M.D.Ward <matthew.ward@byng.co>
 */
interface FilterInterface
{
    
    /**
     * 
     * @param string $input
     * @return string
     */
    public function filter($input);
    
}
