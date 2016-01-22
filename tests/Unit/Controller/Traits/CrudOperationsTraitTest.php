<?php

// file:   CrudOperationsTraitTest.php
// date:   2016-01-16
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Unit\Controller\Traits;

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

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['newResource', 'saveResource', 'respondWithJson'], [$request, $response]);
        $controller->expects($this->once())->method('newResource')->with($data)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('saveResource')->with($resource)->will($this->returnValue(true));
        $controller->expects($this->once())->method('respondWithJson')->with($resource)->will($this->returnValue($response));

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

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['newResource', 'saveResource', 'respondWithJson'], [$request, $response]);
        $controller->expects($this->once())->method('newResource')->with($data)->will($this->returnValue($resource));
        $controller->expects($this->never())->method('saveResource');
        $controller->expects($this->once())->method('respondWithJson')->with($errors, ['code' => 400])->will($this->returnValue($response));

        $this->assertSame($response, $controller->create());
    }

    public function testCreateToRespectResourcesScope()
    {
        $response = new Response();
        $data = ['foo' => 'bar'];
        $request = new Request();
        $request->data = $data;
        $errors = [];
        $scope = ['business_id' => 1337];

        $resource = $this->getMock('Cake\ORM\Entity', ['errors']);
        $resource->expects($this->any())->method('errors')->will($this->returnValue($errors));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['newResource', 'saveResource', 'respondWithJson', 'getResourcesSecureScope'], [$request, $response]);
        $controller->expects($this->once())->method('newResource')->with($data, $scope)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('saveResource')->with($resource)->will($this->returnValue(true));
        $controller->expects($this->once())->method('respondWithJson')->with($resource)->will($this->returnValue($response));
        $controller->expects($this->once())->method('getResourcesSecureScope')->will($this->returnValue($scope));

        $this->assertSame($response, $controller->create());
    }

    public function testCreateToCallHooks()
    {
        $response = new Response();
        $data = ['foo' => 'bar'];
        $request = new Request();
        $request->data = $data;
        $errors = [];

        $resource = $this->getMock('Cake\ORM\Entity', ['errors']);
        $resource->expects($this->any())->method('errors')->will($this->returnValue($errors));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['newResource', 'saveResource', 'respondWithJson', 'beforeCreate', 'afterCreate'], [$request, $response]);
        $controller->expects($this->at(0))->method('newResource')->with($data)->will($this->returnValue($resource));
        $controller->expects($this->at(1))->method('beforeCreate')->with($resource);
        $controller->expects($this->at(2))->method('saveResource')->with($resource)->will($this->returnValue(true));
        $controller->expects($this->at(3))->method('afterCreate')->with($resource);
        $controller->expects($this->at(4))->method('respondWithJson')->with($resource)->will($this->returnValue($response));

        $controller->create();
    }

    public function testDestroy()
    {
        $id = 1337;
        $response = new Response();
        $resource = new Entity();

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'deleteResource', 'respondWithJson']);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('deleteResource')->with($resource)->will($this->returnValue(true));
        $controller->expects($this->once())->method('respondWithJson')->with(null)->will($this->returnValue($response));

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

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'deleteResource', 'respondWithJson']);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->never())->method('deleteResource');
        $controller->expects($this->never())->method('respondWithJson');

        $controller->destroy($id);
    }

    public function testDestroyToCallHooks()
    {
        $id = 1337;
        $request = new Request();
        $request->data = ['foo' => 'bar'];
        $response = new Response();
        $resource = new Entity();

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResourceOrThrowNotFoundException', 'deleteResource', 'respondWithJson', 'beforeDelete', 'afterDelete'], [$request, $response]);
        $controller->expects($this->at(0))->method('findResourceOrThrowNotFoundException')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->at(1))->method('beforeDelete')->with($resource);
        $controller->expects($this->at(2))->method('deleteResource')->with($resource)->will($this->returnValue(true));
        $controller->expects($this->at(3))->method('afterDelete')->with($resource);
        $controller->expects($this->at(4))->method('respondWithJson')->with(null)->will($this->returnValue($response));

        $controller->destroy($id);
    }

    public function testIndex()
    {
        $response = new Response();
        $resources = [new Entity(), new Entity()];

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResources', 'respondWithJson']);
        $controller->expects($this->once())->method('findResources')->will($this->returnValue($resources));
        $controller->expects($this->once())->method('respondWithJson')->with($resources)->will($this->returnValue($response));

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

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'patchResource', 'saveResource', 'respondWithJson'], [$request, $response]);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('patchResource')->with($resource, $data);
        $controller->expects($this->once())->method('saveResource')->with($resource)->will($this->returnValue(true));
        $controller->expects($this->once())->method('respondWithJson')->with($resource)->will($this->returnValue($response));

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

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'patchResource', 'saveResource', 'respondWithJson'], [$request, $response]);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('patchResource')->with($resource, $data);
        $controller->expects($this->never())->method('saveResource');
        $controller->expects($this->once())->method('respondWithJson')->with($errors, ['code' => 400])->will($this->returnValue($response));

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

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'patchResource', 'saveResource', 'respondWithJson'], [$request, $response]);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->never())->method('patchResource');
        $controller->expects($this->never())->method('saveResource');
        $controller->expects($this->never())->method('respondWithJson');

        $controller->update($id);
    }

    public function testUpdateToCallHooks()
    {
        $id = 1337;
        $data = ['foo' => 'bar'];
        $request = new Request();
        $request->data = $data;
        $response = new Response();
        $errors = [];

        $resource = $this->getMock('Cake\ORM\Entity', ['errors']);
        $resource->expects($this->any())->method('errors')->will($this->returnValue($errors));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResourceOrThrowNotFoundException', 'patchResource', 'saveResource', 'respondWithJson', 'beforeUpdate', 'afterUpdate'], [$request, $response]);
        $controller->expects($this->at(0))->method('findResourceOrThrowNotFoundException')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->at(1))->method('patchResource')->with($resource, $data);
        $controller->expects($this->at(2))->method('beforeUpdate')->with($resource);
        $controller->expects($this->at(3))->method('saveResource')->with($resource)->will($this->returnValue(true));
        $controller->expects($this->at(4))->method('afterUpdate')->with($resource);
        $controller->expects($this->at(5))->method('respondWithJson')->with($resource)->will($this->returnValue($response));

        $controller->update($id);
    }

    public function testView()
    {
        $id = 1337;
        $response = new Response();
        $resource = new Entity();

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'respondWithJson']);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->once())->method('respondWithJson')->with($resource)->will($this->returnValue($response));

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

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithCrudOperationsTrait', ['findResource', 'respondWithJson']);
        $controller->expects($this->once())->method('findResource')->with($id)->will($this->returnValue($resource));
        $controller->expects($this->never())->method('respondWithJson');

        $controller->view($id);
    }
}
