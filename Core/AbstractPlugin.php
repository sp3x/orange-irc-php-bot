<?php

namespace Core;

abstract class AbstractPlugin
{
    
    const ACCESS_ALL    = 0;
    const ACCESS_VOICED = 1;
    const ACCESS_CHANOP = 2;
    const ACCESS_BOTOP  = 3;
    
    /**
     * Plugin storage
     * @var ConfigFs
     */
    private $_storage = NULL;
    
    /**
     * Access levels for commands
     * @var array
     */
    private $_access = array();
    
    /**
     * Application instance
     * @var Application
     */
    private $_application;
    
    /**
     * Command usages
     * @var array
     */
    private $_help = array();

    /**
     * Send message to server
     * 
     * @param unknown $message
     * @param unknown $to
     */
    public function message($to, $message)
    {
        $this->getIrc()->sendCommand("PRIVMSG $to :$message");
    }
    
    /**
     * Get plugins storage
     * 
     * @return \Core\ConfigFs
     */
    protected function getStorage()
    {
        return $this->_storage;
    }
    
    /**
     * Set plugins storage
     * 
     * @param ConfigFs $storage
     */
    public function setStorage(ConfigFs $storage)
    {
        $this->_storage = $storage;
    }
    
    /**
     * Check if request is allowed
     * 
     * @param string $command
     * @param IrcResponse $irc
     * @return boolean
     */
    public function allowed($command, IrcResponse $irc)
    {
        if (!isset($this->_access[$command]))
            return true;
        
        switch ($this->_access[$command]) {
            
        	case static::ACCESS_BOTOP: // Check if the sender is an BotOperator
        	    if($this->isBotOperator($irc->getFrom()))
        	        return true;
        	    break;
        	    
        	default: // Access level not specified
        	    return false;
        }
    }
    
    /**
     * Checks if specified user is an operator
     * 
     * @param string $nick
     * @return boolean
     */
    protected function isBotOperator($nick)
    {
        if($nick == $this->getIrc()->getOperator())
            return true;
        
        return false;
    }

    /**
     * Attach to an Event
     * 
     * @param string $command
     * @param string $method
     * @param string $usage
     */
    protected function attach($command, $method, $usage = NULL, $access = NULL)
    {
        if($command[0] == '!') {           
            $command = strtolower($command);
            $command = str_replace('!', '', $command);
            
            if($access) {
                $this->_access[$command] = $access;
            }
            
            $this->getApplication()->getEventManagerBot()->attach($command, $this, $method);
            
            if($usage)
                $this->_help[$command] = $usage;
        } else {
            $command = strtoupper($command);
            $this->getApplication()->getEventManagerIrc()->attach($command, $this, $method);
        }
        
    }
    
    /**
     * Get sotored help and filter by access rights
     * 
     * @return array
     */
    public function getHelp(IrcResponse $irc)
    {
        $help = array();
        
        foreach ($this->_help as $command => $usage) {
            if($this->allowed($command, $irc)) {
                if($this->getApplication()->getEventManagerBot()->disabled($command))
                    $command .= ' (off)';
                $help[$command] = $usage;
            }
        }
        
        return $help;
    }
    
    /**
     * Send message to server
     *
     * @param unknown $message
     * @param unknown $to
     */
    protected function send($command)
    {
        $this->getIrc()->sendCommand($command);
    }
    
    /**
     * Get the irc
     * @return \Core\Irc
     */
    protected function getIrc()
    {
        return $this->getApplication()->getIrc();
    }

    /**
     * Application instance
     * @return \Core\Application
     */
	public function getApplication() {
		return $this->_application;
	}
	
	public function setApplication($_application) {
		$this->_application = $_application;
		return $this;
	}
	

}
