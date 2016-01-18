<?php

// file:   ControllerWithResourcesTrait.php
// date:   2016-01-15
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Dummy\Controller;

use Cake\Controller\Controller;
use Jra\Controller\Traits\ResourcesTrait;

class ControllerWithResourcesTrait extends Controller
{
    use ResourcesTrait;

    /**
     * JSON REST API options.
     *
     * @var array
     */
    public $jraOptions = [
        'secure' => [
            'business_id' => 'getCurrentBusinessId'
        ],
        'table' => 'Users'
    ];

    /**
     * Returns the current business ID.
     *
     * @return int
     */
    public function getCurrentBusinessId()
    {
        return 1337;
    }
}
