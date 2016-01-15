<?php

// file:   ControllerWithResponderTrait.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Test\Dummy\Controller;

use Cake\Controller\Controller;
use JsonRestApi\Controller\Traits\ResponderTrait;

class ControllerWithResponderTrait extends Controller
{
    use ResponderTrait;
}
