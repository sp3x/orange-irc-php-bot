<?php
namespace Cleverbot;

use Core\AbstractPlugin;
use Core\PluginInterface;
use Core\EventManager;
use Core\IrcResponse;
use Core\Command;

class Plugin extends AbstractPlugin implements PluginInterface
{
    
    private $_session = NULL;
    
    private $_bot = NULL;

    /**
     * Called while loading the plugin
     */
    public function onLoad()
    {
        // Attach to the !example command event
        $this->attach('!bot', 'bot', '[mixed] A bot needs entertainment too ;)');
        require_once(__DIR__ . D . 'chatterbotapi.php');
    }

    /**
     * Attached to Bot-Command: !example
     */
    public function bot(IrcResponse $irc, Command $cmd)
    {
        $return = @$this->getSession()->think($cmd->getParamString());
        $this->message($irc->getChannel(), $return);
    }

	public function getSession() {
	    if($this->_session == NULL) {
	        $this->_session = $this->getBot()->createSession();
	    }
	    
		return $this->_session;
	}
	
	public function getBot() {
	    if($this->_bot === NULL) {
	        $factory = new \ChatterBotFactory();
	        $this->_bot = $factory->create(\ChatterBotType::JABBERWACKY);
	    }
	    
		return $this->_bot;
	}
	
	
}
