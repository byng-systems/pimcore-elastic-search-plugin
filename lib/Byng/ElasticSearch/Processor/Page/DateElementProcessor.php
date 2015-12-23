<?php

namespace Byng\ElasticSearch\Processor\Page;

use Pimcore\Model\Document\Tag\Date;

/**
 * Date Element Processor
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class DateElementProcessor
{
    /**
     * Process a date tag
     *
     * @param Date $tag
     *
     * @return string
     */
    public function processElement(Date $tag)
    {
        return (string) $tag->getDataForResource();
    }
}
