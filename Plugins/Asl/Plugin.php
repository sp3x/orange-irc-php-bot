<?php
namespace Asl;

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
        $this->attach('!asl', 'asl', '[USERNAME] Show user information');
        $this->attach('!asladd', 'asladd', '[USERNAME] [LOCATION] [SKILLS] Add user information');
    }

    /**
     * Get asl form user
     */
    public function asl(IrcResponse $irc, Command $cmd)
    {
        $nick = $cmd->getParams()[0];
        
        if(!$nick) {
            return $this->message($irc->getChannel(), 'Usage: !asl [USERNAME]');
        }
        
        $asl = $this->getStorage()->getByKey($nick);
        
        if(!$asl) {
            return $this->message($irc->getChannel(), 'No asl set for '.$nick.' use !asladd');
        }
        
        $this->message($irc->getChannel(), "[Age]: {$asl['age']} [Location]: {$asl['location']} [Skills]: {$asl['skills']}");

    }
    
    /**
     * Add asl for user
     */
    public function asladd(IrcResponse $irc, Command $cmd)
    {
        $nick = $cmd->getParams()[0];
        $params = $cmd->getParams();
    
        if(!$nick || !$params[1] || !$params[2] || !$params[3]) {
            return $this->message($irc->getChannel(), 'Usage: !asladd [USERNAME] [AGE] [LOCATION] [SKILLS]');
        }

        if($nick != $irc->getFrom() /*|| !$this->isOperator($irc->getFrom())*/) {
            return $this->message($irc->getChannel(), 'Only '.$nick.' itself or an op (not implemented) can do that!');
        }

        $config = $this->getStorage()->add(array(
            $nick => array(
            	'age' => $params[1],
                'location' => $params[2],
                'skills' => str_replace(' ', ',', explode(' ', $cmd->getParamString(), 4)[3]),
            )
        ));
        
        $this->message($irc->getChannel(), 'Done');        
    }

	
}
