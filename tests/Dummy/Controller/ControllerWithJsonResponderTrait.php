<?php

// file:   ControllerWithJsonResponderTrait.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Dummy\Controller;

use Cake\Controller\Controller;
use Jra\Controller\Traits\JsonResponderTrait;

class ControllerWithJsonResponderTrait extends Controller
{
    use JsonResponderTrait;
}
