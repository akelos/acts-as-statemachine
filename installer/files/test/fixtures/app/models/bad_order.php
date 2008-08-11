<?php

class BadOrder extends ActiveRecord
{
    var $states = array();
    var $acts_as = array('statemachine'=>array('initial'=>'opened',
                                               'states'=>
                                                 array('opened'=>array('enter'=>'opened',
                                                                       'exit'=>'exitOpened'),
                                                       'sent'=>array('enter'=>'sent',
                                                                     'exit'=>'exitSent'),
                                                       'delivered'=>array('enter'=>'delivered',
                                                                          'exit'=>'exitDelivered'),
                                                       'returned'=>array('enter'=>'returned',
                                                                         'exit'=>'exitReturned')))
                                               );
    
    var $_avoidTableNameValidation = true;
    
    function BadOrder()
    {
        $this->setModelName("BadOrder");
        $attributes = (array)func_get_args();
        $this->setTableName('orders', true, true);
        $this->init($attributes);
    }
    
    function send()
    {
        $this->transition('sent','sent',true);
        $this->transition('opened','sent',true);
        $this->transition('returned','sent',true);
    }
    
    function loop()
    {
        $this->transition('opened','sent');
        $this->transition('sent','opened');
    }
    function deliver()
    {
        $this->transition('sent','delivered',true);
    }
    
    function returnOrder()
    {
        $this->transition('delivered','returned',true);
    }
    
    function opened($from,$to)
    {
        $this->states[]=array('opened',$from,$to);
        $this->loop();
    }
    
    function exitOpened($from,$to)
    {
        $this->states[]=array('exitOpened',$from,$to);
    }
    
    function sent($from,$to)
    {
        $this->states[]=array('sent',$from,$to);
        $this->transition($to,$from);
    }
    
    
    
    function exitSent($from,$to)
    {
        $this->states[]=array('exitSent',$from,$to);
    }
    
    function delivered($from,$to)
    {
        $this->states[]=array('delivered',$from,$to);
    }
    
    function exitDelivered($from,$to)
    {
        $this->states[]=array('exitDelivered',$from,$to);
    }
    
    function returned($from,$to)
    {
        $this->states[]=array('returned',$from,$to);
    }
    
    function exitReturned($from,$to)
    {
        $this->states[]=array('exitReturned',$from,$to);
    }
}


?>