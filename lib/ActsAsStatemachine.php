<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

// +----------------------------------------------------------------------+
// | Akelos Framework - http://www.akelos.org                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2007, Akelos Media, S.L.  & Bermi Ferrer Martinez |
// | Released under the GNU Lesser General Public License, see LICENSE.txt|
// +----------------------------------------------------------------------+
/**
 see http://svn.viney.net.nz/things/rails/plugins/acts_as_taggable_on_steroids/lib/acts_as_taggable.rb
*/
/**
* @package ActiveRecord
* @subpackage Behaviours
* @author Arno Schneider <arno a.t. bermilabs dot com>
* @copyright Copyright (c) 2002-2007, Akelos Media, S.L. http://www.akelos.org
* @license GNU Lesser General Public License <http://www.gnu.org/copyleft/lesser.html>
*/

require_once(AK_LIB_DIR.DS.'AkActiveRecord'.DS.'AkObserver.php');


class ActsAsStatemachine extends AkObserver
{
    var $_instance;
    var $_states;
    var $_initial_state;
    var $_current_state;
    var $_initial_run_done=false;
    var $_state_column = 'status';

    function ActsAsStatemachine(&$ActiveRecordInstance, $options = array())
    {
        $this->_instance = &$ActiveRecordInstance;
        $this->init($options);
    }
    
    function init($options = array())
    {
        if (isset($options['states'])) {
            $this->_registerStates($options['states']);
        }
        
        if (isset($options['state_column'])) {
            $this->_state_column = $options['state_column'];
        }
        
        if (isset($options['initial'])) {
            $this->_initial_state = $options['initial'];
        }
        
        if (isset($this->_states) && isset($this->_initial_state)) {
            $this->observe(&$this->_instance);
        }
    }
    function _registerStates($states)
    {
        $default_options = array();
        $parameters = array('available_options'=>array('enter','exit'));
        Ak::parseOptions($states,$default_options,$parameters,true);
        $this->_states = $states;
    }
    function beforeCreate(&$record)
    {
        $currentState = $record->get($this->_state_column);
        if (empty($currentState)) {
            $record->set($this->_state_column,$this->_initial_state);
            $record->__run_initial_trans = true;
        }
        return true;
    }

    function afterCreate(&$record)
    {
        return $this->_checkInited(&$record,isset($record->__run_initial_trans)?$record->__run_initial_trans:false);
    }

    function _runInitialStateActions(&$record)
    {
        $record->statemachine->_initial_run_done=true;
        if (isset($this->_states[$this->_initial_state]) &&
            isset($this->_states[$this->_initial_state]['enter'])) {
            $actions = $this->_states[$this->_initial_state]['enter'];
            $actions = !is_array($actions)?array($actions):$actions;
            foreach ($actions as $action) {
                /**
                 * $record->transition($fromState, $toState)
                 */
                $res = $record->$action(null,$this->_initial_state);
                if (!$res) {

                    return false;
                }
            }
            
        }
        return true;
    }
    function _checkInited(&$record, $forceRun=false)
    {
        $currentState=$record->getAttribute($this->_state_column);
        if (!$record->statemachine->_initial_run_done && empty($currentState)) {
            $this->_runInitialStateActions(&$record);
            $record->setAttribute($this->_state_column,$this->_initial_state);
        } else if ($forceRun) {
            $this->_runInitialStateActions(&$record);
        }
    }
    function transition(&$record, $from, $to, $save = false)
    {
        /**
         * case
         * 
         * action1 -> from: state1 to: state2
         * action2 -> from: state2 to: state1
         * action1 -> from: state1 to: state2
         */
        $this->_checkInited(&$record);
        $currentState = $record->getAttribute($this->_state_column);
        $visitedStates = isset($record->___actsasfsm_visited)?$record->___actsasfsm_visited:array($this->_initial_state);
        $isLoop = $from == $to;
        $alreadyVisited = in_array($to,$visitedStates);
        if ($alreadyVisited ||
            $isLoop || 
            $currentState != $from ||
            (isset($record->___actsasfsm_guarded) && $record->___actsasfsm_guarded) ) {
                
                return $currentState;
        }
        
        
        /**
         * Protect the record from bumping between transitions when transition() is being
         * called in one of the actions
         */
        $record->___actsasfsm_guarded = true;
        /**
         * Leaving the previous state
         */
        $exitActions = isset($this->_states[$currentState]) && isset($this->_states[$currentState]['exit'])?$this->_states[$currentState]['exit']:array();
        $exitActions = !is_array($exitActions)?array($exitActions):$exitActions;
        
        foreach($exitActions as $action) {
            if (method_exists($record, $action)) {
                $record->$action($from,$to);
            }
        }
        /**
         * Entering the new state
         */
        $currentState = $to;
        
        $enterActions = isset($this->_states[$currentState]) && isset($this->_states[$currentState]['enter'])?$this->_states[$currentState]['enter']:array();
        $enterActions = !is_array($enterActions)?array($enterActions):$enterActions;
        
        foreach($enterActions as $action) {
            if (method_exists($record, $action)) {
                $record->$action($from,$to);
            }
        }
        
        $res = $record->setAttribute($this->_state_column,$to);
        $record->{$this->_state_column}=$to;
        if ($save) {
            $record->save();
        }
        $visitedStates[] = $to;
        $record->___actsasfsm_guarded = false;
        $record->___actsasfsm_visited = $visitedStates;
        return $to;
    }
    
    function setStatus(&$record, $status)
    {
        $record->set($this->_state_column,$status);
    }
}
?>