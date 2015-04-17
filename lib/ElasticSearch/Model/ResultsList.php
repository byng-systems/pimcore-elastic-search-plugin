<?php
/**
 * ResultsList.php
 * Definition of class ResultsList
 * 
 * Created 17-Apr-2015 13:42:01
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 * @copyright (c) 2015, Byng Services Ltd
 */

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
     *
     * @var array
     */
    protected $results = [];
    
    /**
     *
     * @var integer
     */
    protected $totalHits;
    
    
    
    /**
     * 
     * @param array $results
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
        
        if ((($totalHits = (int) $totalHits)) < 0) {
            throw new UnexpectedValueException("Number of total hits must be greater than or equal to zero");
        }
        $this->totalHits = $totalHits;
    }
    
    /**
     * 
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }
    
    /**
     * 
     */
    public function getTotalHits()
    {
        return $this->totalHits;
    }
    
    /**
     * 
     * @param string $mode
     * @return integer
     */
    public function count($mode = COUNT_NORMAL)
    {
        return count($this->results, $mode);
    }

    /**
     * 
     * @return mixed
     */
    public function current()
    {
        return current($this->results);
    }

    /**
     * 
     * @return mixed
     */
    public function key()
    {
        return key($this->results);
    }

    /**
     * 
     * @return mixed
     */
    public function next()
    {
        next($this->results);
    }

    /**
     * 
     * @return mixed
     */
    public function rewind()
    {
        reset($this->results);
    }

    /**
     * 
     * @return boolean
     */
    public function valid()
    {
        return (current($this->results) !== false);
    }

    /**
     * 
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    /**
     * 
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->results[$offset];
    }

    /**
     * 
     * @param mixed $offset
     * @param mixed $value
     * @throws BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException("This result set is immutable");
    }

    /**
     * 
     * @param mixed $offset
     * @throws BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException("This result set is immutable");
    }

}
