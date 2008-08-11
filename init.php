<?php

class ActsAsStatemachinePlugin extends AkPlugin
{
    function load()
    {
        require_once($this->getPath().DS.'lib'.DS.'ActsAsStatemachine.php');
    }
}

?>