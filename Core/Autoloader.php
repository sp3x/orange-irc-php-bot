<?php
/**
 * This file is part of mg's framework.
 *
 * (c) Marcel Genovese info@sp3x.de
 */

namespace Core;

/**
 * Simple autoloader
 * 
 * @author sp3x
 */
class Autoloader
{
   
    /**
     * 
     * @var \Autoloader
     */
    private static $_instance = NULL;
    
    /**
     * List with directories the autoload
     * function should search in
     * 
     * @var array
     */
    private $_directories = array();
    
    /**
     * Private constructor for singletone
     */
    private function __construct()
    {
    	
    }
    
    /**
     * Get sigletone instance of autoloader
     * 
     * @return  \Autoloader   Autoloader instance
     */
    public static function getInstance()
    {
        if (static::$_instance == NULL) {
        	static::$_instance = new static();
        	static::$_instance->register();
        }
        
        return static::$_instance;
    }
    
    /**
     * Register splAutoloader
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }
    
    /**
     * Register a directory
     * 
     * @param string $path
     */
    public function registerPath($path)
    {
        if ($path[0] == '.' || $path[0] == '..')
            $path = realpath(getcwd() . DIRECTORY_SEPARATOR . $path);

        $this->_directories[realpath($path . DIRECTORY_SEPARATOR)] = 1;
    }
    
    /**
     * Try to load a class by namespace and className
     * like: \Vendor\Package\Class or \Class or Class
     * 
     * @param string $className FQN of loadable class
     * @return void
     */
    public function autoload($class)
    {
        foreach ($this->_directories as $path => $value) {
            $className = ltrim($class, '\\');
            $fileName  = '';
            $namespace = '';
            if ($lastNsPos= strripos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName  = $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace)
                . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

            if (file_exists($fileName)) {
                require $fileName;
                return;
            }
        }
    }
}
