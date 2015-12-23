<?php

namespace Byng\ElasticSearch\Processor\Page;

use Byng\ElasticSearch\Filter\FilterInterface;
use Byng\ElasticSearch\Filter\TagKeyFilter;
use NF\HtmlToText;

/**
 * PageProcessor Factory
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class PageProcessorFactory
{
    /**
     * Build the PageProcessor
     *
     * @param FilterInterface|null $filter
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
            )
        );
    }
}
