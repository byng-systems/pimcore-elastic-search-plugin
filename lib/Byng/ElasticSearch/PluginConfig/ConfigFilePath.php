<?php

namespace Byng\ElasticSearch\PluginConfig;

/**
 * Config File Path
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Michal Maszkiewicz
 */
final class ConfigFilePath
{
    const CONFIG_FILE_NAME = "elasticsearchplugin.xml";

    /**
     * @var string
     */
    private $fullPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fullPath = PIMCORE_CONFIGURATION_DIRECTORY . "/" . self::CONFIG_FILE_NAME;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->fullPath;
    }

    /**
     * Get directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return dirname($this->fullPath);
    }
}
