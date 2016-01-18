<?php

// file:   ErrorController.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Jra\Controller\Traits\ResponderTrait;

/**
 * Error controller class.
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
class ErrorController extends Controller
{
    use ResponderTrait;

    /**
     * Before render hook method.
     *
     * @param Cake\Event\Event
     */
    public function beforeRender(Event $event)
    {
        return $this->respondWith(null, [
            'code' => $this->viewVars['error']->getCode(),
            'message' => $this->viewVars['message']
        ]);
    }
}
