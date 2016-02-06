# JsonRestApi plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require bus-factor/cakephp-json-rest-api
```

## Setup

First, load the plugin in your ```config/bootstrap.php```:
```
Plugin::load('Jra', ['bootstrap' => true])
```

Second, use the ```Jra\Controller\Traits\CrudOperationsTrait``` in the controllers that should respond with JSON:
```
<?php

namespace App\Controller;

use Jra\Controller\Traits\CrudOperationsTrait;

class UsersController extends AppController
{
    use CrudOperationsTrait;
}
```

Third, define the routes to the controller in your ```config/routes.php```:
```
Router::scope('/', function ($routes) {
    $routes->connect('/users', ['controller' => 'Users', 'action' => 'index', '[method]' => 'GET']);
    $routes->connect('/users', ['controller' => 'Users', 'action' => 'create', '[method]' => 'POST']);
    $routes->connect('/users/:id', ['controller' => 'Users', 'action' => 'view', '[method]' => 'GET'], ['id' => '/^\d+^$/', 'pass' => ['id']]);
    $routes->connect('/users/:id', ['controller' => 'Users', 'action' => 'update', '[method]' => 'PUT'], ['id' => '/^\d+^$/', 'pass' => ['id']]);
    $routes->connect('/users/:id', ['controller' => 'Users', 'action' => 'destroy', '[method]' => 'DELETE'], ['id' => '/^\d+^$/', 'pass' => ['id']]);

});
```

## Customization

### CRUD options

You can provide options to the CRUD operations trait:
```
public $jraOptions = [
    'pagination' => [
        'limit' => 50
    ],
    'secure' => [
        'user_id' => 'getCurrentUserId'
    ],
    'table' => 'Users'
];
```

### CRUD operation hooks

You can implement hook methods:
```
public function beforeCreate(Entity &$resource);
public function afterCreate(Entity &$resource);
public function beforeDelete(Entity &$resource);
public function afterDelete(Entity &$resource);
public function beforeUpdate(Entity &$resource);
public function afterUpdate(Entity &$resource);
```
