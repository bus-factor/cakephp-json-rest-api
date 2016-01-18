<?php

// file:   ExceptionRenderer.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Error;

use Cake\Error\ExceptionRenderer as ExceptionRendererBase;
use Cake\Network\Response;
use Jra\Controller\ErrorController;

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
     * @return Jra\Controller\ErrorController
     */
    protected function _getController()
    {
        return new ErrorController(null, new Response());
    }
}
