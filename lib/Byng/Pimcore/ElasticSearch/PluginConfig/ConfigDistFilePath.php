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
        $this->filePath = PIMCORE_PLUGINS_PATH . "/Elasticsearch/config.xml.dist";
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
