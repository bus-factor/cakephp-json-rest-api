<?php

// file:   CrudOperationsTrait.php
// date:   2016-01-16
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Controller\Traits;

/**
 * CRUD operations trait for controllers.
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
trait CrudOperationsTrait
{
    use CrudOperationHooksTrait;
    use ResourcesTrait;
    use JsonResponderTrait;

    /**
     * Creates a new resource.
     *
     * @return Cake\Network\Response
     */
    public function create()
    {
        $inaccessibleFields = $this->getResourcesSecureScope();
        $resource = $this->newResource($this->request->data, $inaccessibleFields);

        if (!empty($resource->errors())) {
            return $this->respondWithJson($resource->errors(), ['code' => 400]);
        }

        $this->beforeCreate($resource);
        $this->saveResource($resource);
        $this->afterCreate($resource);

        return $this->respondWithJson($resource);
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

        $this->beforeDelete($resource);
        $this->deleteResource($resource);
        $this->afterDelete($resource);

        return $this->respondWithJson(null);
    }

    /**
     * Returns all resources.
     *
     * @return Cake\Network\Response
     */
    public function index()
    {
        $resources = $this->findResources();

        return $this->respondWithJson($resources);
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
            return $this->respondWithJson($resource->errors(), ['code' => 400]);
        }

        $this->beforeUpdate($resource);
        $this->saveResource($resource);
        $this->afterUpdate($resource);

        return $this->respondWithJson($resource);
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

        return $this->respondWithJson($resource);
    }
}
