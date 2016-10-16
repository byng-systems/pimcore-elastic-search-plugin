# Pimcore Elasticsearch Plugin

The Elasticsearch plugin for Pimcore Saves/updates contents (text only) of document's (editables) and assets to Elasticsearch. It also provides a query builder to search the index.

## Features

* Automatic indexing of documents and assets to Elasticsearch when created/updated in Pimcore admin. 
* Hooks to add custom properties to the index.
* A simple query builder to retrieve indexed documents. Supports querying, filtering, sorting and pagination.

## Installation via composer

The recommended method to install the Pimcore Elasticsearch Plugin is via [Composer](https://getcomposer.org/).

1 - Add [`byng/pimcore-elasticsearch-plugin`](https://packagist.org/packages/byng/pimcore-elasticsearch-plugin) as a dependency in your project's composer.json file and run `composer install`

2 - Copy the distribution config file (`elasticsearchplugin.xml.dist`) in the root of the plugin folder 
to `{PIMCORE_WEBSITE_DIR}/var/config/elasticsearchplugin.xml`.

3 - Enable the plugin in Pimcore using the extension manager.

## Quickstart

Once the installation has been completed, the first time you create or update a document the Elasticsearch index will be created and the document will be indexed. You can verify that the index exists using curl or [Kibana](https://www.elastic.co/products/kibana).

## Hooks

Hooks allow you to hook into the plugin at various points to provide additional functionality. It uses the standard Zend EventManager which Pimcore uses. 

### Registering an event listener example

For example to register an even listener:

```php
// @var $eventManager Zend_EventManager_EventManager
$eventManager->attach(
    "document.elasticsearch.preIndex",
    [__CLASS__, "handlePreIndex"]
);
```

The above code will call the `handlePreIndex()` method of the class where it was added whenever a document is ready to be indexed.

Within the `handlePreIndex()` method you have access to the actual Pimcore document which is being indexed and also the data the plugin has lready extracted. You can add additional properties to the parameters array and they will also be saved to the index:

```php
public static function handlePreIndex(ZendEvent $event)
{
    /** @var Page $document */
    $document = $event->getTarget();
    $params = $event->getParams();

    $params["body"]["page"]["customProperty"] = "something";
}
```

## Available hooks

The following hooks are currently available:

### document.elasticsearch.preIndex

This hook is called after the plugin has extracted all the information from the document to index and before it writes the data to Elasticsearch. You can use this hook to write additional/custom properties to the index.

### asset.elasticsearch.preIndex

This hook is the asset equivalent of "document.elasticsearch.preIndex".


## Querying

The pluigin provides a simple query builder to make it easy to extract information from Elasticsearch.

### Example

```php
use Byng\Pimcore\Elasticsearch\Query\BoolQuery;
use Byng\Pimcore\Elasticsearch\Query\MatchQuery;
use Byng\Pimcore\Elasticsearch\Query\Query;
use Byng\Pimcore\Elasticsearch\Query\QueryBuilder;
use Byng\Pimcore\Elasticsearch\Gateway\PageGateway;

$boolQuery = new BoolQuery();
$boolQuery->addMust(new MatchQuery("_all", "something"));

$query = new Query($boolQuery);

$queryBuilder = new QueryBuilder();
$queryBuilder->setQuery($query);
$queryBuilder->setSize(10); // number of results to return

$pageGateway = PageGateway::getInstance();
$resultSet =  $pageGateway->query($queryBuilder);
```

The following json request will be generated form the above code and sent to Elasticsearch:

```json
{
    "query": {
        "bool": {
            "must": [
                {
                    "match": {
                        "_all": "something"
                    }
                }
            ]
        }
    },
    "size": 10
}
```

This will retrieve all documents that contain the word "something".

### Todo

* Provide more complete query documentation
* Add support for custom queries, i.e. an array which can be converted to json and posted to Elasticsearch without any processing if the plugin doesn't support the whole Elasticsearch DSL for querying.

## License

MIT
