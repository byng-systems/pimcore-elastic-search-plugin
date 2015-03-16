<?php
/**
 * PageProcessorFactory.php
 * Definition of class PageProcessorFactory
 * 
 * Created 16-Mar-2015 12:43:02
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 * @copyright (c) 2015, Byng Services Ltd
 */
namespace ElasticSearch\Processor\Page;

use NF\HtmlToText;



/**
 * PageProcessorFactory
 * 
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class PageProcessorFactory
{
    
    /**
     * 
     * @return PageProcessor
     */
    public function build()
    {
        return new PageProcessor(
            new ElementProcessor(new HtmlToText()),
            new HrefElementProcessor(new ObjectTagProcessor())
        );
    }
    
}
