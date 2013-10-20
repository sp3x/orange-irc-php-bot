<?php
namespace Core;

class Command
{
    
    /**
     * The command
     * @var string
     */
    private $_command;
    
    /**
     * Command parameters
     * @var array
     */
    private $_params = array();
    
    /**
     * Complete command row
     * @var string
     */
    private $_trail;
    
    /**
     * Get all params as string
     */
    public function getParamString()
    {
        return implode(' ', $this->getParams());
    }
    
    /**
     * Extract parameters from trail
     * 
     * @param string $trail
     */
    public function __construct($trail)
    {
        $this->setTrail($trail);
        if ($this->isValid()) {
            $pos = strpos($trail, ' ');
            if(!$pos) $pos = strlen($trail);
            $command = substr($trail, 0, $pos);
            $this->setCommand(trim(strtolower(str_replace('!', '', $command))));
            if ($pos) {
                $params  = explode(' ', trim(substr($trail, $pos+1, strlen($trail)-$pos)));
                $this->setParams($params);
            }
        }
    }
    
    /**
     * Check if trail is valid
     * 
     * @return boolean
     */
    public function isValid()
    {
        return ($this->getTrail()[0] == '!' && strlen($this->getTrail()) > 1) ? true : false;
    }

	public function getCommand() {
		return $this->_command;
	}
	
	public function setCommand($_command) {
		$this->_command = $_command;
		return $this;
	}
	
	public function getParams() {
		return $this->_params;
	}
	
	public function setParams($_params) {
		$this->_params = $_params;
		return $this;
	}

	public function getTrail() {
		return $this->_trail;
	}
	
	public function setTrail($_trail) {
		$this->_trail = $_trail;
		return $this;
	}
	
	
    
}