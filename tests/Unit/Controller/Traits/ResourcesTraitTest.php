<?php

// file:   ResourcesTraitTest.php
// date:   2016-01-15
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Test\Unit\Controller\Traits;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\Table;
use JsonRestApi\Test\Dummy\Controller\ControllerWithResourcesTrait;
use PHPUnit_Framework_TestCase;

class ResourcesTraitTest extends PHPUnit_Framework_TestCase
{
    public function testDeleteResource()
    {
        $resource = new Entity();
        $retVal = 'uargh';

        $table = $this->getMock('Cake\ORM\Table', ['delete']);
        $table->expects($this->once())->method('delete')->with($resource)->will($this->returnValue($retVal));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));

        $this->assertSame($retVal, $controller->deleteResource($resource));
    }

    public function testFindResource()
    {
        $pk = 'foo';
        $pkValue = 'bar';
        $resource = new Entity();

        $connection = ConnectionManager::get('test');
        $schema = ['id' => ['type' => 'integer'], 'email' => ['type' => 'string']];

        $table = $this->getMock('Cake\ORM\Table', ['primaryKey'], [['table' => 'users', 'connection' => $connection, 'schema' => $schema]]);
        $table->expects($this->once())->method('primaryKey')->will($this->returnValue($pk));

        $query = $this->getMock('Cake\ORM\Query', ['where', 'first'], [null, $table]);
        $query->expects($this->once())->method('where')->with([$pk => $pkValue])->will($this->returnValue($query));
        $query->expects($this->once())->method('first')->will($this->returnValue($resource));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable', 'getResourcesTableQuery']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));
        $controller->expects($this->once())->method('getResourcesTableQuery')->will($this->returnValue($query));

        $this->assertSame($resource, $controller->findResource($pkValue));
    }

    public function testFindResources()
    {
        $resources = [new Entity(), new Entity()];
        $connection = ConnectionManager::get('test');
        $schema = ['id' => ['type' => 'integer'], 'email' => ['type' => 'string']];

        $table = new Table(['table' => 'users', 'connection' => $connection, 'schema' => $schema]);

        $query = $this->getMock('Cake\ORM\Query', ['toArray'], [null, $table]);
        $query->expects($this->once())->method('toArray')->will($this->returnValue($resources));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTableQuery']);
        $controller->expects($this->once())->method('getResourcesTableQuery')->will($this->returnValue($query));

        $this->assertSame($resources, $controller->findResources());
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage No $resourcesOptions defined
     */
    public function testGetResourcesOptionsToHandleMissingOptionsDefinition()
    {
        $controller = new ControllerWithResourcesTrait(null, null);
        unset($controller->resourcesOptions);

        $controller->getResourcesOption('name');
    }

    public function testGetResourcesOptionToReturnDefaultValue()
    {
        $defaultValue = 'fallback value';
        $controller = new ControllerWithResourcesTrait(null, null);

        $this->assertSame($defaultValue, $controller->getResourcesOption('foo', $defaultValue));
    }

    public function testGetResourcesOptionToReturnActualValue()
    {
        $defaultValue = 'fallback value';
        $controller = new ControllerWithResourcesTrait(null, null);

        $this->assertSame($controller->resourcesOptions['name'], $controller->getResourcesOption('name', $defaultValue));
    }

    /**
     * @dataProvider provideTestGetResourcesTableData
     *
     * @param string $resourcesName
     */
    public function testGetResourcesTable($resourcesName)
    {
        $controller = new ControllerWithResourcesTrait(null, null);
        $controller->resourcesOptions['name'] = $resourcesName;
        $table = $controller->getResourcesTable();

        $this->assertInstanceOf('Cake\ORM\Table', $table);
        $this->assertSame($resourcesName, $table->alias());
    }

    public function provideTestGetResourcesTableData()
    {
        return [
            ['Users'],
            ['Businesses']
        ];
    }

    public function testGetResourcesTableQuery()
    {
        $connection = ConnectionManager::get('test');
        $schema = ['id' => ['type' => 'integer'], 'email' => ['type' => 'string']];

        $table = $this->getMock('Cake\ORM\Table', ['find'], [['table' => 'users', 'connection' => $connection, 'schema' => $schema]]);

        $query = new Query(null, $table);

        $table->expects($this->once())->method('find')->with('all')->will($this->returnValue($query));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));

        $this->assertSame($query, $controller->getResourcesTableQuery());
    }

    public function testNewResource()
    {
        $accessibleFields = ['foo' => 'bar'];
        $inaccessibleFields = ['fiz' => 'baz'];
        $resource = new Entity();
        $resource->foo = 'bar';
        $resource->fiz = 'baz';

        $table = $this->getMock('Cake\ORM\Table', ['newEntity']);
        $table->expects($this->once())->method('newEntity')->with($accessibleFields)->will($this->returnValue($resource));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable', 'validateResource']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));
        $controller->expects($this->once())->method('validateResource')->with($resource);

        $this->assertSame($resource, $controller->newResource($accessibleFields, $inaccessibleFields));
    }

    public function testPatchResource()
    {
        $resource = new Entity();
        $accessibleFields = ['foo' => 'bar'];
        $options = ['validate' => false];

        $table = $this->getMock('Cake\ORM\Table', ['patchEntity']);
        $table->expects($this->once())->method('patchEntity')->with($resource, $accessibleFields, $options)->will($this->returnValue($resource));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable', 'validateResource']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));
        $controller->expects($this->once())->method('validateResource')->with($resource);

        $this->assertSame($resource, $controller->patchResource($resource, $accessibleFields));
    }

    public function testSaveResource()
    {
        $resource = new Entity();

        $table = $this->getMock('Cake\ORM\Table', ['save']);
        $table->expects($this->once())->method('save')->with($resource)->will($this->returnValue($resource));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));

        $this->assertSame($resource, $controller->saveResource($resource));
    }

    public function testValidateResource()
    {
        $resourceAsArray = ['foo' => 'bar'];
        $errors = ['foo' => 'Cannot be left empty'];

        $resource = $this->getMock('Cake\ORM\Entity', ['toArray', 'errors']);
        $resource->expects($this->once())->method('toArray')->will($this->returnValue($resourceAsArray));
        $resource->expects($this->once())->method('errors')->with($errors, null, true);

        $validator = $this->getMock('Cake\Validation\Validator', ['errors']);
        $validator->expects($this->once())->method('errors')->with($resourceAsArray)->will($this->returnValue($errors));

        $table = $this->getMock('Cake\ORM\Table', ['validator']);
        $table->expects($this->once())->method('validator')->with('default')->will($this->returnValue($validator));

        $controller = $this->getMock('JsonRestApi\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));

        $this->assertEquals($errors, $controller->validateResource($resource));
    }
}
