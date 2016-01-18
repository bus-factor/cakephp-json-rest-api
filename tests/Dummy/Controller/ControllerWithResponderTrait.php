<?php

// file:   ControllerWithResponderTrait.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Dummy\Controller;

use Cake\Controller\Controller;
use Jra\Controller\Traits\ResponderTrait;

class ControllerWithResponderTrait extends Controller
{
    use ResponderTrait;
}
