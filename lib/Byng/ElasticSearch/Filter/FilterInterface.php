<?php

namespace Byng\ElasticSearch\Filter;

/**
 * Filter Interface
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
interface FilterInterface
{
    /**
     * Filter a given input string
     *
     * @param string $input
     *
     * @return string
     */
    public function filter($input);
}
