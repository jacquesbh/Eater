<?php

namespace Jacquesbh\Eater;

use Iterator;

interface EaterInterface
{
    /**
     * @param array|EaterInterface|null $data
     * @param bool $recursive
     * @access public
     *
     * @return EaterInterface
     */
    public function addData($data, $recursive = false);

    /**
     * @param mixed $name
     * @param mixed $value
     * @param bool $recursive
     *
     * @return EaterInterface
     */
    public function setData($name = null, $value = null, $recursive = false);

    /**
     * @param string $name
     * @param string $field
     *
     * @return mixed
     */
    public function getData($name = null, $field = null);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasData($name = null);

    /**
     * @param string $name
     *
     * @return EaterInterface
     */
    public function unsetData($name = null);

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset);

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset);

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value);

    /**
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset);

    /**
     * Format a string for storage
     *
     * @param string $str
     *
     * @return string
     */
    public function format($str);

    /**
     * Merge an other Eater (or array)
     *
     * @param EaterInterface|array $eater
     *
     * @return EaterInterface
     */
    public function merge($eater);

    /**
     * Retrun a new external @a Iterator, used internally for foreach loops.
     *
     * @access public
     *
     * @return Iterator
     */
    public function getIterator();

    /**
     * Retrun the number of datas contained in the current @a Eater object.
     * This does not include datas contained by child @a Eater instances.
     *
     * @access public
     *
     * @return int
     */
    public function count();
}
