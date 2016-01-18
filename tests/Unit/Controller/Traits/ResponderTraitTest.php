<?php

// file:   ResponderTraitTest.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Unit\Controller\Traits;

use Cake\Utility\Hash;
use Jra\Test\Dummy\Controller\ControllerWithResponderTrait;
use PHPUnit_Framework_TestCase;

class ResponderTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestRespondWithData
     */
    public function testRespondWith($arguments, $expectedBody)
    {
        $expectedStatusCode = Hash::get($arguments, [1, 'code'], 200);

        $response = $this->getMock('Cake\Network\Response', ['type', 'statusCode', 'body']);
        $response->expects($this->once())->method('type')->with('json');
        $response->expects($this->once())->method('statusCode')->with($expectedStatusCode);
        $response->expects($this->once())->method('body')->with($expectedBody);

        $controller = new ControllerWithResponderTrait(null, $response);

        $this->assertSame($response, call_user_func_array([$controller, 'respondWith'], $arguments));
    }

    public function provideTestRespondWithData()
    {
        return [[
            ['foo'],
            '{"status":"success","code":200,"data":"foo","message":null}'
        ], [
            [(object)['a' => 'b'], ['message' => 'Yay!']],
            '{"status":"success","code":200,"data":{"a":"b"},"message":"Yay!"}'
        ],[
            [[['a' => 'b'], ['c' => 'd']]],
            '{"status":"success","code":200,"data":[{"a":"b"},{"c":"d"}],"message":null}'
        ],[
            ['foo', ['code' => 302, 'message' => 'Some message.']],
            '{"status":"success","code":302,"data":"foo","message":"Some message."}'
        ],[
            ['foo', ['code' => 500, 'message' => 'Some error.']],
            '{"status":"failure","code":500,"errors":"foo","message":"Some error."}'
        ]];
    }
}
