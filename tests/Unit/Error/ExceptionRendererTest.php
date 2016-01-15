<?php

// file:   ExceptionRendererTest.php
// date:   2016-01-13
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Test\Unit\Error;

use Exception;
use JsonRestApi\Error\ExceptionRenderer;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class ExceptionRendererTest extends PHPUnit_Framework_TestCase
{
    public function testGetController()
    {
        $class = new ReflectionClass('JsonRestApi\Error\ExceptionRenderer');

        $method = $class->getMethod('_getController');
        $method->setAccessible(true);

        $exception = new Exception();
        $exceptionRenderer = new ExceptionRenderer($exception);

        $controller = $method->invoke($exceptionRenderer);

        $this->assertInstanceOf('JsonRestApi\Controller\ErrorController', $controller);
    }
}
