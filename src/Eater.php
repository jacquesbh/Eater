<?php

/*
 * This file is part of the Eater library.
 *
 * (c) Jacques Bodin-Hullin <j.bodinhullin@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Jacquesbh\Eater;

use ArrayIterator;
use Iterator;

class Eater implements EaterInterface
{
    protected array $data = [];

    public function __construct()
    {
        if (\func_num_args()) {
            if (\is_array($data = func_get_arg(0))) {
                $this->addData($data);
            }
        }
        \call_user_func_array([$this, '_construct'], \func_get_args());
    }

    /**
     * Secondary constructor
     * Specially for override :).
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct(): void
    {
    }


    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addData($data, bool $recursive = false): self
    {
        if (null === $data || (!\is_array($data) && !($data instanceof EaterInterface))) {
            return $this;
        }
        foreach ($data as $key => $value) {
            if ($recursive && \is_array($value)) {
                $value = (new self())->addData($value, $recursive);
            }
            $this->setData($key, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function setData($name = null, $value = null, bool $recursive = false): self
    {
        if (\is_array($name) || null === $name) {
            $this->data = [];
            if (!empty($name)) {
                $this->addData($name, $recursive);
            }
        } else {
            $this->data[$this->format((string) $name)] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($name = null, $field = null)
    {
        if (null === $name) {
            return $this->data;
        }
        if (\array_key_exists($name = $this->format($name), $this->data)) {
            if (null !== $field) {
                return $this->data[$name][$field] ?? null;
            }

            return $this->data[$name];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasData(string $name = null): bool
    {
        return null === $name
            ? !empty($this->data)
            : \array_key_exists($this->format($name), $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData(string $name = null): self
    {
        if (null === $name) {
            $this->data = [];
        } elseif (\array_key_exists($name = $this->format($name), $this->data)) {
            unset($this->data[$name]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return \array_key_exists($this->format($offset), $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->getData($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->setData($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        $this->unsetData($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function format(string $str): string
    {
        return strtolower(preg_replace('`(.)([A-Z])`', '$1_$2', $str));
    }

    /**
     * {@inheritdoc}
     */
    public function merge($eater): self
    {
        if (!$eater instanceof EaterInterface && !\is_array($eater)) {
            throw new InvalidArgumentException('Only array or Eater are expected for merge.');
        }

        return $this->setData(
            array_merge_recursive(
                $this->getData(),
                ($eater instanceof EaterInterface) ? $eater->getData() : $eater
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return \count($this->data);
    }

    /**
     * Magic CALL.
     *
     * @param string $name Method name
     * @param array $arguments Method arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        switch ($prefix) {
            case 'set':
                return $this->setData(substr($name, 3), !isset($arguments[0]) ? null : $arguments[0]);
            case 'get':
                $field = $arguments[0] ?? null;

                return $this->getData(substr($name, 3), $field);
            case 'has':
                return $this->hasData(substr($name, 3));
            case 'uns':
                $begin = 3;
                if ('unset' == substr($name, 0, 5)) {
                    $begin = 5;
                }

                return $this->unsetData(substr($name, $begin));
        }
    }

    /**
     * @param mixed $value
     */
    public function __set($name, $value): void
    {
        $this->setData($name, $value);
    }

    /**
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->getData($name);
    }

    public function __isset(string $name): bool
    {
        return $this->offsetExists($name);
    }

    /**
     * Magic TOSTRING.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this, \JSON_FORCE_OBJECT);
    }

    /**
     * JsonSerializable::jsonSerialize.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }

    /**
     * Magic SLEEP.
     *
     * @return array
     */
    public function __sleep(): array
    {
        return ['data'];
    }
}
