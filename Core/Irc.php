<?php

namespace Core;

use Core\Exception\ConnectionException;

class Irc extends ClientSocket
{
    /**
     * Nickname of the bot
     * @var string
     */
    private $_nick;
    
    /**
     * Password of the server
     * @var string
     */
    private $_password;
    
    /**
     * Nickname of the primary operator of this bot
     * @var string
     */
    private $_operator;
    
    /**
     * IRC Recv buffer
     * @var string
     */
    private $_buffer = '';
    
    /**
     * Irc config
     * @var Config
     */
    private $_config;
    
    /**
     * Specifies if its the first connection to the server
     * @var unknown
     */
    private $_firstConnect = true;
     
    /**
     * Init the config on construct
     * 
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->setConfig($config);
    }
    
    /**
     * Return parsed message from IRC
     * 
     * @return IrcResponse
     */
    public function getResponse()
    {
        $this->getConnection();
        
        $message = '';
        
        $buffer = $this->getBuffer();

        $pos = strpos($buffer, "\r\n");
        if ($pos) {
            $message = trim(substr($buffer, 0, $pos));
            $this->_buffer = substr($buffer, $pos+2, strlen($buffer));
        }

        return new IrcResponse($message, $this->getNick());
    }
    
    /**
     * Get a connection to the IRC and init it
     * 
     * @throws ConnectionException
     */
    public function getConnection()
    {
        while (!$this->connected()) {
            if(!$this->_firstConnect) {
                fwrite(STDERR, 'ERROR: Reconnecting to IRC-Server (in '.$this->getTimeout().'s)...'."\n");
                sleep($this->getTimeout());
            }
            
            $this->connect();
            $this->register();
            $this->_firstConnect = false;
        }
    }
    
    public function register()
    {
        $nick = $this->getNick();
        $this->sendCommand("USER $nick $nick $nick OrangeBot :$nick");
        $this->sendCommand("NICK $nick");
    }
    
    /**
     * Send a command to the IRC server
     * 
     * @param string $cmd
     * @return int
     */
    public function sendCommand($cmd)
    {
        return $this->send($cmd."\r\n");
    }
    
    /**
     * Get all data from socket
     * 
     * @return string
     */
    public function getBuffer()
    {
        if(($buffer = $this->receive()) !== false) {
            $this->_buffer .= $buffer;
        }
        
        return $this->_buffer;
    }
    
    /**
     * Set configuration
     * 
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->setServer($config['server']);
        $this->setPort($config['port']);
        $this->setTimeout($config['timeout']);
        
        if($config['ssl']) $this->enableSSL(); else $this->disableSSL();
        
        $this->setNick($config['nick'])
            ->setOperator($config['administrator'])
            ->setPassword($config['password']);
        
    }

	public function getNick() {
		return $this->_nick;
	}
	
	public function setNick( $_nick) {
		$this->_nick = $_nick;
		return $this;
	}
	
	public function getPassword() {
		return $this->_password;
	}
	
	public function setPassword( $_password) {
		$this->_password = $_password;
		return $this;
	}
	
	public function getOperator() {
		return $this->_operator;
	}
	
	public function setOperator( $_operator) {
		$this->_operator = $_operator;
		return $this;
	}
	
}
