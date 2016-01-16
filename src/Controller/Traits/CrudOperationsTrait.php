<?php

// file:   CrudOperationsTrait.php
// date:   2016-01-16
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Controller\Traits;

use Cake\Network\Exception\NotFoundException;

/**
 * CRUD operations trait for controllers.
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
trait CrudOperationsTrait
{
    use ResourcesTrait;
    use ResponderTrait;

    /**
     * Creates a new resource.
     *
     * @return Cake\Network\Response
     */
    public function create()
    {
        $resource = $this->newResource($this->request->data);

        if (!empty($resource->errors())) {
            return $this->respondWith($resource->errors(), ['code' => 400]);
        }

        $this->saveResource($resource);

        return $this->respondWith($resource);
    }

    /**
     * Deletes a resource.
     *
     * @param mixed $id
     *
     * @return Cake\Network\Response
     *
     * @throws Cake\Network\Exception\NotFoundException
     */
    public function destroy($id)
    {
        $resource = $this->findResourceOrThrowNotFoundException($id);

        $this->deleteResource($resource);

        return $this->respondWith(null);
    }

    /**
     * Returns all resources.
     *
     * @return Cake\Network\Response
     */
    public function index()
    {
        $resources = $this->findResources();

        return $this->respondWith($resources);
    }

    /**
     * Updates a resource.
     *
     * @param mixed $id
     *
     * @return Cake\Network\Response
     *
     * @throws Cake\Network\Exception\NotFoundException
     */
    public function update($id)
    {
        $resource = $this->findResourceOrThrowNotFoundException($id);

        $this->patchResource($resource, $this->request->data);

        if (!empty($resource->errors())) {
            return $this->respondWith($resource->errors(), ['code' => 400]);
        }

        $this->saveResource($resource);

        return $this->respondWith($resource);
    }

    /**
     * Returns a resource.
     *
     * @param mixed $id
     *
     * @return Cake\Network\Response
     *
     * @throws Cake\Network\Exception\NotFoundException
     */
    public function view($id)
    {
        $resource = $this->findResourceOrThrowNotFoundException($id);

        return $this->respondWith($resource);
    }
}
