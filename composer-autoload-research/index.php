<?php

require __DIR__ . '/vendor/autoload.php';


echo "===========================classmap=======================================".PHP_EOL;

/**
 * "classmap" : ["component/"]
 */

echo MyTimer::SEC_PER_DAY.PHP_EOL;
echo Process::ERR.PHP_EOL;
echo Queue\BeanstalkdQueue::QUEUE_SYSLOG.PHP_EOL;


echo "===========================files=======================================".PHP_EOL;

/**
 *"files": ["helper/functions.php"]
 */
echo func().PHP_EOL;



echo "===========================psr-0=======================================".PHP_EOL;

/**
 * "psr-0": {
 *      "Linux\\Man\\": ""
 * }
 */
echo Linux\Man\Errno::EBADF.PHP_EOL;
echo Linux\Man\Signals\SigKill::SIGKILL.PHP_EOL;
echo Linux\Man\Signals_SigTerm::SIGTERM.PHP_EOL; #psr-0 伪命名空间类型


/**
 * psr-0: {
 *  "": "domain/",
 * }
 */
echo ServerManager::BEANSTALK_SERVER.PHP_EOL;
echo Me_Quan_Zhang::MY_SITE.PHP_EOL;


/**
 *ps-0: {
 *  "Hello\\Girl\\": ["hello/"]
 *}
 */
echo Hello\Girl\HelloGirl::HELLO_GIRL.PHP_EOL;


echo "===========================psr-4=======================================".PHP_EOL;

/**
 * psr-4: {
 *   "": ["core/", "util/"],
 * }
 */
echo Condition::EQ.PHP_EOL;
echo FileSystem::FILE_MODE.PHP_EOL;


/**
 *psr-4: {
 *  "App\\": ""
 *}
 */
echo App\Application::APP_NAME.PHP_EOL;


/**
 * "Lego\\": ["lego/src/"]
 */
echo Lego\Formatter\JsonFormatter::SIMPLE_DATE.PHP_EOL;
