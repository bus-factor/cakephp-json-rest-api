<?php

// file:   bootstrap.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

use Cake\Core\Configure;
use Cake\Error\ErrorHandler;

Configure::write('Error.exceptionRenderer', 'JsonRestApi\Error\ExceptionRenderer');

if (PHP_SAPI !== 'cli') {
    (new ErrorHandler(Configure::read('Error')))->register();
}
