# Pimcore Elasticsearch Plugin

## What it does

* Saves/updates contents (text only) of Document's editables to Elasticsearch
 
## Configure

Copy the distribution config file (`elasticsearchplugin.xml.dist`) in the root of the plugin folder 
to `{PIMCORE_WEBSITE_DIR}/var/config/elasticsearchplugin.xml`.

## Notes

* This plugin requires that the `elasticsearch-mapper-attachments` plugin is installed in 
Elasticsearch. If you do not have it installed, plugin installation in Pimcore will fail.
* This plugin automatically creates the indices defined in the configuration file, and also updates
the mapping of some. Please double-check that this will not negatively impact your Elasticsearch 
indices. You have been warned - we cannot be held responsible for any loss of data as a result of
the use of this plugin.

## License

MIT
