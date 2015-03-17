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

use ElasticSearch\Filter\FilterInterface;
use ElasticSearch\Filter\TagKeyFilter;
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
    public function build(FilterInterface $filter = null)
    {
        return new PageProcessor(
            new ElementProcessor(new HtmlToText()),
            new SelectElementProcessor($filter ?: new TagKeyFilter()),
            new HrefElementProcessor(new ObjectTagProcessor())
        );
    }
    
}
