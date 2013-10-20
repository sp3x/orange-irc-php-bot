<?php
namespace Core;

class EventManagerBot extends EventManager
{
    
	/**
	 * Check if command is allowed to user
	 * 
	 * @see \Core\EventManager::trigger()
	 */
	public function trigger($event, IrcResponse $irc, Command $cmd = NULL)
	{
	   // Check if the user can access the requested command
	   if($cmd && isset($this->_listener[$event])) {
	       foreach ($this->_listener[$event] as $listener) {
	           if(!$listener[0]->allowed($event, $irc)) {
	               $listener[0]->message($irc->getFrom(), 'Denied ('.$event.')');
	               return false;
	           }
	       }
	   }    
	    
		parent::trigger($event, $irc, $cmd);
	}

}