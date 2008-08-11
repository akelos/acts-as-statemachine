<?php
require_once(AK_BASE_DIR.DS.'app'.DS.'vendor'.DS.'plugins'.DS.'acts_as_statemachine'.DS.'lib'.DS.'ActsAsStatemachine.php');

class ActsAsStatemachineTest extends AkUnitTest
{

    function setUp()
    {
        $this->installAndIncludeModels('Order');
        $this->instantiateModel('BadOrder');
    }
    
    function test_initial_state_transition()
    {
        $order = new Order();
        $order->save();
        $expectedStates = array(array('opened',null,'opened'));
        $this->assertEqual($expectedStates,$order->states);
    }
    function test_loop()
    {
        $order = new BadOrder();
        $order->save();
        $order->loop();
        $expectedStates = array(array('opened',null,'opened'),array('exitOpened','opened','sent'),array('sent','opened','sent'));
        $this->assertEqual($expectedStates,$order->states);
    }
    
    function test_event_transition_send_order()
    {
        $order = new Order();
        $order->save();
        $expectedStates = array(array('opened',null,'opened'));
        $this->assertEqual($expectedStates,$order->states);
        
        $order = $order->find($order->id);
        $order->send();
        
        $expectedStates = array(array('exitOpened','opened','sent'),array('sent','opened','sent'));
        $this->assertEqual($expectedStates,$order->states);
    }
    
    function test_event_transition_deliver_order()
    {
        $order = new Order();
        $order->save();
        $expectedStates = array(array('opened',null,'opened'));
        $this->assertEqual($expectedStates,$order->states);
        
        $order = $order->find($order->id);
        $order->send();
        
        $order = $order->find($order->id);
        $order->deliver();
        
        $expectedStates = array(array('exitSent','sent','delivered'),array('delivered','sent','delivered'));
        $this->assertEqual($expectedStates,$order->states);
    }
    
    function test_event_transition_return_order()
    {
        $order = new Order();
        $order->save();
        $expectedStates = array(array('opened',null,'opened'));
        $this->assertEqual($expectedStates,$order->states);
        
        $order = $order->find($order->id);
        $order->send();
        
        $order = $order->find($order->id);
        $order->deliver();
        
        $order = $order->find($order->id);
        $order->returnOrder();
        $expectedStates = array(array('exitDelivered','delivered','returned'),array('returned','delivered','returned'));
        $this->assertEqual($expectedStates,$order->states);
    }
    
    function test_event_transition_full_order_lifecycle()
    {
        $order = new Order();
        $order->send();
        $this->assertEqual('sent',$order->status);
        $order->deliver();
        $this->assertEqual('delivered',$order->status);
        $order->returnOrder();
        $this->assertEqual('returned',$order->status);
        
        $expectedStates = array(array('opened',null,'opened'),
                          array('exitOpened','opened','sent'),array('sent','opened','sent'),
                          array('exitSent','sent','delivered'),array('delivered','sent','delivered'),
                          array('exitDelivered','delivered','returned'),array('returned','delivered','returned'));
        $this->assertEqual($expectedStates,$order->states);
    }
    
    function test_event_transition_full_order_lifecycle_bad_order()
    {
        $order = new BadOrder();
        $order->send();
        $this->assertEqual('sent',$order->status);
        $order->deliver();
        $this->assertEqual('delivered',$order->status);
        $order->returnOrder();
        $this->assertEqual('returned',$order->status);
        
        $expectedStates = array(array('opened',null,'opened'),
                          array('exitOpened','opened','sent'),array('sent','opened','sent'),
                          array('exitSent','sent','delivered'),array('delivered','sent','delivered'),
                          array('exitDelivered','delivered','returned'),array('returned','delivered','returned'));
        $this->assertEqual($expectedStates,$order->states);
    }
}
?>