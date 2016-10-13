<?php

namespace ElasticSearch\Processor\Page;

use Pimcore\Model\Object\Tag;

/**
 * Object Tag Processor
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class ObjectTagProcessor
{
    /**
     * Process a Tag.
     *
     * @param Tag $tag
     * @return string|null
     */
    public function processTag(Tag $tag)
    {
        return str_replace(' ', '_', $tag->getName());
    }
}
