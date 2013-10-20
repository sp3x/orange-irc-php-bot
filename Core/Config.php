<?php
namespace Core;

class Config extends ArrayObject
{

    /**
     * Init config
     * 
     * @param array $config
     */
    public function __construct(array $config = array())
    {
       $this->set($config);
    }
    
    /**
     * Get complete configuration or init it on first call
     * 
     * @param array $config
     * @return array
     */
    public function get(array $config = array())
    {
        return $this->_container;
    }
    
    /**
     * Check if specified key exists
     * 
     * @param string $key
     */
    public function keyExists($key)
    {
        return isset($this->_container[$key]);
    }
    
    /**
     * Set configuration and overwrite all data
     * 
     * @param array $config
     * @return Config
     */
    public function set($config, $value = NULL)
    {
        if($value !== NULL) {
            $this->_container[$config] = $value;
            return $this;    
        }

        $this->_container = $config;
        
        return $this;
    }
    
    /**
     * Set config key
     * 
     * @param string $key
     * @param array $config
     * @return Config
     */
    public function setKey($key, array $config)
    {
        $this->_container[$key] = $config;
        
        return $this;
    }
    
    /**
     * Add array to configuration
     * 
     * @param array $config
     * @return Config
     */
    public function add(array $config)
    {
        $this->_container = array_merge($config, $this->_container);
        
        return $this;
    }
    
    /**
     * Get configuration by key
     * 
     * @param string $key
     * @return NULL
     */
    public function getByKey($key)
    {
        if (array_key_exists($key, $this->_container))
            return $this->_container[$key];
        
        return null;
    }
    
}