<?php

namespace Byng\Pimcore\Elasticsearch\Processor\Page;

use Byng\Pimcore\Elasticsearch\Filter\FilterInterface;
use Byng\Pimcore\Elasticsearch\Filter\TagKeyFilter;
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
