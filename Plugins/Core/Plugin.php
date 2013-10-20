<?php
namespace Core;

use Core\AbstractPlugin;
use Core\PluginInterface;
use Core\EventManager;
use Core\IrcResponse;
use Core\Command;

class Plugin extends AbstractPlugin implements PluginInterface
{

    /**
     * (non-PHPdoc)
     * @see \Core\PluginInterface::onLoad()
     */
	public function onLoad() {
	    $em = $this->getApplication()->getEventManagerBot();
	    
	    $this->message($this->getIrc()->getOperator(), 'Hallo :) I\'am online!');
	    
        $this->attach('PING',     'pong');
        $this->attach('!help',    'help',    'Display avilable commands with usage');
        $this->attach('!quit',    'quit',    'Stop the bot',    static::ACCESS_BOTOP);
        $this->attach('!restart', 'restart', 'Restart the bot', static::ACCESS_BOTOP);
        $this->attach('!irc',     'irc',     '[IRC_CMD] Execute params as IRC command', static::ACCESS_BOTOP);
        $this->attach('!enable',  'enable',  '[CMD] Enable command', static::ACCESS_BOTOP);
        $this->attach('!disable', 'disable', '[CMD] Disable command', static::ACCESS_BOTOP);
        
        /*
         * Disable disabled plugins from storage
         */
        $disabled = $this->getStorage()->getByKey('disabled');
        if($disabled) foreach($disabled as $event => $disabled) {
            if($disabled)
                $em->disable($event);
        }
	}

	/**
	 * Handle Ping/Pong Requests
	 * 
	 * @param IrcResponse $irc
	 */
	public function pong(IrcResponse $irc)
	{
	    $param = $irc->getTrail();
	    $this->send("PONG :$param");
	}
	
	/**
	 * Exit the bot
	 *
	 * @param IrcResponse $irc
	 * @param Command $cmd
	 */
	public function quit(IrcResponse $irc, Command $cmd)
	{
	   $this->message($this->getIrc()->getOperator(), 'Stop requested (by '.$irc->getFrom().')');
	   exit(0);
	}
	
	/**
	 * Restart the bot
	 *
	 * @param IrcResponse $irc
	 * @param Command $cmd
	 */
	public function restart(IrcResponse $irc, Command $cmd)
	{
	    $this->message($this->getIrc()->getOperator(), 'Restart requested (by '.$irc->getFrom().')...');
	    exit(1); // Exit with X so shell script can catch this and restart and wait X seconds
	}
	
	/**
	 * Display help
	 * 
	 * @param IrcResponse $irc
	 * @param Command $cmd
	 */
	public function help(IrcResponse $irc, Command $cmd)
	{
	    $help = array();
	    
	    $plugins = $this->getApplication()->getPluginManager()->getPluginsLoaded();
	    foreach ($plugins as $plugin) {
	        $help = array_merge($plugin->getHelp($irc), $help);
	    }
	   
	    $this->message($irc->getFrom(), str_pad('', 50, '-'));
	    $this->message($irc->getFrom(), str_pad(str_pad('| Command', 15).' | Helptext', 49).'|');
	    $this->message($irc->getFrom(), str_pad('', 50, '-'));
	    
	    foreach($help as $command => $text) {
	        $command = str_pad($command, 15);
	        $command .= ' - ';
	        
	        $this->message($irc->getFrom(), $command.$text);
	    }
	}
	
	/**
	 * Execute message as IRC command
	 * 
	 * @param IrcResponse $irc
	 * @param Command $cmd
	 */
	public function irc(IrcResponse $irc, Command $cmd)
	{
	    $this->send($cmd->getParamString());
	}
	
	/**
	 * Enable command
	 *
	 * @param IrcResponse $irc
	 * @param Command $cmd
	 */
	public function enable(IrcResponse $irc, Command $cmd)
	{
	    $em = $this->getApplication()->getEventManagerBot();
	    $cmd = $cmd->getParams()[0];
	    
	    if($em->disabled($cmd) === NULL) {
	        $this->message($irc->getFrom(), 'Not found ('.$cmd.')');
	        return;
	    }
	        
	    $this->getStorage()->set('disabled', array($cmd => false))->persist();
	    
	    $em->enable($cmd);
	    $this->message($irc->getFrom(), 'Done');
	}
	
	/**
	 * Disable command
	 *
	 * @param IrcResponse $irc
	 * @param Command $cmd
	 */
	public function disable(IrcResponse $irc, Command $cmd)
	{
	    $em = $this->getApplication()->getEventManagerBot();
	    $cmd = $cmd->getParams()[0];
	    
	    if($em->disabled($cmd) === NULL) {
	        $this->message($irc->getFrom(), 'Not found ('.$cmd.')');
	        return;
	    }

	    $this->getStorage()->add('disabled', array($cmd => true))->persist();
	    
	    $em->disable($cmd);
	    $this->message($irc->getFrom(), 'Done');
	}

	/**
	 * Cleanly quit from IRC
	 */
	public function __destruct()
	{
	    $this->send('QUIT See u later ;)');
	}
	
}
