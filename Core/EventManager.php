<?php
namespace Core;

class EventManager
{
    
    /**
     * Event listeners
     * @var array
     */
    protected $_listener = array();
    
    /**
     * Trigger events
     * 
     * @param string $event
     * @param mixed $param
     * @return boolean
     */
    public function trigger($event, IrcResponse $irc, Command $cmd = NULL)
    {
        $event = strtolower($event);
        
        if(!isset($this->_listener[$event]))
            return false; // No event found
        
        foreach ($this->_listener[$event] as $listener) {
            if($listener[2]) $listener[0]->{$listener[1]}($irc, $cmd);
        }
        
        return true;
    }
    
    /**
     * Clear all listeners
     * 
     * @return EventManager
     */
    public function clear()
    {
        $this->_listener = array();
        
        return $this;
    }
    
    /**
     * Attach Object->Method to this EventManager
     * 
     * @param string $event
     * @param Object $object
     * @param string $method
     */
    public function attach($event, $object, $method)
    {
        $event = strtolower($event);
        
        if(!isset($this->_listener[$event])) {
            $this->_listener[$event] = array();
        }
        
        $this->_listener[$event][] = array($object, $method, true);
    }
    
    /**
     * Disable event
     * 
     * @param string $event
     * @param Object $object
     * @param string $method
     * @return boolean
     */
    public function disable($event)
    {
        $event = strtolower($event);
        
        if(!isset($this->_listener[$event]))
            return false; // No event found
        
        foreach ($this->_listener[$event] as $key => $listener) {
            $this->_listener[$event][$key][2] = false;
        }
    }
    
    /**
     * Check if event is disabled
     * 
     * @param string $event
     * @return boolean
     */
    public function disabled($event)
    {
        $event = strtolower($event);
        
        if(!isset($this->_listener[$event]))
            return NULL; // No event found
        
        if(isset($this->_listener[$event][0]))
            return !$this->_listener[$event][0][2];
    }
    
    /**
     * Enable event
     * 
     * @param string $event
     * @param Object $object
     * @param string $method
     * @return boolean
     */
    public function enable($event)
    {
        $event = strtolower($event);
        
        if(!isset($this->_listener[$event]))
            return false; // No event found
        
        foreach ($this->_listener[$event] as $key => $listener) {
            $this->_listener[$event][$key][2] = true;
        }
    }
    
}