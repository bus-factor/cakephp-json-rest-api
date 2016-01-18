<?php

// file:   OptionsTraitTest.php
// date:   2016-01-17
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Test\Unit\Controller\Traits;

use PHPUnit_Framework_TestCase;
use Jra\Test\Dummy\Controller\ControllerWithOptionsTrait;

class OptionsTraitTest extends PHPUnit_Framework_TestCase
{
    public function testGetOptions()
    {
        $controller = new ControllerWithOptionsTrait();

        $this->assertSame($controller->jraOptions, $controller->getJraOptions());
    }

    public function testGetJraOptionsToHandleMissingDefinition()
    {
        $controller = new ControllerWithOptionsTrait();
        unset($controller->jraOptions);

        $this->assertEquals([], $controller->getJraOptions());
    }

    public function testGetJraOption()
    {
        $options = ['foo' => 'bar'];

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithOptionsTrait', ['getJraOptions']);
        $controller->expects($this->once())->method('getJraOptions')->will($this->returnValue($options));

        $this->assertSame('bar', $controller->getJraOption('foo'));
    }

    public function testGetJraOptionWhenNested()
    {
        $options = ['foo' => ['bar' => 'fiz']];

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithOptionsTrait', ['getJraOptions']);
        $controller->expects($this->once())->method('getJraOptions')->will($this->returnValue($options));

        $this->assertSame('fiz', $controller->getJraOption('foo.bar'));
    }

    public function testHasJraOption()
    {
        $options = ['foo' => 'bar'];

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithOptionsTrait', ['getJraOptions']);
        $controller->expects($this->once())->method('getJraOptions')->will($this->returnValue($options));

        $this->assertTrue($controller->hasJraOption('foo'));
    }

    public function testHasJraOptionWhenNested()
    {
        $options = ['foo' => ['bar' => 'fiz']];

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithOptionsTrait', ['getJraOptions']);
        $controller->expects($this->once())->method('getJraOptions')->will($this->returnValue($options));

        $this->assertTrue($controller->hasJraOption('foo.bar'));
    }

    public function testHasJraOptionWhenNotPresent()
    {
        $options = [];

        $controller = $this->getMock('Jra\Test\Dummy\Controller\ControllerWithOptionsTrait', ['getJraOptions']);
        $controller->expects($this->once())->method('getJraOptions')->will($this->returnValue($options));

        $this->assertFalse($controller->hasJraOption('foo'));
    }

    public function testSetJraOption()
    {
        $options = ['foo' => 'bar', 'fiz' => 'baz'];

        $controller = new ControllerWithOptionsTrait();
        $controller->jraOptions = $options;

        $controller->setJraOption('foo', 'fiz');

        $this->assertEquals(['foo' => 'fiz', 'fiz' => 'baz'], $controller->getJraOptions());
    }

    public function testSetJraOptionWhenNested()
    {
        $options = ['foo' => ['bar' => ['fiz' => 'baz']]];

        $controller = new ControllerWithOptionsTrait();
        $controller->jraOptions = $options;

        $controller->setJraOption('foo.bar.fiz', 'baaah');

        $this->assertEquals(['foo' => ['bar' => ['fiz' => 'baaah']]], $controller->getJraOptions());
    }

    public function testSetJraOptionWhenNotDefined()
    {
        $controller = new ControllerWithOptionsTrait();
        unset($controller->jraOptions);

        $controller->setJraOption('foo.bar.fiz', 'baaah');

        $this->assertEquals(['foo' => ['bar' => ['fiz' => 'baaah']]], $controller->getJraOptions());
    }
}
