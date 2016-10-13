<?php

namespace ElasticSearch\Filter;

/**
 * FilterInterface
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
interface FilterInterface
{
    /**
     * Filter some input.
     *
     * @param string $input
     * @return string
     */
    public function filter($input);
}
