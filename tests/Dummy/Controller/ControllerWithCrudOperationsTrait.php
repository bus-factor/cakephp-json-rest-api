<?php

// file:   ControllerWithCrudOperationsTrait.php
// date:   2016-01-16
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Test\Dummy\Controller;

use Cake\Controller\Controller;
use JsonRestApi\Controller\Traits\CrudOperationsTrait;

class ControllerWithCrudOperationsTrait extends Controller
{
    use CrudOperationsTrait;

    /**
     * Resources trait options.
     *
     * @var array
     */
    public $resourcesOptions = [
        'name' => 'Users'
    ];
}
