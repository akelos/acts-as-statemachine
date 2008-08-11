<?php
class OrderInstaller extends AkInstaller
{
    function up_1()
    {
        $this->createTable('orders','id,name,status');
    }
    
    function down_1()
    {
        $this->dropTable('orders');
    }
}