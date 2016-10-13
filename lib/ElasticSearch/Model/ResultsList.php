<?php

namespace ElasticSearch\Model;

use ArrayAccess;
use BadMethodCallException;
use Countable;
use InvalidArgumentException;
use Iterator;
use UnexpectedValueException;

/**
 * ResultsList
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class ResultsList implements Iterator, ArrayAccess, Countable
{
    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var integer
     */
    protected $totalHits;


    /**
     * ResultsList constructor.
     *
     * @param array   $results
     * @param integer $totalHits
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function __construct(array $results, $totalHits)
    {
        $this->results = $results;

        if (!is_numeric($totalHits)) {
            throw new InvalidArgumentException("Number of total hits must be given as an integer");
        }

        if ((($totalHits = (int)$totalHits)) < 0) {
            throw new UnexpectedValueException("Number of total hits must be greater than or equal to zero");
        }

        $this->totalHits = $totalHits;
    }

    /**
     * Get results.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Get totalHits.
     *
     * @return integer
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
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException("This result set is immutable");
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException("This result set is immutable");
    }
}
