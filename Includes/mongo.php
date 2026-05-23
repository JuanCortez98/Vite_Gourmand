<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('MONGO_URI', 'mongodb://127.0.0.1:27017');
define('MONGO_DB', 'vite_gourmand');

function getMongoManager() {
    if (!class_exists('MongoDB\Driver\Manager')) {
        throw new RuntimeException('Le driver MongoDB pour PHP n est pas installé. Installez l extension mongodb.');
    }

    return new MongoDB\Driver\Manager(MONGO_URI);
}

function mongoQuery(string $collection, array $filter = [], array $options = []): array
{
    $namespace = MONGO_DB . '.' . $collection;
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = getMongoManager()->executeQuery($namespace, $query);

    $documents = [];
    foreach ($cursor as $document) {
        $documents[] = json_decode(json_encode($document), true);
    }

    return $documents;
}

function mongoInsertOne(string $collection, array $document): string
{
    if (!isset($document['created_at'])) {
        $document['created_at'] = new MongoDB\BSON\UTCDateTime();
    }

    $bulk = new MongoDB\Driver\BulkWrite();
    $insertedId = $bulk->insert($document);
    getMongoManager()->executeBulkWrite(MONGO_DB . '.' . $collection, $bulk);
    return (string)$insertedId;
}

function mongoUpdateOne(string $collection, array $filter, array $update, array $options = []): bool
{
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->update($filter, ['$set' => $update], array_merge(['multi' => false, 'upsert' => false], $options));
    $result = getMongoManager()->executeBulkWrite(MONGO_DB . '.' . $collection, $bulk);
    return $result->getMatchedCount() > 0;
}

function mongoDeleteOne(string $collection, array $filter, array $options = []): int
{
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->delete($filter, array_merge(['limit' => 1], $options));
    $result = getMongoManager()->executeBulkWrite(MONGO_DB . '.' . $collection, $bulk);
    return $result->getDeletedCount();
}
