<?php

class Order extends ActiveRecord
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
    
                                                 
    function send()
    {
        $this->transition('opened','sent',true);
        $this->transition('returned','sent',true);
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
    }
    
    function exitOpened($from,$to)
    {
        $this->states[]=array('exitOpened',$from,$to);
    }
    
    function sent($from,$to)
    {
        $this->states[]=array('sent',$from,$to);
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