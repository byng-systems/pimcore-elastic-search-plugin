<?php
/**
 * TagKeyFilter.php
 * Definition of class TagKeyFilter
 * 
 * Created 16-Mar-2015 16:00:55
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 * @copyright (c) 2015, Byng Services Ltd
 */
namespace ElasticSearch\Filter;



/**
 * TagKeyFilter
 * 
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class TagKeyFilter implements FilterInterface
{
    
    /**
     *
     * @var array
     */
    protected $replaceable = [' ', '-', '\''];
    
    /**
     *
     * @var string
     */
    protected $replacement = '_';
    
    
    
    /**
     * 
     * @param array $replaceable
     * @param type $replacement
     */
    public function __construct(array $replaceable = null, $replacement = null)
    {
        if ($replaceable !== null) {
            $this->setReplaceable($replaceable);
        }
        
        if ($replacement !== null) {
            $this->setReplacement($replacement);
        }
    }
    
    /**
     * 
     * @return array
     */
    public function getReplaceable()
    {
        return $this->replaceable;
    }
    
    /**
     * 
     * @return string
     */
    public function getReplacement()
    {
        return $this->replacement;
    }
    
    /**
     * 
     * @param array $replaceable
     * @return interface
     */
    public function setReplaceable(array $replaceable)
    {
        $this->replaceable = $replaceable;
        
        return $this;
    }

    /**
     * 
     * @param string $replacement
     * @return \ElasticSearch\Filter\TagKeyFilter
     */
    public function setReplacement($replacement)
    {
        $this->replacement = $replacement;
        
        return $this;
    }
    
    /**
     * 
     * @param string $input
     * @return string
     */
    public function filter($input)
    {
        return str_replace(
            $this->replaceable,
            $this->replacement,
            strtolower((string) $input)
        );
    }
    
}
