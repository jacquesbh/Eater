<?php

/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jacques Bodin-Hullin <j.bodinhullin@monsieurbiz.com>
 * @github https://github.com/jacquesbh/Eater
 */

namespace Jacquesbh\Eater;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;

class Eater implements ArrayAccess, IteratorAggregate, JsonSerializable, Countable, EaterInterface
{
    protected array $data = [];

    public function __construct()
    {
        if (func_num_args()) {
            if (is_array($data = func_get_arg(0))) {
                $this->addData($data);
            }
        }
        call_user_func_array([$this, '_construct'], func_get_args());
    }

    /**
     * Secondary constructor
     * <p>Specially for override :)</p>
     *
     * @access protected
     * @return void
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct(): void
    {
    }


    /**
     * {@inheritdoc}
     */
    public function addData(array $data, bool $recursive = false): self
    {
        if ($data === null || (!is_array($data) && !($data instanceof EaterInterface))) {
            return $this;
        }
        foreach ($data as $key => $value) {
            if ($recursive && is_array($value)) {
                $value = (new Eater)->addData($value, $recursive);
            }
            $this->setData($key, $value);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($name = null, $value = null, bool $recursive = false): self
    {
        if (is_array($name) || $name === null) {
            $this->data = [];
            if (!empty($name)) {
                $this->addData($name, $recursive);
            }
        } else {
            $this->data[$this->format($name)] = $value;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($name = null, $field = null)
    {
        if ($name === null) {
            return $this->data;
        } elseif (array_key_exists($name = $this->format($name), $this->data)) {
            if ($field !== null) {
                return isset($this->data[$name][$field]) ? $this->data[$name][$field] : null;
            }
            return $this->data[$name];
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($name = null)
    {
        return $name === null
            ? !empty($this->data)
            : array_key_exists($this->format($name), $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($name = null)
    {
        if ($name === null) {
            $this->data = [];
        } elseif (array_key_exists($name = $this->format($name), $this->data)) {
            unset($this->data[$name]);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($this->format($offset), $this->data);
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
    public function offsetSet($offset, $value)
    {
        $this->setData($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->unsetData($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function format($str)
    {
        return strtolower(preg_replace('`(.)([A-Z])`', "$1_$2", $str));
    }

    /**
     * {@inheritdoc}
     */
    public function merge($eater)
    {
        if (!$eater instanceof EaterInterface && !is_array($eater)) {
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
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Magic CALL
     *
     * @param string $name Method name
     * @param array $arguments Method arguments
     *
     * @access public
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
                $field = isset($arguments[0]) ? $arguments[0] : null;
                return $this->getData(substr($name, 3), $field);
            case 'has':
                return $this->hasData(substr($name, 3));
            case 'uns':
                $begin = 3;
                if (substr($name, 0, 5) == 'unset') {
                    $begin = 5;
                }
                return $this->unsetData(substr($name, $begin));
        }
    }

    /**
     * Magic SET
     *
     * @param string $name
     * @param mixed $value
     *
     * @access public
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setData($name, $value);
    }

    /**
     * Magic GET
     *
     * @param string $name
     *
     * @access public
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getData($name);
    }

    /**
     * Magic ISSET
     *
     * @param string $name
     *
     * @access public
     *
     * @return mixed
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * Magic TOSTRING
     *
     * @access public
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this, JSON_FORCE_OBJECT);
    }

    /**
     * JsonSerializable::jsonSerialize
     *
     * @access public
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }

    /**
     * Magic SLEEP
     *
     * @access public
     *
     * @return array
     */
    public function __sleep(): array
    {
        return ['data'];
    }
}
