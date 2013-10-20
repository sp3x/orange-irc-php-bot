<?php
namespace Core;

class IrcConfig
{
    
    public static $template = array(
    	
        'server' => array(
    	   'message'  => 'Specify the server to connect to',
           'type'     => 'string',
           'required' => true,
           'default'  => 'kornbluth.freenode.net',
        ),

        'port' => array(
    	   'message'  => 'Specify the server\'s port',
           'type'     => 'int',
           'required' => true,
           'default'  => 6667,
        ),
        
        'ssl' => array(
            'message'  => 'Is the server using ssl',
            'type'     => 'bool',
            'required' => true,
            'default'  => false,
        ),
        
        'timeout' => array(
            'message'  => 'Specify the socket timeout',
            'type'     => 'int',
            'required' => true,
            'default'  => 3,
        ),
        
        'nick' => array(
            'message'  => 'Specify the bot\'s nick',
            'type'     => 'string',
            'required' => true,
            'default'  => 'orange',
        ),
        
        'password' => array(
            'message'  => 'Specify the server\'s password',
            'type'     => 'string',
        ),
        
        'administrator' => array(
            'message'  => 'Specify the nick of the administrator of this bot',
            'type'     => 'string',
            'required' => true,
            'default'  => 'sp3x',
        ),
        
    );
    
}