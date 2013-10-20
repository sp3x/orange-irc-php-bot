<?php
namespace Core;

use Core\Exception\PluginException;
class PluginManager
{
    
    private $_plugins;
    
    private $_path;
    
    /**
     * Application instance
     * @var Application
     */
    private $_application = null;
    
    /**
     * First time load plugins
     */
    public function __construct($path, Application $application)
    {
        $this->_path = $path;
        $this->_application = $application;
    }
    
    /**
     * Get all valid plugins
     * 
     * @throws PluginException
     * @return multitype:string
     */
    public function getValidPluginPaths()
    {
        $plugins = array();
        $dir = new Directory($this->_path);
        
        foreach ($dir->getDirectories() as $directory) {
            $pluginPath = $this->_path . D . $directory . D;
            $pluginClass = "$directory\Plugin";
            $pluginFile = $pluginPath . 'Plugin.php';
            
            if(!file_exists($pluginFile)) {
                throw new PluginException("Plugin file not found ($pluginFile)");
            }
            
            $syntax = $this->syntaxCheck($pluginFile);
            if($syntax !== true) {
                throw new PluginException("Plugin syntax error ({$syntax[0]})");
            }
            
            $plugins[strtolower($directory)] = array(
            	'path'  => $pluginPath,
                'file'  => $pluginFile,
                'class' => $pluginClass,
            );
        }
        
        return $plugins;
    }
    
    /**
     * Check plugin for correct syntax before loading it
     * 
     * @param stirng $file
     * @return false|string
     */
    private function syntaxCheck($file)
    {
        $cmd = "/usr/bin/env php -l $file";
        
        $result = $this->execute($cmd, $stdout, $stderr);
        if ($result == 0)
            return true;
        
        return $stderr;
    }
    
    /**
     * Execute program and get stderr and stdout text
     * 
     * @param string $cmd
     * @param array $stdout
     * @param array $stderr
     * @return int
     */
    private function execute($cmd, &$stdout, &$stderr)
    {
        $outfile = tempnam(".", "cmd");
        $errfile = tempnam(".", "cmd");
        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("file", $outfile, "w"),
            2 => array("file", $errfile, "w")
        );
        $proc = proc_open($cmd, $descriptorspec, $pipes);
    
        if (!is_resource($proc)) return 255;
    
        fclose($pipes[0]);    //Don't really want to give any input
    
        $exit = proc_close($proc);
        $stdout = file($outfile);
        $stderr = file($errfile);
    
        unlink($outfile);
        unlink($errfile);
        
        return $exit;
    }
    
    /**
     * Return application object
     * 
     * @return Application
     */
    private function getApplication()
    {
        return $this->_application;
    }
    
    /**
     * Load plugins
     */
    public function loadPlugins()
    {
        foreach ($this->getValidPluginPaths() as $plugin => $params) {
            include_once($params['file']);
            
            $object = new $params['class']();
            
            if($object instanceof AbstractPlugin) {
                $object->setApplication($this->getApplication());
                $object->setStorage((new ConfigFs(DATA_DIR.D.'plugin_'.$plugin.'.dat'))->load());
            }
            
            if(method_exists($object, 'onLoad'))
                $object->onLoad();
            
            $this->_plugins[$plugin] = $object;
        }
    }
    
    /**
     * Return loaded plugins
     * 
     * @return array
     */
    public function getPluginsLoaded()
    {
        return $this->_plugins;
    }
    
}