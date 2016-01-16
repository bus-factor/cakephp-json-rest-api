<?php

// file:   CrudOperationsTraitTest.php
// date:   2016-01-16
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Test\Unit\Controller\Traits;

use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\Entity;
use PHPUnit_Framework_TestCase;

class CrudOperationsTraitTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $response = new Response();
        $data = ['foo' => 'bar'];
        $request = new Request();
        $request->data = $data;
        $errors = [];

        $resource = $this->getMock('Cake\ORM\Entity', ['errors']);
        $resource->expects($this->any())->method('errors')->will($this->returnValue($errors));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['newResource', 'saveResource', 'respondWith'], [$request, $response]);
        $controller->expects($this->once())->method('newResource')->with($data)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('saveResource')->with($resource)->will($this->returnValue(true));
        $controller->expects($this->once())->method('respondWith')->with($resource)->will($this->returnValue($response));

        $this->assertSame($response, $controller->create());
    }

    public function testCreateToHandleValidationErrors()
    {
        $response = new Response();
        $data = ['foo' => 'bar'];
        $request = new Request();
        $request->data = $data;
        $errors = ['foo' => ['_empty' => 'This field cannot be left empty']];

        $resource = $this->getMock('Cake\ORM\Entity', ['errors']);
        $resource->expects($this->any())->method('errors')->will($this->returnValue($errors));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['newResource', 'saveResource', 'respondWith'], [$request, $response]);
        $controller->expects($this->once())->method('newResource')->with($data)->will($this->returnValue($resource));
        $controller->expects($this->never())->method('saveResource');
        $controller->expects($this->once())->method('respondWith')->with($errors, ['code' => 400])->will($this->returnValue($response));

        $this->assertSame($response, $controller->create());
    }

    public function testDestroy()
    {
        $id = 1337;
        $response = new Response();
        $resource = new Entity();

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'deleteResource', 'respondWith']);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('deleteResource')->with($resource)->will($this->returnValue(true));
        $controller->expects($this->once())->method('respondWith')->with(null)->will($this->returnValue($response));

        $this->assertSame($response, $controller->destroy($id));
    }

    /**
     * @expectedException Cake\Network\Exception\NotFoundException
     */
    public function testDestroyToHandleMissingResource()
    {
        $id = 1337;
        $response = new Response();
        $resource = false;

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'deleteResource', 'respondWith']);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->never())->method('deleteResource');
        $controller->expects($this->never())->method('respondWith');

        $controller->destroy($id);
    }

    public function testIndex()
    {
        $response = new Response();
        $resources = [new Entity(), new Entity()];

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResources', 'respondWith']);
        $controller->expects($this->once())->method('findResources')->will($this->returnValue($resources));
        $controller->expects($this->once())->method('respondWith')->with($resources)->will($this->returnValue($response));

        $this->assertSame($response, $controller->index());
    }

    public function testUpdate()
    {
        $id = 1337;
        $data = ['foo' => 'bar'];
        $request = new Request();
        $request->data = $data;
        $response = new Response();
        $errors = [];

        $resource = $this->getMock('Cake\ORM\Entity', ['errors']);
        $resource->expects($this->any())->method('errors')->will($this->returnValue($errors));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'patchResource', 'saveResource', 'respondWith'], [$request, $response]);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('patchResource')->with($resource, $data);
        $controller->expects($this->once())->method('saveResource')->with($resource)->will($this->returnValue(true));
        $controller->expects($this->once())->method('respondWith')->with($resource)->will($this->returnValue($response));

        $this->assertSame($response, $controller->update($id));
    }

    public function testUpdateToHandleValidationErrors()
    {
        $id = 1337;
        $data = ['foo' => 'bar'];
        $request = new Request();
        $request->data = $data;
        $response = new Response();
        $errors = ['foo' => ['_empty' => 'This field cannot be left empty']];

        $resource = $this->getMock('Cake\ORM\Entity', ['errors']);
        $resource->expects($this->any())->method('errors')->will($this->returnValue($errors));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'patchResource', 'saveResource', 'respondWith'], [$request, $response]);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('patchResource')->with($resource, $data);
        $controller->expects($this->never())->method('saveResource');
        $controller->expects($this->once())->method('respondWith')->with($errors, ['code' => 400])->will($this->returnValue($response));

        $this->assertSame($response, $controller->update($id));
    }

    /**
     * @expectedException Cake\Network\Exception\NotFoundException
     */
    public function testUpdateToHandleMissingResource()
    {
        $id = 1337;
        $data = ['foo' => 'bar'];
        $request = new Request();
        $request->data = $data;
        $response = new Response();
        $errors = [];
        $resource = false;

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'patchResource', 'saveResource', 'respondWith'], [$request, $response]);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->never())->method('patchResource');
        $controller->expects($this->never())->method('saveResource');
        $controller->expects($this->never())->method('respondWith');

        $controller->update($id);
    }

    public function testView()
    {
        $id = 1337;
        $response = new Response();
        $resource = new Entity();

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'respondWith']);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('respondWith')->with($resource)->will($this->returnValue($response));

        $this->assertSame($response, $controller->view($id));
    }

    /**
     * @expectedException Cake\Network\Exception\NotFoundException
     */
    public function testViewToHandleMissingResource()
    {
        $id = 1337;
        $response = new Response();
        $resource = false;

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'respondWith']);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->never())->method('respondWith');

        $controller->view($id);
    }
}
