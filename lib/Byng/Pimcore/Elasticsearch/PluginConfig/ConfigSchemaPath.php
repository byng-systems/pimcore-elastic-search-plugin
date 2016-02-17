<?php

/**
 * This file is part of the "byng/pimcore-elasticsearch-plugin" project.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the LICENSE is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Byng\Pimcore\Elasticsearch\PluginConfig;

/**
 * Config Schema Path
 *
 * @author Elliot Wright <elliot@elliotwright.co>
 */
final class ConfigSchemaPath
{
    const CONFIG_FILE_NAME = "elasticsearchplugin.xsd";

    /**
     * @var string
     */
    private $fullPath;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fullPath = PIMCORE_PLUGINS_PATH . "/PimcoreElasticsearchPlugin/" . self::CONFIG_FILE_NAME;
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
