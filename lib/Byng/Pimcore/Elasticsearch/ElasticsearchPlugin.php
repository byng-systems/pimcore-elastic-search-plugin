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

namespace Byng\Pimcore\Elasticsearch;

use Byng\Pimcore\Elasticsearch\Event\EventManager as DocumentEventManager;
use Byng\Pimcore\Elasticsearch\Job\CacheAllPagesJob;
use Byng\Pimcore\Elasticsearch\PluginConfig\ConfigDistFilePath;
use Byng\Pimcore\Elasticsearch\PluginConfig\ConfigFilePath;
use Byng\Pimcore\Elasticsearch\Repository\PageRepositoryFactory;
use Pimcore;
use Pimcore\API\Plugin\AbstractPlugin;
use Pimcore\API\Plugin\PluginInterface;
use Zend_Config_Xml as XmlConfig;

/**
 * Elasticsearch Pimcore Plugin
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Michal Maszkiewicz
 */
final class ElasticsearchPlugin extends AbstractPlugin implements PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $config = new XmlConfig(new ConfigFilePath());
        $repositoryFactory = new PageRepositoryFactory();
        $pageRepository = $repositoryFactory->build($config);

        $documentEventManager = new DocumentEventManager(
            Pimcore::getEventManager(),
            $pageRepository,
            new CacheAllPagesJob($pageRepository)
        );

        $documentEventManager->attachPostDelete();
        $documentEventManager->attachPostUpdate();
        $documentEventManager->attachMaintenance();
    }

    /**
     * {@inheritdoc}
     */
    public static function install()
    {
        if (self::isInstalled()) {
            return true;
        }

        $configPath = new ConfigFilePath();

        if (!is_writable($configPath->getDirectory())) {
            throw new \RuntimeException(sprintf(
                "Unable to write to config directory: '%s'",
                $configPath->getDirectory()
            ));
        }

        if (copy(new ConfigDistFilePath(), $configPath)) {
            return true;
        }

        throw new \RuntimeException("Unable to create a config file: " . $configPath);
    }

    /**
     * {@inheritdoc}
     */
    public static function uninstall()
    {
        if (self::isInstalled()) {
            unlink(new ConfigFilePath());
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function isInstalled()
    {
        $configPath = new ConfigFilePath();

        if (file_exists($configPath)) {
            if (is_writable($configPath)) {
                // Consider installed as config file exists and is writable.
                return true;
            }

            throw new \RuntimeException(sprintf(
                "Config file exists, but is not writable: '%s'",
                $configPath
            ));
        }

        return false;
    }
}
