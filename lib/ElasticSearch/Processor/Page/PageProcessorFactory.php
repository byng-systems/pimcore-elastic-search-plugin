<?php

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
        $elementProcessor = new ElementProcessor(new HtmlToText());

        return new PageProcessor(
            $elementProcessor,
            new DateElementProcessor(),
            new SelectElementProcessor(
                ($filter ?: new TagKeyFilter()),
                $elementProcessor
            ),
            new HrefElementProcessor(new ObjectTagProcessor(), $filter)
        );
    }

}
