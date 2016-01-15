<?php

// file:   ExceptionRenderer.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Error;

use Cake\Error\ExceptionRenderer as ExceptionRendererBase;
use Cake\Network\Response;
use JsonRestApi\Controller\ErrorController;

/**
 * Exception renderer class.
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
class ExceptionRenderer extends ExceptionRendererBase
{
    /**
     * Returns the error controller instance.
     *
     * @return JsonRestApi\Controller\ErrorController
     */
    protected function _getController()
    {
        $response = new Response();

        return new ErrorController(null, $response);
    }
}
