<?php

// file:   ErrorController.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Jra\Controller\Traits\JsonResponderTrait;

/**
 * Error controller class.
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
class ErrorController extends Controller
{
    use JsonResponderTrait;

    /**
     * Before render hook method.
     *
     * @param Cake\Event\Event
     */
    public function beforeRender(Event $event)
    {
        return $this->respondWithJson(null, [
            'code' => $this->viewVars['error']->getCode(),
            'message' => $this->viewVars['message']
        ]);
    }
}
