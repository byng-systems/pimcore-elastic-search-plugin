<?php

namespace ElasticSearch\Processor\Page;

use Pimcore\Model\Document\Tag;

/**
 * DateElementProcessor
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class DateElementProcessor
{
    /**
     * Process a date element.
     *
     * @param Tag\Date $tag
     * @return string
     */
    public function processElement(Tag\Date $tag)
    {
        return (string) $tag->getDataForResource();
    }
}
