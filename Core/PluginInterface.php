<?php
namespace Core;

interface PluginInterface {
    /**
     * Called on loading plugin
     * 
     * @param EventManager $emIrc
     * @param EventManager $emBot
     */
    public function onLoad();
}