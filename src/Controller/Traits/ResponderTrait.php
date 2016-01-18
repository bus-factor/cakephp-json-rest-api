<?php

// file:   ResponderTrait.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Controller\Traits;

use Cake\Utility\Hash;

/**
 * Responder trait for controllers.
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
trait ResponderTrait
{
    /**
     * Responds with JSON formatted data.
     *
     * @param mixed $data
     * @param array $options Valid keys are 'code' and 'message'.
     *
     * @return Cake\Network\Response
     */
    public function respondWith($data, array $options = [])
    {
        $code = Hash::get($options, 'code', 200);
        $status = ($code < 400) ? 'success' : 'failure';
        $message = Hash::get($options, 'message', null);
        $dataField = ($status === 'success') ? 'data' : 'errors';

        $json = json_encode([
            'status' => $status,
            'code' => $code,
            $dataField => $data,
            'message' => $message
        ]);

        $this->response->type('json');
        $this->response->statusCode($code);
        $this->response->body($json);

        return $this->response;
    }
}
