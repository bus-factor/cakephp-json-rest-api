<?php

// file:   ControllerWithResourcesTrait.php
// date:   2016-01-15
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Test\Dummy\Controller;

use Cake\Controller\Controller;
use JsonRestApi\Controller\Traits\ResourcesTrait;

class ControllerWithResourcesTrait extends Controller
{
    use ResourcesTrait;

    /**
     * Resources trait options.
     *
     * @var array
     */
    public $resourcesOptions = [
        'name' => 'Users'
    ];
}
