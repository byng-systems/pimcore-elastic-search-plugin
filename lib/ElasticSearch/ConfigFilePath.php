<?php
/**
 *
 * @author      Michal Maszkiewicz
 * @package     
 */

namespace ElasticSearch;


class ConfigFilePath
{
    const   CONFIG_FILE_NAME            =       'elasticsearchplugin.xml';

    /** @var string */
    protected $fullPath;

    public function __construct()
    {
        $this->fullPath = PIMCORE_CONFIGURATION_DIRECTORY . '/' . self::CONFIG_FILE_NAME;
    }

    public function __toString()
    {
        return $this->fullPath;
    }

    public function getDirectory()
    {
        return dirname($this->fullPath);
    }
}
