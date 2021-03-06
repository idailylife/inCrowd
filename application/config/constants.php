<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
define('EXIT_SUCCESS', 0); // no errors
define('EXIT_ERROR', 1); // generic error
define('EXIT_CONFIG', 3); // configuration error
define('EXIT_UNKNOWN_FILE', 4); // file not found
define('EXIT_UNKNOWN_CLASS', 5); // unknown class
define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
define('EXIT_USER_INPUT', 7); // invalid user input
define('EXIT_DATABASE', 8); // database error
define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
 * Customized constants
 */
define('DEBUG_MODE', false);
define('CMP_TYPE_GENERAL', 0);
define('CMP_TYPE_USERTEST', 1);
define('IMAGE_BASE_URL', '');       //Base url of comparison images
//define('PATH_TO_RESOURCES', 'd:/wamp/www/'); //Base path of resources
define('COMPARISON_SIZE', 14); //每个用户需要比较的总图片·对·数(不含陷阱题)
define('TEST_CMP_SIZE', 4); //其中每个用户的用户能力测试用的图片·对·数
//define('TRAP_CMP_SIZE', 1); //默认一组内陷阱题的个数
define('MAX_COMPARISON_SIZE', 105); //单个HIT的最大可用比较对数量
define('KEY_HIT_RECORD', 'current_hit_record');  //Session中的键
define('KEY_HIT_COOKIE', 'hit_token');           //Cookie中的键
define('KEY_INVITE_PASS', 'invite_pass');
define('KEY_PASS', 'pass');
define('FONT_PATH', dirname(__FILE__).'/../fonts/courbd.ttf');
define('DB_CONFIG_FILEPATH', 'dbconfig.json');  //数据库配置文件
define('PENALTY_RATE_QOE', 0.90); //QoE问题答错时得分乘以的倍率
define('PENALTY_RATE_TRAP', 0.80); //陷阱题答错时得分乘以的倍率
define('PENALTY_RATE_MAX', 1.05);  //最大倍率
define('BONUS_RATE_QOE', 1.05);   //QoE问题答对时乘以的倍率
define('EXPAND_RATE_MIN', 0.80);  //可以继续下一组实验的最低限倍率

//Traffic control
define('NEED_INVITE', false);   //是否需要邀请码参与
define('MAX_HIT_SIZE', 1000);     //HIT任务总量控制

//Configuration for image id range
define('GENERAL_PIC_START_ID', 1);
define('GENERAL_PIC_END_ID', 551);
define('EVAL_PIC_START_ID', 70);
define('EVAL_PIC_END_ID', 119);

//Cloud Image Fetch
define('USE_CLOUD_SRV', true);
define('CLOUD_SRV_URL', 'http://7xsc9c.com1.z0.glb.clouddn.com/');
define('CLOUD_SRV_SUFFIX', ''); //尚未实现