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
 * @author Jacques Bodin-Hullin <jacques@bodin-hullin.net>
 * @github http://github.com/jacquesbh/Eater
 */

/**
 * @namespace
 */
namespace Jacquesbh\Eater;

/**
 * Eater class
 */
class Eater implements \ArrayAccess, \Iterator, \JsonSerializable
{

    /**
     * Data
     *
     * @access protected
     * @var array
     */
    protected $_data = [];

    /**
     * Constructor :)
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        if (func_num_args()) {
            if (is_array($data = func_get_arg(0))) {
                $this->addData($data);
            }
        }
        $this->_construct();
    }

    /**
     * Secondary constructor
     * <p>Specialy for override :)</p>
     *
     * @access protected
     * @return void
     */
    protected function _construct()
    {}

    /**
     * Add data
     *
     * @param array $data
     * @access public
     * @return Eater
     */
    public function addData($data, $recursive = false)
    {
        foreach ($data as $key => $value) {
            if ($recursive && is_array($value)) {
                $value = (new Eater)->addData($value, $recursive);
            }
            $this->setData($key, $value);
        }
        return $this;
    }

    /**
     * Set data
     *
     * @param mixed $name
     * @param mixed $value
     * @access public
     * @return Eater
     */
    public function setData($name, $value = null, $recursive = false)
    {
        if (is_array($name)) {
            $this->_data = array();
            $this->addData($name, $recursive);
        } else {
            $this->_data[$this->format($name)] = $value;
        }
        return $this;
    }

    /**
     * Returns data
     *
     * @param string $name
     * @param string $field
     * @access public
     * @return mixed
     */
    public function getData($name = null, $field = null)
    {
        if (is_null($name)) {
            return $this->_data;
        } elseif (array_key_exists($name = $this->format($name), $this->_data)) {
            if ($field !== null) {
                return isset($this->_data[$name][$field]) ? $this->_data[$name][$field] : null;
            }
            return $this->_data[$name];
        }
        return null;
    }

    /**
     * Data exists?
     *
     * @param string $name
     * @access public
     * @return bool
     */
    public function hasData($name = null)
    {
        return is_null($name)
            ? !empty($this->_data)
            : array_key_exists($this->format($name), $this->_data);
    }

    /**
     * Unset data
     *
     * @param string $name
     * @access public
     * @return Eater
     */
    public function unsetData($name = null)
    {
        if (is_null($name)) {
            $this->_data = [];
        } elseif (array_key_exists($name = $this->format($name), $this->_data)) {
            unset($this->_data[$name]);
        }
        return $this;
    }

    /**
     * Returns if data offset exist
     *
     * @param mixed $offset
     * @access public
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($this->format($offset), $this->_data);
    }

    /**
     * Returns data
     *
     * @param mixed $offset
     * @access public
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getData($offset);
    }

    /**
     * Set data
     *
     * @param mixed $offset
     * @param mixed $value
     * @access public
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->setData($offset, $value);
    }

    /**
     * Unset data
     *
     * @param mixed $offset
     * @access public
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->unsetData($offset);
    }

    /**
     * Format a string for storage
     *
     * @param string $str
     * @access public
     * @return string
     */
    public function format($str)
    {
        return strtolower(preg_replace('`(.)([A-Z])`', "$1_$2", $str));
    }

    /**
     * Merge an other Eater (or array)
     *
     * @param Eater|array $eater
     * @access public
     * @return Eater
     */
    public function merge($eater)
    {
        if (!$eater instanceof Eater && !is_array($eater)) {
            throw new Eater\Exception('Only array or Eater are expected for merge.');
        }
        return $this->setData(array_merge_recursive(
            $this->getData(),
            ($eater instanceof Eater) ? $eater->getData() : $eater
        ));
    }

    /**
     * Current data
     *
     * @access public
     * @return mixed
     */
    public function current()
    {
        return current($this->_data);
    }

    /**
     * Current key
     *
     * @access public
     * @return mixed
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * Next data
     *
     * @access public
     * @return mixed
     */
    public function next()
    {
        return next($this->_data);
    }

    /**
     * Rewind data
     *
     * @access public
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->_data);
    }

    /**
     * Returns if key is valid
     *
     * @access public
     * @return bool
     */
    public function valid()
    {
        $key = $this->key();
        return ($key !== null && $key !== false);
    }

    /**
     * Magic CALL
     *
     * @param string $name Method name
     * @param array $arguments Method arguments
     * @access public
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        switch ($prefix) {
            case 'set':
                return $this->setData(substr($name, 3), $arguments[0]);
                break;
            case 'get':
                $field = isset($arguments[0]) ? $arguments[0] : null;
                return $this->getData(substr($name, 3), $field);
                break;
            case 'has':
                return $this->hasData(substr($name, 3));
                break;
            case 'uns':
                $begin = 3;
                if (substr($name, 0, 5) == 'unset') {
                    $begin = 5;
                }
                return $this->unsetData(substr($name, $begin));
                break;
        }
    }

    /**
     * Magic SET
     *
     * @param string $name
     * @param mixed $value
     * @access public
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
     * @access public
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getData($name);
    }

    /**
     * Magic TOSTRING
     *
     * @access public
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
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->_data;
    }

    /**
     * Magic SLEEP
     *
     * @access public
     * @return string
     */
    public function __sleep()
    {
        return ['_data'];
    }


}
