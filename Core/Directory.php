<?php

namespace Core;

class Directory
{
    /**
     * Directory path
     * @var string
     */
    private $_path = '';
    
    /**
     * Cached files to speedup script
     * @var array
     */
    private $_cache = array();
    
    /**
     * Construtor, sets just the path
     * @param string $path
     */
    public function __construct($path = '')
    {
        $this->_path = $path;
        $this->cache();
    }
    
    /**
     * Cache current $this->_path directory
     * 
     * @return boolean
     */
    public function cache()
    {
        if(!$this->exists() || !$this->isDirectory())
            return false;
        
        $cache = array( // Init cache construct
        	'f'    => array(),
            'd'    => array()
        );
        
        $list = array_diff(scandir($this->_path), array('.', '..'));

        // Seperate result into directories and files
        foreach ($list as $file) {
            if (is_dir($this->_path . DIRECTORY_SEPARATOR . $file)) {
                $cache['d'][] = $file;  // Add a dir
                continue;
            }

            $cache['f'][] = $file;  // Add a file
        }
        
        $this->_cache = $cache;
        return true;
    }
    
    /**
     * Change directory
     * 
     * @param string $newPath
     * @return boolean
     */
    public function cd($newPath)
    {
        $this->_path = $newPath;
        return $this->cache();
    }
    
    /**
     * Return all directories in $this->path
     * 
     * @return array
     */
    public function getDirectories()
    {
        return $this->_cache['d'];
    }
    
    /**
     * Return all files in $this->_path
     * 
     * @return array
     */
    public function getFiles()
    {
        return $this->_cache['f'];
    }
    
    /**
     * Clear cache and reload files
     */
    public function refresh()
    {
        $this->cache();
    }
    
    /**
     * Checks if this directory exists
     * if one is specified. otherwise it
     * checks if $this->_path exists.
     * 
     * @param string    $path
     * @return boolean
     */
    public function exists($path = null)
    {
        if ($path === null)
            $path = $this->_path;
        
        if (file_exists($path))
            return true;
        
        return false;
    }
    
    /**
     * Checks if param is a directory
     * @return boolean
     */
    public function isDirectory($directory = null)
    {
        if($directory === null)
            $directory = $this->_path;
            
        return is_dir($directory);
    }
    
    /**
     * Checks if param is a file
     * @return boolean
     */
    public function isFile($file = null)
    {
        if($file === null)
            $file = $this->_path;
         
        return is_file($file);
    }
}