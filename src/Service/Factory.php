<?php
namespace Service\Arango;

use ArangoDBClient\Collection as ArangoCollection;
use ArangoDBClient\CollectionHandler as ArangoCollectionHandler;
use ArangoDBClient\Connection as ArangoConnection;
use ArangoDBClient\DocumentHandler as ArangoDocumentHandler;
use ArangoDBClient\Document as ArangoDocument;
use ArangoDBClient\Statement as ArangoStatement;

class Factory
{
	private $connection;

	function __construct(ArangoConnection $connection)
	{
		$this->connection = $connection;
	}

	function newCollection($name)
	{
		return new Collection($name);
	}

	function newDocument()
	{
		return new Document();
	}

	function handleDocument()
	{
		return new ArangoDocumentHandler($this->connection);
	}

	function handleCollection(Collection $value=null)
	{
		return new ArangoCollectionHandler($this->connection);
	}

	function newReadStatement($query, $batchSize = 1000, $canCount = true)
	{
		return new ArangoStatement(
			$this->connection,
			array(
				"query" => $query,
				"batchSize" => $batchSize,
				"count" => $canCount,
				"sanitize" => true,
			)
		);
	}

	function newChangeStatement($query, $bindVars)
	{
		return new ArangoStatement(
			$this->connection,
			array(
				"query" => $query,
				"bindVars" => $bindVars,
				"batchSize" => 1,
				"count" => true,
				"sanitize" => true,
			)
		);
	}
}