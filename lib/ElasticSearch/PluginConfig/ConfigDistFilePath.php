<?php
/**
 *
 * @author      Michal Maszkiewicz
 * @package     
 */

namespace ElasticSearch\PluginConfig;

class ConfigDistFilePath
{
    protected $filePath;

    public function __construct()
    {
        $this->filePath = PIMCORE_PLUGINS_PATH . '/ElasticSearch/config.xml.dist';
    }

    public function __toString()
    {
        return $this->filePath;
    }
}
