<?php

namespace Jacquesbh\Eater;

use ArrayAccess;
use Countable;
use Iterator;
use IteratorAggregate;
use JsonSerializable;

interface EaterInterface extends ArrayAccess, IteratorAggregate, JsonSerializable, Countable
{
    /**
     * @param array|EaterInterface|null $data
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addData($data, bool $recursive = false): self;

    /**
     * @param mixed $name
     * @param mixed $value
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setData($name = null, $value = null, bool $recursive = false): self;

    /**
     * @return mixed
     */
    public function getData(string $name = null, string $field = null);

    public function hasData(string $name = null): bool;

    public function unsetData(string $name = null): self;

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool;

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset);

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void;

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void;

    /**
     * Format a string for storage
     */
    public function format(string $str): string;

    /**
     * Merge an other Eater (or array)
     *
     * @param EaterInterface|array $eater
     */
    public function merge($eater): self;

    /**
     * Retrun a new external Iterator, used internally for foreach loops.
     */
    public function getIterator(): Iterator;

    /**
     * Retrun the number of datas contained in the current Eater object.
     * This does not include datas contained by child Eater instances.
     */
    public function count(): int;
}
