<?php

error_reporting(E_ALL);

set_time_limit(0);

define('ALL_TESTS_CALL',true);
define('ALL_TESTS_RUNNER',true);
define('AK_ENVIRONMENT','testing');
define('AK_TEST_DATABASE_ON', true);

define('AK_BASE_DIR', realpath(dirname(__FILE__).str_repeat(DIRECTORY_SEPARATOR.'..', 5)));

require_once(dirname(__FILE__).str_repeat(DIRECTORY_SEPARATOR.'..', 5).DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

require_once(AK_LIB_DIR.DS.'AkInstaller.php');

session_start();

$test = &new GroupTest('Acts As Statemachine Plugin Unit Tests');

$test->addTestFile(AK_TEST_DIR.DS.'unit'.DS.'lib'.DS.'ActsAsStatemachineTest.php');

exit ($test->run(new TextReporter()) ? 0 : 1);



?>