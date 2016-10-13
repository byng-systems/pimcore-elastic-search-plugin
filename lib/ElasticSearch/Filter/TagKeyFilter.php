<?php

namespace ElasticSearch\Filter;

/**
 * TagKeyFilter
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class TagKeyFilter implements FilterInterface
{
    /**
     * @var array
     */
    protected $replaceable = [' ', '-', '\''];

    /**
     * @var string
     */
    protected $replacement = '_';


    /**
     * TagKeyFilter constructor.
     *
     * @param array $replaceable
     * @param string $replacement
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
     * Get replaceable.
     *
     * @return array
     */
    public function getReplaceable()
    {
        return $this->replaceable;
    }

    /**
     * Get replacement.
     *
     * @return string
     */
    public function getReplacement()
    {
        return $this->replacement;
    }

    /**
     * Set replaceable.
     *
     * @param array $replaceable
     * @return $this
     */
    public function setReplaceable(array $replaceable)
    {
        $this->replaceable = $replaceable;

        return $this;
    }

    /**
     * Set replacement.
     *
     * @param string $replacement
     * @return $this
     */
    public function setReplacement($replacement)
    {
        $this->replacement = $replacement;

        return $this;
    }

    /**
     * {@inheritdoc}
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
