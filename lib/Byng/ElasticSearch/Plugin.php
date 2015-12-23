<?php

use Byng\ElasticSearch\Event\EventManager as DocumentEventManager;
use Byng\ElasticSearch\Job\CacheAllPagesJob;
use Byng\ElasticSearch\PluginConfig\ConfigDistFilePath;
use Byng\ElasticSearch\PluginConfig\ConfigFilePath;
use Byng\ElasticSearch\Repository\PageRepositoryFactory;
use Pimcore\API\Plugin\AbstractPlugin;
use Pimcore\API\Plugin\PluginInterface;

/**
 * ElasticSearch Pimcore Plugin
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 * @author Michal Maszkiewicz
 */
final class ElasticSearchPlugin extends AbstractPlugin implements PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $config = new Zend_Config_Xml(new ConfigFilePath());
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
            throw new RuntimeException(sprintf(
                "Unable to write to config directory: '%s'",
                $configPath->getDirectory()
            ));
        }

        if (copy(new ConfigDistFilePath(), $configPath)) {
            return true;
        }

        throw new RuntimeException("Unable to create a config file: " . $configPath);
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

            throw new RuntimeException(sprintf(
                "Config file exists, but is not writable: '%s'",
                $configPath
            ));
        }

        return false;
    }
}
