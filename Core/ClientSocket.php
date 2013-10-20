<?php
namespace Core;

use Core\Exception\ConnectionException;
class ClientSocket
{
    /**
     * Socket number
     * @var int
     */
    private $_socket;
    
    /**
     * Last error number
     * @var int
     */
    private $_errno = 0;
    
    /**
     * Last error
     * @var string
     */
    private $_error;
    
    /**
     * Connection timeout
     * @var int
     */
    private $_timeout = 0;
    
    /**
     * Server to connect to
     * @var string
     */
    private $_server;
    
    /**
     * Port to connect to
     * @var int
     */
    private $_port;
    
    /**
     * Use SSL?
     * @var bool
     */
    private $_ssl = false;
    
    /**
     * Connect to remote host
     * 
     * @throws ConnectionException
     * @return ClientSocket
     */
    public function connect()
    {
        $server = $this->_server;
        
        if($this->_ssl) // Check for SSL
            $server = 'ssl://'.$server;
        
        $this->_socket = $this->_socket = fsockopen(
	       $server,
           $this->_port,
           $this->_errno,
           $this->_error,
           $this->_timeout
        );
        
        if(!$this->connected())
            throw new ConnectionException($this->_error);
        
        return $this;
    }
    
    /**
     * Disconnect from host
     * 
     * @return boolean
     */
    public function disconnect()
    {
        if($this->connected())
            return fclose($this->_socket);
        
        return false;
    }
    
    /**
     * Send data to socket
     * 
     * @throws ConnectionException
     * @param string $buffer
     * @return int
     */
    public function send($buffer)
    {
        $count = @fwrite($this->_socket, $buffer);
        
        if($count === false)
            throw new ConnectionException('Send error');
        
        return $count;
    }
    
    /**
     * Get data from socket
     * 
     * @throws ConnectionException
     * @return string
     */
    public function receive()
    {
        $data = @fgets($this->_socket);
        
        //if($data === false)
            //throw new ConnectionException('Receive error');
            
        return $data;
    }
    
    /**
     * Check if connection is okay
     * 
     * @return boolean
     */
    public function connected()
    {
        return ($this->_socket) ? !feof($this->_socket) : false;
    }
    
    /**
     * Enable SSL on connect
     * 
     * @return ClientSocket
     */
    public function enableSSL()
    {
        $this->_ssl = true;
        
        return $this;
    }
    
    /**
     * Disable SSL on connect
     * 
     * @return ClientSocket
     */
    public function disableSSL()
    {
        $this->_ssl = false;
        
        return $this;
    }

	public function getSocket() {
		return $this->_socket;
	}
	
	public function setSocket( $_socket) {
		$this->_socket = $_socket;
		return $this;
	}
	
	public function getTimeout() {
		return $this->_timeout;
	}
	
	public function setTimeout( $_timeout) {
		$this->_timeout = $_timeout;
		return $this;
	}
	
	public function getServer() {
		return $this->_server;
	}
	
	public function setServer( $_server) {
		$this->_server = $_server;
		return $this;
	}
	
	public function getPort() {
		return $this->_port;
	}
	
	public function setPort( $_port) {
		$this->_port = $_port;
		return $this;
	}
	
	public function getSsl() {
		return $this->_ssl;
	}
	
	public function setSsl( $_ssl) {
		$this->_ssl = $_ssl;
		return $this;
	}
	
        
}