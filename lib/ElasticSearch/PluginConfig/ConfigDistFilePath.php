<?php

namespace ElasticSearch\PluginConfig;

/**
 * Config Dist File Path
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Michal Maszkiewicz
 */
final class ConfigDistFilePath
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->filePath = PIMCORE_PLUGINS_PATH . "/ElasticSearch/config.xml.dist";
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->filePath;
    }
}
