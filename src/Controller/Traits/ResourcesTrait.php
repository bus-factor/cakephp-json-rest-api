<?php

// file:   ResourcesTrait.php
// date:   2016-01-15
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Controller\Traits;

use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use RuntimeException;

/**
 * Resources trait for controllers.
 *
 * This trait requires the following JSON REST API options:
 *
 *      public $jraOptions = [
 *          'pagination' => [
 *              'limit' => 50
 *          ],
 *          'secure' => [
 *              'user_id' => 'getCurrentUserId'
 *          ],
 *          'table' => 'Users'
 *      ];
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
trait ResourcesTrait
{
    use OptionsTrait;

    /**
     * Deletes a resource.
     *
     * @param Cake\ORM\Entity $resource
     *
     * @return bool
     */
    public function deleteResource(Entity &$resource)
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

        return $this->getResourcesQuery()->where([$pk => $pkValue])->first();
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
     * @param array $pagination
     *
     * @return array
     */
    public function findResources(array $pagination)
    {
        $query = $this->getResourcesQuery();
        $query = $query->limit($pagination['limit'])->page($pagination['page']);

        return $query->toArray();
    }

    /**
     * Returns the resources secured scope.
     *
     * @return array
     */
    public function getResourcesSecureScope()
    {
        if (!$this->hasJraOption('secure')) {
            return [];
        }

        $scope = $this->getJraOption('secure');

        foreach ($scope as $column => &$method) {
            $method = $this->{$method}();
        }

        return $scope;
    }

    /**
     * Returns the table for the resources.
     *
     * @return Cake\ORM\Table
     */
    public function &getResourcesTable()
    {
        if (!$this->hasJraOption('table')) {
            $this->setJraOption('table', $this->modelClass);
        }

        $table = $this->getJraOption('table');

        if (!isset($this->{$table})) {
            $this->loadModel($table);
        }

        return $this->{$table};
    }

    /**
     * Returns a query object for resource's table.
     *
     * @return Cake\ORM\Query
     */
    public function getResourcesQuery()
    {
        $query = $this->getResourcesTable()->find('all');
        $scope = $this->getResourcesSecureScope();

        return empty($scope) ? $query : $query->where($scope);
    }

    /**
     * Returns the resource validator.
     *
     * @return Cake\Validation\Validator
     */
    public function getResourceValidator()
    {
        return $this->getResourcesTable()->validator('default');
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
    public function patchResource(Entity &$resource, array $accessibleFields)
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
    public function saveResource(Entity &$resource)
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
    public function validateResource(Entity &$resource)
    {
        $errors = $this->getResourceValidator()->errors($resource->toArray());

        $resource->errors($errors, null, true);

        return $errors;
    }
}
