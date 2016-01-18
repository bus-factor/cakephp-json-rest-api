<?php

// file:   CrudOperationHooksTraitTest.php
// date:   2016-01-18
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Unit\Controller\Traits;

use PHPUnit_Framework_TestCase;
use Jra\Test\Dummy\Controller\ControllerWithCrudOperationHooksTrait;

class CrudOperationHooksTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestPresenceOfMethodsData
     */
    public function testPresenceOfMethods($method)
    {
        $controller = new ControllerWithCrudOperationHooksTrait(null, null);

        $this->assertTrue(method_exists($controller, $method));
    }

    public function provideTestPresenceOfMethodsData()
    {
        return [
            ['beforeCreate'],
            ['afterCreate'],
            ['beforeDelete'],
            ['afterDelete'],
            ['beforeUpdate'],
            ['afterUpdate']
        ];
    }
}
