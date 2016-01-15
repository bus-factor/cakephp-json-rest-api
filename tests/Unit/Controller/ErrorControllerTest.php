<?php

// file:   ErrorControllerTest.php
// date:   2016-01-13
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Test\Unit\Controller;

use Cake\Event\Event;
use Cake\Network\Response;
use Exception;
use JsonRestApi\Controller\ErrorController;
use PHPUnit_Framework_TestCase;

class ErrorControllerTest extends PHPUnit_Framework_TestCase
{
    public function testInstanceOf()
    {
        $errorController = new ErrorController();

        $this->assertInstanceOf('Cake\Controller\Controller', $errorController);
    }

    public function testBeforeRender()
    {
        $event = new Event('foo');
        $code = 401;
        $message = 'Foo message';
        $error = new Exception('', $code);
        $options = ['code' => $code, 'message' => $message];

        $response = new Response();

        $errorController = $this->getMock('JsonRestApi\Controller\ErrorController', ['respondWith']);
        $errorController->expects($this->once())->method('respondWith')->with(null, $options)->will($this->returnValue($response));

        $errorController->viewVars = [
            'error' => $error,
            'message' => $message
        ];

        $errorController->response = $response;

        $this->assertSame($response, $errorController->beforeRender($event));
    }
}
