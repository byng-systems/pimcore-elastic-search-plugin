<?php

namespace Byng\Pimcore\Elasticsearch\Model;

use ArrayAccess;
use BadMethodCallException;
use Countable;
use InvalidArgumentException;
use Iterator;
use UnexpectedValueException;

/**
 * Results List
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class ResultsList implements Iterator, ArrayAccess, Countable
{
    /**
     * @var array
     */
    private $results = [];

    /**
     * @var integer
     */
    private $totalHits;


    /**
     * Constructor
     *
     * @param array $results
     * @param integer $totalHits
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function __construct(array $results, $totalHits)
    {
        if (!is_int($totalHits)) {
            throw new InvalidArgumentException("Number of total hits must be given as an integer");
        }

        if ($totalHits < 0) {
            throw new UnexpectedValueException("Number of total hits must be greater than or equal to zero");
        }

        $this->results = $results;
        $this->totalHits = $totalHits;
    }

    /**
     * Get results
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Get totalHits
     *
     * @return int
     */
    public function getTotalHits()
    {
        return $this->totalHits;
    }

    /**
     * {@inheritdoc}
     */
    public function count($mode = COUNT_NORMAL)
    {
        return count($this->results, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->results);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->results);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->results);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->results);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return (current($this->results) !== false);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->results[$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException("This result set is immutable");
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException("This result set is immutable");
    }
}
