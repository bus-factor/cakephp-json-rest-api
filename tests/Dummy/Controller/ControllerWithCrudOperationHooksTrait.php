<?php

// file:   ControllerWithCrudOperationHooksTrait.php
// date:   2016-01-18
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Dummy\Controller;

use Cake\Controller\Controller;
use Jra\Controller\Traits\CrudOperationHooksTrait;

class ControllerWithCrudOperationHooksTrait extends Controller
{
    use CrudOperationHooksTrait;
}
