<?php

namespace ElasticSearch\PluginConfig;

/**
 * ConfigDistFilePath
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class ConfigDistFilePath
{
    /**
     * @var string
     */
    protected $filePath;

    /**
     * ConfigDistFilePath constructor.
     */
    public function __construct()
    {
        $this->filePath = PIMCORE_PLUGINS_PATH . '/ElasticSearch/config.xml.dist';
    }

    /**
     * Convert dist file path to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->filePath;
    }
}
