<?php

namespace ElasticSearch\Processor\Page;

use Object_Tag;

/**
 * ObjectTagProcessor
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class ObjectTagProcessor
{
    /**
     * Process a tag object
     *
     * @param Object_Tag $tag
     *
     * @return string
     */
    public function processTag(Object_Tag $tag)
    {
        return str_replace(" ", "_", $tag->getName());
    }

}
