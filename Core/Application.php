<?php

namespace Core;

use Core\Exception\ConnectionException;
use Core\Exception\PluginException;

class Application extends Console
{
    
    /**
     * PluginManager
     * @var PluginManager
     */
    private $_pluginManager = NULL;
    
    /**
     * IRC EventManager
     * @var EventManagerIrc
     */
    private $_emIrc = NULL;
    
    /**
     * Bot EventManager
     * @var EventManagerBot
     */
    private $_emBot = NULL;
    
    /**
     * Loop
     * @var bool
     */
    private $_run = true;

    /**
     * Application configuration
     * @var array
     */
    protected $_config = NULL;
    
    /**
     * Instance of IRC object
     * @var Irc
     */
    private $_irc = NULL;

    /**
     * Singletone pattern
     */
    private static $_instance = NULL;

    /**
     * Get singletone
     * 
     * @return Application
     */
    public static function getInstance()
    {
        if (static::$_instance === NULL)
            static::$_instance = new static();
            
        return static::$_instance;
    }

    /**
     * Run application
     *
     * @param  array $config
     * @return int   Exit-Code
     */
    public function run($config = NULL)
    {
        $this->getConfig($config); // Init configuration object
        $response = NULL;
        $this->getIrc()->getConnection();
        $this->loadPlugins();
        
        while($this->_run) {
            try {
                $response = $this->getIrc()->getResponse();
                $this->getEventManagerIrc()->trigger('irc.received', $response);
                $this->getEventManagerIrc()->trigger('irc.preDispatch', $response);
                
                // Trigger event for received command
                $this->getEventManagerIrc()->trigger('*', $response);
                $this->getEventManagerIrc()->trigger($response->getCommand(), $response);
                
                // Trigger event for received bot command
                if ($response->getTrail()[0] == '!') {
                    $this->getEventManagerIrc()->trigger('irc.preCommand', $response);
                    // If $response has been filtered, drop it.
                    if($this->getIrc()->connected() && $response->dropped())
                        continue;
                    
                    $command = new Command($response->getTrail());
                    if ($command->isValid())
                        $this->getEventManagerBot()->trigger($command->getCommand(), $response, $command);
                    
                    $this->getEventManagerIrc()->trigger('irc.postCommand', $response);
                }
                
                $this->getEventManagerIrc()->trigger('irc.postDispatch', $response);
            } catch (ConnectionException $e) {
                $this->error($e->getMessage());
            }
      
        }

        return 0;
    }
    
    /**
     * Load plugins
     */
    private function loadPlugins()
    {
        try {
            $this->getPluginManager()->loadPlugins();
        } catch (PluginException $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * Get plugin manager
     * 
     * @return \Core\PluginManager
     */
    public function getPluginManager()
    {
        if($this->_pluginManager === null) {
            $this->_pluginManager = new PluginManager(PLUGINS_DIR, $this);
        }
        
        return $this->_pluginManager;
    }
    
    /**
     * Event manager for Bot commands
     *
     * @return \Core\EventManager
     */
    public function getEventManagerBot()
    {
        if($this->_emBot === NULL) {
            $this->_emBot = new EventManagerBot();
        }
    
        return $this->_emBot;
    }
    
    /**
     * Event manager for IRC commands
     * 
     * @return \Core\EventManager
     */
    public function getEventManagerIrc()
    {
        if($this->_emIrc === NULL) {
            $this->_emIrc = new EventManager();
        }
        
        return $this->_emIrc;
    }
    
    /**
     * Get the configuration or init it by param
     *
     * @param array $config
     * @return ConfigFs
     */
    public function getConfig($config = NULL)
    {
        if ($this->_config === NULL) {
            if(!$config)
                throw new NoConfigException();

            $this->setConfig(new \Core\ConfigManager($config));
            $this->getConfig()->load();
            
            if(!$this->getConfig()->keyExists('irc'))
            {
                // Get config via stdin
                $this->getConfig()->setKey('irc', $this->dialog(IrcConfig::$template));
                $this->getConfig()->persist();
            }
        }
        
        return $this->_config;
    }
    
    /**
     * Spread config to all core objects on change
     * 
     * @param array $config
     */
    public function setConfig(Config $config)
    {
        $this->_config = $config;
    }
    
    /**
     * Get the irc object
     *
     * @return Irc
     */
    public function getIrc()
    {
        if ($this->_irc === NULL) {
            $this->_irc = new Irc($this->getConfig()->getByKey('irc'));
        }
        
        return $this->_irc;
    }
    
    /**
     * Save configuration on clean exit
     */
    public function __destruct()
    {
        $this->getConfig()->persist();
    }


}
