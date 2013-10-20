<?php
namespace Core;

class Console
{
    
    private $_params = NULL;

    protected function __construct()
    {
        set_time_limit(0);
        @ob_end_flush();
        
        if($this->getParam('wait'))
            sleep($this->getParam('wait'));
    }
    
    public function getParam($param)
    {
        global $argv;
        
        if($this->_params === NULL) {
            for($i=1; $i<count($argv); $i++) {
                if(preg_match('|^--(\S+)=(\S+)$|', $argv[$i], $matches))
                    $this->_params[$matches[1]] = $matches[2];
            }
        }
        
        return (isset($this->_params[$param])) ? $this->_params[$param] : NULL;
    }
    
    public function getInput($type = 'string')
    {
        echo '<:';
        $data = fgets(STDIN);
        echo "\n";
        
        return trim($data);
    }
    
    public function dialog(array $dialog)
    {
        $result = array();
        
        foreach ($dialog as $option => $params) {
            do {
                $this->ask($params);
                $data = $this->getInput();
                
                if($data === '' && isset($params['default']))
                    $data = $params['default'];

                if($data || $data === false) break;
                
                if($data == '' && (!isset($params['required']) || !$params['required']))
                    break;
                    
                $data = false;
                
            } while(!$data);
            
            if(isset($params['type'])) switch($params['type']) {
            	case 'bool':
            	    $data = (bool)$data;
            	    break;
            	    
            	case 'int':
            	    $data = (int)$data;
            }
            
            $result[$option] = $data;
        }
        
        return $result;
    }
    
    /**
     * Print formated question
     * 
     * @param array $options
     * @return Console
     */
    public function ask($options)
    {
        $params = '';
        
        if(isset($options['type']) && $options['type'] == 'bool') {
            if($options['default'])
                $params = '(Y,n)';
            else
                $params = ' (y,N)';
            
            unset($options['default']);
        }

        if(isset($options['default'])) {
            $params .= ' ['.$options['default'].']';
        }
        
        $this->text($options['message'].$params.':');
        
        return $this;
    }
    
    /**
     * Display text to stderr
     * 
     * @param string $text
     * @param bool $nl
     * @return Console
     */
    public function error($text, $nl = true)
    {
        fwrite(STDERR, 'ERROR: '.$text."\n");
        
        return $this;
    }
    
    /**
     * Print text on stdout
     * 
     * @param string $text
     * @param bool $nl
     * @return Console
     */
    public function text($text, $nl = true)
    {
        fwrite(STDOUT, $text."\n");
        
        return $this;
    }
    
}