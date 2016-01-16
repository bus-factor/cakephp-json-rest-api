<?php

// file:   bootstrap.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

require __DIR__ . '/../vendor/autoload.php';

Configure::write('Datasources', [
    'default' => [
        'className' => 'Cake\Database\Connection',
        'driver' => 'Cake\Database\Driver\Sqlite',
        'database' => __DIR__ . '/default.sqlite',
        'encoding' => 'utf8',
        'timezone' => 'UTC',
        'cacheMetadata' => false,
        'log' => false
    ],
    'test' => [
        'className' => 'Cake\Database\Connection',
        'driver' => 'Cake\Database\Driver\Sqlite',
        'database' => __DIR__ . '/test.sqlite',
        'encoding' => 'utf8',
        'timezone' => 'UTC',
        'cacheMetadata' => false,
        'log' => false
    ]
]);

ConnectionManager::config(Configure::consume('Datasources'));
