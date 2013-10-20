<?php
namespace Core;

use Core\Exception\ConfigPersistException;

class ConfigFs extends Config
{
    
    private $_file;
    
    private $_data = array();
    
    public function __construct($file)
    {
        $this->_file = $file;
    }
    
    public function persist()
    {
        if($this->isWritable())
            return file_put_contents($this->_file, serialize($this->_container));
        
        throw new ConfigPersistException('Cannot write config ('.$this->_file.')');
    }
    
    /**
     * Loads the configuration from file if it exists.
     * If not it will be created
     * 
     * @return ConfigFs
     */
    public function load()
    {
        if (!$this->exists())
            touch($this->_file);
        
        $this->_container = unserialize(file_get_contents($this->_file));
        if (!is_array($this->_container))
            $this->_container = array();
        
        return $this;
    }
    
    /**
     * Check if the configuration file exists
     * 
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->_file);
    }
    
    /**
     * Checks if the config file is witable if it exists
     * otherwise it will test if the config file cn be
     * created into its directory
     * 
     * @return bool
     */
    public function isWritable()
    {
        if ($this->exists())
            return is_writable($this->_file);
        
        return is_writable(dirname($this->_file));
    }

}