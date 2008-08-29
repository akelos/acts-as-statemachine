<?php

 /**
 * @ExtensionPoint BaseActiveRecord
    *
   */
class ActsAsStatemachineExtensions
{
    /**
     * transition from state $from to state $to
     *
     * @param string $from
     * @param string $to
     * @param boolean $save
     */
    function transition($from, $to, $save=false) {
        if (isset($this->statemachine) && method_exists($this->statemachine,"transition")) {
            $newStatus = $this->statemachine->transition(&$this,$from, $to, $save);
            $this->statemachine->setStatus(&$this,$newStatus);
        }
    }
}
?>