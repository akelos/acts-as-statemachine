<?php
define('AK_AASTM_PLUGIN_FILES_DIR', AK_APP_PLUGINS_DIR.DS.'acts_as_statemachine'.DS.'installer'.DS.'files');

class ActsAsStatemachineInstaller extends AkPluginInstaller
{

  
    function down_1()
    {
        echo "Uninstalling the acts_as_statemachin plugin migration\n";
    }
    
    function up_1()
    {
        echo "\n\nInstallation completed\n";
    }
    
   
}
?>