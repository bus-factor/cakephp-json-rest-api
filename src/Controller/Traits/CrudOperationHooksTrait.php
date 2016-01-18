<?php

// file:   CrudOperationHooksTrait.php
// date:   2016-01-18
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Controller\Traits;

use Cake\ORM\Entity;

/**
 * JSON REST API CRUD operation hooks controller trait.
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
trait CrudOperationHooksTrait
{
    /**
     * Before create hook method.
     *
     * @param Cake\ORM\Entity
     */
    public function beforeCreate(Entity &$resource)
    {
    }

    /**
     * After create hook method.
     *
     * @param Cake\ORM\Entity
     */
    public function afterCreate(Entity &$resource)
    {
    }

    /**
     * Before delete hook method.
     *
     * @param Cake\ORM\Entity
     */
    public function beforeDelete(Entity &$resource)
    {
    }

    /**
     * After delete hook method.
     *
     * @param Cake\ORM\Entity
     */
    public function afterDelete(Entity &$resource)
    {
    }

    /**
     * Before update hook method.
     *
     * @param Cake\ORM\Entity
     */
    public function beforeUpdate(Entity &$resource)
    {
    }

    /**
     * After update hook method.
     *
     * @param Cake\ORM\Entity
     */
    public function afterUpdate(Entity &$resource)
    {
    }
}
