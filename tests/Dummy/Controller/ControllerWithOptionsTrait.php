<?php

// file:   ControllerWithOptionsTrait.php
// date:   2016-01-17
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Dummy\Controller;

use Cake\Controller\Controller;
use Jra\Controller\Traits\OptionsTrait;

class ControllerWithOptionsTrait extends Controller
{
    use OptionsTrait;

    /**
     * JSON REST API options.
     *
     * @var array
     */
    public $jraOptions = [
        'name' => 'Foo'
    ];
}
