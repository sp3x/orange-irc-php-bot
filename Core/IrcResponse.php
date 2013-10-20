<?php
namespace Core;

class IrcResponse
{
    private $_nick;
    
    private $_prefix;
    
    private $_params;
    
    private $_command;
    
    private $_trail;
    
    private $_row;
    
    private $_from;

    private $_channel;
    
    public function __construct($row, $nick)
    {
        $regex = '/^(:(?<prefix>\S+) )?(?<command>\S+)( (?!:)(?<params>.+?))?( :(?<trail>.+))?$/';

        preg_match($regex, $row, $matches);
        
        $this->setNick($nick);
        if(isset($matches['prefix'])) $this->setPrefix($matches['prefix']);
        if(isset($matches['command'])) $this->setCommand($matches['command']);
        if(isset($matches['params'])) $this->setParams($matches['params']);
        if(isset($matches['trail'])) $this->setTrail($matches['trail']);
        $this->setRow($row);

        $this->setFrom(substr($this->getPrefix(), 0, strpos($this->getPrefix(), '!')));
        
        if($this->getParams() == $this->getNick())
            $this->setChannel($this->getFrom());
        else
            $this->setChannel($this->getParams());
    }

	public function getPrefix() {
		return $this->_prefix;
	}
	
	public function setPrefix($_prefix) {
		$this->_prefix = $_prefix;
		return $this;
	}
	
	public function getParams() {
		return $this->_params;
	}
	
	public function setParams($_params) {
		$this->_params = $_params;
		return $this;
	}
	
	public function getCommand() {
		return $this->_command;
	}
	
	public function setCommand($_command) {
		$this->_command = $_command;
		return $this;
	}
	
	public function getTrail() {
		return $this->_trail;
	}
	
	public function setTrail($_trail) {
		$this->_trail = $_trail;
		return $this;
	}

	public function getRow() {
		return $this->_row;
	}
	
	public function setRow($_row) {
		$this->_row = $_row;
		return $this;
	}

	public function getFrom() {
		return $this->_from;
	}
	
	public function setFrom($_from) {
		$this->_from = $_from;
		return $this;
	}
	
	public function getChannel() {
		return $this->_channel;
	}
	
	public function setChannel($_channel) {
		$this->_channel = $_channel;
		return $this;
	}

	public function getNick() {
		return $this->_nick;
	}
	
	public function setNick($_nick) {
		$this->_nick = $_nick;
		return $this;
	}
	
	
	
	
    
    
    
}