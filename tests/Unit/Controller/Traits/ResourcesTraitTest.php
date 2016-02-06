<?php

// file:   ResourcesTraitTest.php
// date:   2016-01-15
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Unit\Controller\Traits;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Jra\Test\Dummy\Controller\ControllerWithResourcesTrait;
use PHPUnit_Framework_TestCase;

class ResourcesTraitTest extends PHPUnit_Framework_TestCase
{
    public function testDeleteResource()
    {
        $resource = new Entity();
        $retVal = 'uargh';

        $table = $this->getTableMock(['delete']);
        $table->expects($this->once())->method('delete')->with($resource)->will($this->returnValue($retVal));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));

        $this->assertSame($retVal, $controller->deleteResource($resource));
    }

    public function testFindResource()
    {
        $pk = 'foo';
        $pkValue = 'bar';
        $resource = new Entity();

        $table = $this->getTableMock(['primaryKey']);
        $table->expects($this->once())->method('primaryKey')->will($this->returnValue($pk));

        $query = $this->getMock('Cake\ORM\Query', ['where', 'first'], [null, $table]);
        $query->expects($this->once())->method('where')->with([$pk => $pkValue])->will($this->returnValue($query));
        $query->expects($this->once())->method('first')->will($this->returnValue($resource));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable', 'getResourcesQuery']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));
        $controller->expects($this->once())->method('getResourcesQuery')->will($this->returnValue($query));

        $this->assertSame($resource, $controller->findResource($pkValue));
    }

    public function testFindResources()
    {
        $limit = 1337;
        $page = 42;
        $pagination = ['limit' => $limit, 'page' => $page];

        $resources = [new Entity(), new Entity()];
        $table = $this->getTableMock();

        $query = $this->getMock('Cake\ORM\Query', ['toArray', 'limit', 'page'], [null, $table]);
        $query->expects($this->at(0))->method('limit')->with($limit)->will($this->returnValue($query));
        $query->expects($this->at(1))->method('page')->with($page)->will($this->returnValue($query));
        $query->expects($this->at(2))->method('toArray')->will($this->returnValue($resources));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesQuery']);
        $controller->expects($this->once())->method('getResourcesQuery')->will($this->returnValue($query));

        $this->assertSame($resources, $controller->findResources($pagination));
    }

    /**
     * @dataProvider provideTestGetResourcesTableData
     *
     * @param string $table
     */
    public function testGetResourcesTable($table)
    {
        $controller = new ControllerWithResourcesTrait(null, null);
        $controller->setJraOption('table', $table);
        $tableInstance = $controller->getResourcesTable();

        $this->assertInstanceOf('Cake\ORM\Table', $tableInstance);
        $this->assertSame($table, $tableInstance->alias());
    }

    public function provideTestGetResourcesTableData()
    {
        return [
            ['Users'],
            ['Businesses']
        ];
    }

    public function testGetResourcesTableWhenNoOptionProvided()
    {
        $controller = new ControllerWithResourcesTrait(null, null);
        $controller->modelClass = 'Messages';
        unset($controller->jraOptions);

        $tableInstance = $controller->getResourcesTable();

        $this->assertInstanceOf('Cake\ORM\Table', $tableInstance);
        $this->assertSame('Messages', $tableInstance->alias());
        $this->assertSame('Messages', $controller->getJraOption('table'));
    }

    public function testGetResourcesQuery()
    {
        $table = $this->getTableMock(['find']);

        $query = new Query(null, $table);

        $table->expects($this->once())->method('find')->with('all')->will($this->returnValue($query));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable']);
        $controller->setJraOption('secure', []);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));

        $this->assertSame($query, $controller->getResourcesQuery());
    }

    public function testGetResourcesQueryToRespectSecureScope()
    {
        $table = $this->getTableMock(['find']);

        $query = $this->getMock('Cake\ORM\Query', ['where'], [null, $table]);
        $query->expects($this->once())->method('where')->with(['business_id' => 1337])->will($this->returnValue($query));

        $table->expects($this->once())->method('find')->with('all')->will($this->returnValue($query));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));

        $this->assertSame($query, $controller->getResourcesQuery());
    }

    public function testGetResourceValidator()
    {
        $validator = new Validator();

        $table = $this->getTableMock(['validator']);
        $table->expects($this->once())->method('validator')->with('default')->will($this->returnValue($validator));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));

        $this->assertSame($validator, $controller->getResourceValidator());
    }

    public function testNewResource()
    {
        $accessibleFields = ['foo' => 'bar'];
        $inaccessibleFields = ['fiz' => 'baz'];
        $resource = new Entity();
        $resource->foo = 'bar';
        $resource->fiz = 'baz';

        $table = $this->getTableMock(['newEntity']);
        $table->expects($this->once())->method('newEntity')->with($accessibleFields)->will($this->returnValue($resource));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable', 'validateResource']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));
        $controller->expects($this->once())->method('validateResource')->with($resource);

        $this->assertSame($resource, $controller->newResource($accessibleFields, $inaccessibleFields));
    }

    public function testPatchResource()
    {
        $resource = new Entity();
        $accessibleFields = ['foo' => 'bar'];
        $options = ['validate' => false];

        $table = $this->getTableMock(['patchEntity']);
        $table->expects($this->once())->method('patchEntity')->with($resource, $accessibleFields, $options)->will($this->returnValue($resource));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable', 'validateResource']);
        $controller->expects($this->once())->method('getResourcesTable')->will($this->returnValue($table));
        $controller->expects($this->once())->method('validateResource')->with($resource);

        $this->assertSame($resource, $controller->patchResource($resource, $accessibleFields));
    }

    public function testSaveResource()
    {
        $resource = new Entity();

        $table = $this->getTableMock(['save']);
        $table->expects($this->once())->method('save')->with($resource)->will($this->returnValue($resource));

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourcesTable']);
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

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithResourcesTrait', ['getResourceValidator']);
        $controller->expects($this->once())->method('getResourceValidator')->will($this->returnValue($validator));

        $this->assertEquals($errors, $controller->validateResource($resource));
    }

    protected function getTableMock(array $methods = [])
    {
        $connection = ConnectionManager::get('test');

        $schema = [
            'id' => ['type' => 'integer'],
            'email' => ['type' => 'string'],
            'business_id' => ['type' => 'integer']
        ];

        $options = [
            'table' => 'users',
            'connection' => $connection,
            'schema' => $schema
        ];

        if (empty($methods)) {
            return new Table($options);
        }

        return $this->getMock('Cake\ORM\Table', $methods, [$options]);
    }
}
