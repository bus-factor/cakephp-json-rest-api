<?php

// file:   OptionsTrait.php
// date:   2016-01-17
// author: Michael Leßnau <michael.lessnau@gmail.com>

namespace Jra\Controller\Traits;

use Cake\Utility\Hash;

/**
 * JSON REST API options.
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 */
trait OptionsTrait
{
    /**
     * Returns the JSON REST API options.
     *
     * @return array
     */
    public function &getJraOptions()
    {
        if (!isset($this->jraOptions)) {
            $this->jraOptions = [];
        }

        return $this->jraOptions;
    }

    /**
     * Returns a JSON REST API option.
     *
     * @param mixed $path
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function getJraOption($path, $defaultValue = null)
    {
        return Hash::get($this->getJraOptions(), $path, $defaultValue);
    }

    /**
     * Checks if a particular option is available.
     *
     * @param string $path
     *
     * @return bool
     */
    public function hasJraOption($path)
    {
        return Hash::check($this->getJraOptions(), $path);
    }

    /**
     * Sets a JSON REST API option.
     *
     * @param mixed $path
     * @param mixed $value
     */
    public function setJraOption($path, $value)
    {
        if (!isset($this->jraOptions)) {
            $this->jraOptions = [];
        }

        $this->jraOptions = Hash::insert($this->jraOptions, $path, $value);
    }
}
