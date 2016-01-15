<?php

// file:   ResponderTrait.php
// date:   2016-01-12
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace JsonRestApi\Controller\Traits;

use Cake\Utility\Hash;

/**
 * ResponderTrait feature for controllers.
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
trait ResponderTrait
{
    /**
     * Responds with JSON formatted data.
     *
     * @param mixed $data
     * @param array $options
     *
     * @return Cake\Network\Response
     */
    public function respondWith($data, array $options = [])
    {
        $message = Hash::get($options, 'message', null);
        $code = Hash::get($options, 'code', 200);
        $status = ($code < 400) ? 'success' : 'failure';

        $json = json_encode([
            'status' => $status,
            'code' => $code,
            ($status === 'success' ? 'data' : 'errors') => $data,
            'message' => $message
        ]);

        $this->response->type('json');
        $this->response->statusCode($code);
        $this->response->body($json);

        return $this->response;
    }
}
