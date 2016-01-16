<?php

// file:   ResourcesTrait.php
// date:   2016-01-15
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Controller\Traits;

use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use RuntimeException;

/**
 * Resources trait for controllers.
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
trait ResourcesTrait
{
    /**
     * Deletes a resource.
     *
     * @param Cake\ORM\Entity $resource
     *
     * @return bool
     */
    public function deleteResource(Entity $resource)
    {
        return $this->getResourcesTable()->delete($resource);
    }

    /**
     * Finds a resource by primary key.
     *
     * @param mixed $pkValue
     *
     * @return Cake\ORM\Entity
     */
    public function findResource($pkValue)
    {
        $pk = $this->getResourcesTable()->primaryKey();

        return $this->getResourcesTableQuery()->where([$pk => $pkValue])->first();
    }

    /**
     * Finds a resource, or throws a not-found exception.
     *
     * @param mixed $id
     *
     * @return Cake\ORM\Entity
     *
     * @throws Cake\Network\Exception\NotFoundException
     */
    public function findResourceOrThrowNotFoundException($id)
    {
        $resource = $this->findResource($id);

        if (!$resource) {
            throw new NotFoundException();
        }

        return $resource;
    }

    /**
     * Finds all resources.
     *
     * @return array
     */
    public function findResources()
    {
        return $this->getResourcesTableQuery()->toArray();
    }

    /**
     * Returns a resources option.
     *
     * @param mixed $path
     * @param mixed $defaultValue
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function getResourcesOption($path, $defaultValue = null)
    {
        if (!isset($this->resourcesOptions)) {
            throw new RuntimeException('No $resourcesOptions defined');
        }

        return Hash::get($this->resourcesOptions, $path, $defaultValue);
    }

    /**
     * Returns the table for the resources.
     *
     * @return Cake\ORM\Table
     */
    public function &getResourcesTable()
    {
        $resourcesName = $this->getResourcesOption('name');

        if (!isset($this->{$resourcesName})) {
            $this->{$resourcesName} = TableRegistry::get($resourcesName);
        }

        return $this->{$resourcesName};
    }

    /**
     * Returns a query object for resource's table.
     *
     * @return Cake\ORM\Query
     */
    public function getResourcesTableQuery()
    {
        return $this->getResourcesTable()->find('all');
    }

    /**
     * Returns a new resource.
     *
     * @param array $data
     *
     * @return Cake\ORM\Entity
     */
    public function newResource(array $accessibleFields = [], array $inaccessibleFields = [])
    {
        $resource = $this->getResourcesTable()->newEntity($accessibleFields);

        foreach ($inaccessibleFields as $key => $value) {
            $resource->{$key} = $value;
        }

        $this->validateResource($resource);

        return $resource;
    }

    /**
     * Patches a resource.
     *
     * @param Cake\ORM\Entity $entity
     * @param array $accessibleFields
     *
     * @return Cake\ORM\Entity
     */
    public function patchResource(Entity $resource, array $accessibleFields)
    {
        $this->getResourcesTable()->patchEntity($resource, $accessibleFields, ['validate' => false]);
        $this->validateResource($resource);

        return $resource;
    }

    /**
     * Saves a resource.
     *
     * @param Cake\ORM\Entity $resource
     *
     * @return Cake\ORM\Entity|bool
     */
    public function saveResource(Entity $resource)
    {
        return $this->getResourcesTable()->save($resource);
    }

    /**
     * Validates a resource and returns the validation errors.
     *
     * @param Cake\ORM\Entity $resource
     *
     * @return array
     */
    public function validateResource(Entity $resource)
    {
        $errors = $this->getResourcesTable()->validator('default')->errors($resource->toArray());

        $resource->errors($errors, null, true);

        return $errors;
    }
}
