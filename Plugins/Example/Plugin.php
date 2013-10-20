<?php
namespace Example;

use Core\AbstractPlugin;
use Core\PluginInterface;
use Core\EventManager;
use Core\IrcResponse;
use Core\Command;

class Plugin extends AbstractPlugin implements PluginInterface
{

    /**
     * Called while loading the plugin
     */
    public function onLoad()
    {
        // Attach to the !example command event
        $this->attach('!example', 'example', 'Try it and you will see ;)');
        
        // You can also attach to IRC commands like:
        //$this->attach('PRIVMSG', 'example2');
    }

    /**
     * Attached to Bot-Command: !example
     */
    public function example(IrcResponse $irc, Command $cmd)
    {
        $from    = $irc->getFrom();
        $channel = $irc->getChannel();
	    
        $this->message($channel, "Hallo $from!");
        $this->getStorage();
    }
	
    /**
     * Attached to IRC-Command: PRIVMSG
     */
    /*public function example2(IrcResponse $irc)
    {
        $this->message($irc->getFrom(), 'Received PRIVMSG command!');
    }*/
	
}
