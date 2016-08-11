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

use Byng\Pimcore\Elasticsearch\Event\AssetEventManager;
use Byng\Pimcore\Elasticsearch\Event\DocumentEventManager;
use Byng\Pimcore\Elasticsearch\Gateway\AssetGatewayFactory;
use Byng\Pimcore\Elasticsearch\Job\CacheAllAssetsJob;
use Byng\Pimcore\Elasticsearch\Job\CacheAllPagesJob;
use Byng\Pimcore\Elasticsearch\PluginConfig\ConfigDistFilePath;
use Byng\Pimcore\Elasticsearch\PluginConfig\ConfigFilePath;
use Byng\Pimcore\Elasticsearch\Gateway\PageGatewayFactory;
use Byng\Pimcore\Elasticsearch\Gateway\RawGatewayFactory;
use Byng\Pimcore\Elasticsearch\PluginConfig\ConfigSchemaPath;
use Elasticsearch\ClientBuilder;
use Pimcore;
use Pimcore\API\Plugin\AbstractPlugin;
use Pimcore\API\Plugin\PluginInterface;
use Zend_Config;
use Zend_Config_Xml as XmlConfig;

/**
 * Elasticsearch Pimcore Plugin
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Michal Maszkiewicz
 * @author Asim Liaquat <asim@byng.co>
 */
final class ElasticsearchPlugin extends AbstractPlugin implements PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $config = self::loadConfig();
        $hosts = $config->get("hosts");

        $rawGatewayFactory = new RawGatewayFactory();
        $rawGateway = $rawGatewayFactory->build($hosts);
        
        if ($types = $config->get("types")) {
            $eventManager = Pimcore::getEventManager();

            if ($assetConfig = $types->get("asset")) {
                $assetGatewayFactory = new AssetGatewayFactory();
                $assetGateway = $assetGatewayFactory->build($hosts, $assetConfig);

                $assetEventManager = new AssetEventManager($eventManager, $assetGateway, new CacheAllAssetsJob($assetGateway));

                $assetEventManager->attachEvents();
            }

            if ($pageConfig = $types->get("page")) {
                $pageGatewayFactory = new PageGatewayFactory();
                $pageGateway = $pageGatewayFactory->build($hosts, $pageConfig);

                $documentEventManager = new DocumentEventManager($eventManager, $pageGateway, new CacheAllPagesJob($pageGateway));

                $documentEventManager->attachEvents();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function install()
    {
        if (self::isInstalled()) {
            return true;
        }

        $config = self::loadConfig();

        /** @var Zend_Config $types */
        if ($types = $config->get("types")) {
            $client = ClientBuilder::fromConfig([
                "hosts" => $config->get("hosts")->toArray()
            ]);

            /** @var Zend_Config $assetConfig */
            if ($assetConfig = $types->get("asset")) {
                $indexParams = [
                    "index" => $assetConfig->get("indexName")
                ];

                if (!$client->indices()->exists($indexParams)) {
                    $client->indices()->create($indexParams);
                }

                $client->indices()->putMapping($indexParams + [
                    "type" => $assetConfig->get("typeName"),
                    "body" => [
                        "asset" => [
                            "properties" => [
                                $assetConfig->get("typeName") => [
                                    "properties" => [
                                        $assetConfig->get("bodyContent")->get("propertyName") => [
                                            "type" => "attachment"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);
            }

            /** @var Zend_Config $pageConfig */
            if ($pageConfig = $types->get("page")) {
                $indexParams = [
                    "index" => $pageConfig->get("indexName")
                ];

                if (!$client->indices()->exists($indexParams)) {
                    $client->indices()->create($indexParams);
                }
            }
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

    /**
     * Load and validate the plugin configuration file.
     *
     * @return XmlConfig
     *
     * @throws \RuntimeException
     */
    private static function loadConfig()
    {
        $config = new \DOMDocument();
        $config->load(new ConfigFilePath());

        $isValid = $config->schemaValidate(new ConfigSchemaPath());

        if (!$isValid) {
            throw new \RuntimeException("Invalid Elasticsearch configuration.");
        }

        return new XmlConfig($config->saveXML());
    }
}
