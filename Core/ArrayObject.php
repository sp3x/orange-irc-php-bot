<?php
namespace Core;

class ArrayObject implements \ArrayAccess, \Iterator, \Serializable
{
    
    /**
     * Data array
     * @var array
     */
    protected $_container = array();

    public function serialize() {
        return serialize($this->_container);
    }
    
    public function toArray()
    {
        return $this->_container;
    }
    
    public function unserialize($data) {
        $this->_container = unserialize($data);
    }
    
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->_container[] = $value;
        } else {
            $this->_container[$offset] = $value;
        }
    }
    
    public function offsetExists($offset) {
        return isset($this->_container[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->_container[$offset]);
    }
    
    public function offsetGet($offset) {
        return isset($this->_container[$offset]) ? $this->_container[$offset] : null;
    }
    
    public function rewind() {
        return reset($this->_container);
    }
    
    public function current() {
        return current($this->_container);
    }
    
    public function key() {
        return key($this->_container);
    }
    
    public function next() {
        return next($this->_container);
    }
    
    public function valid() {
        return key($this->_container) !== null;
    }
}