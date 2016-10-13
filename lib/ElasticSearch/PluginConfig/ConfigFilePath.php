<?php

namespace ElasticSearch\PluginConfig;

/**
 * ConfigFilePath
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class ConfigFilePath
{
    const CONFIG_FILE_NAME = 'elasticsearchplugin.xml';

    /**
     * @var string
     */
    protected $fullPath;


    /**
     * ConfigFilePath constructor.
     */
    public function __construct()
    {
        $this->fullPath = PIMCORE_CONFIGURATION_DIRECTORY . '/' . self::CONFIG_FILE_NAME;
    }

    /**
     * Convert file path to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->fullPath;
    }

    /**
     * Get directory.
     *
     * @return string
     */
    public function getDirectory()
    {
        return dirname($this->fullPath);
    }
}
