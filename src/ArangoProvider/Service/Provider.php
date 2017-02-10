<?php
namespace ArangoProvider\Service;

use Silex\Application;
use Silex\ServiceProviderInterface;
use ArangoDBClient\Collection as ArangoCollection;
use ArangoDBClient\CollectionHandler as ArangoCollectionHandler;
use ArangoDBClient\Connection as ArangoConnection;
use ArangoDBClient\ConnectionOptions as ArangoConnectionOptions;
use ArangoDBClient\DocumentHandler as ArangoDocumentHandler;
use ArangoDBClient\Document as ArangoDocument;
use ArangoDBClient\Exception as ArangoException;
use ArangoDBClient\Export as ArangoExport;
use ArangoDBClient\ConnectException as ArangoConnectException;
use ArangoDBClient\ClientException as ArangoClientException;
use ArangoDBClient\ServerException as ArangoServerException;
use ArangoDBClient\Statement as ArangoStatement;
use ArangoDBClient\UpdatePolicy as ArangoUpdatePolicy;

class Provider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
    	if ( !isset($app['config']->arango) ) {
    		throw new \Exception("Connection settings for arango database is missing", 1);
    	}

    	$db = [
    		'database' => $app['config']->arango->database,
    		'host' => sprintf(
    			'tcp://%s:%d',
    			$app['config']->arango->host,
    			$app['config']->arango->port
    		),
    		'user' => $app['config']->arango->user,
    		'pass' => $app['config']->arango->pass
    	];

        $connectionOptions = array(
          ArangoConnectionOptions::OPTION_DATABASE => $db['database'],
      		ArangoConnectionOptions::OPTION_ENDPOINT => $db['host'],
      		ArangoConnectionOptions::OPTION_AUTH_TYPE => 'Basic',
      		ArangoConnectionOptions::OPTION_AUTH_USER => $db['user'],
      		ArangoConnectionOptions::OPTION_AUTH_PASSWD => $db['pass'],
      		ArangoConnectionOptions::OPTION_CONNECTION => 'Close',
      		ArangoConnectionOptions::OPTION_TIMEOUT => 3,
      		ArangoConnectionOptions::OPTION_RECONNECT => true,
      		ArangoConnectionOptions::OPTION_CREATE => true,
      		ArangoConnectionOptions::OPTION_UPDATE_POLICY => ArangoUpdatePolicy::LAST,
      	);
          ArangoException::enableLogging();

          $app['adb-h'] = $app->share(function() use ($connectionOptions) {
            return new Factory(
              new ArangoConnection( $connectionOptions )
            );
          });


			unset($db);
    }

    public function boot(Application $app)
    {
    }
}