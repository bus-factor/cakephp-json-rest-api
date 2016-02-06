<?php

// file:   JsonResponderTraitTest.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Unit\Controller\Traits;

use Cake\Utility\Hash;
use Jra\Test\Dummy\Controller\ControllerWithJsonResponderTrait;
use PHPUnit_Framework_TestCase;

class JsonResponderTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestRespondWithJsonData
     */
    public function testRespondWithJson($arguments, $expectedBody)
    {
        $expectedStatusCode = Hash::get($arguments, [1, 'code'], 200);

        $response = $this->getMock('Cake\Network\Response', ['type', 'statusCode', 'body']);
        $response->expects($this->once())->method('type')->with('json');
        $response->expects($this->once())->method('statusCode')->with($expectedStatusCode);
        $response->expects($this->once())->method('body')->with($expectedBody);

        $controller = new ControllerWithJsonResponderTrait(null, $response);

        $this->assertSame($response, call_user_func_array([$controller, 'respondWithJson'], $arguments));
    }

    public function provideTestRespondWithJsonData()
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
        ],[
            ['foo', ['code' => 500, 'message' => 'Some error.', 'pagination' => ['limit' => 1337, 'page' => 42]]],
            '{"status":"failure","code":500,"errors":"foo","message":"Some error.","pagination":{"limit":1337,"page":42}}'
        ]];
    }
}
