<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */

//관리자일때 (cli or admin. or admin5.)
if( strtolower(php_sapi_name()) == "cli" || strpos(parse_url($_SERVER['HTTP_HOST'], PHP_URL_PATH), "admin.") !== FALSE || strpos(parse_url($_SERVER['HTTP_HOST'], PHP_URL_PATH), "admin5.") !== FALSE ) {
    define("APP_TYPE", "admin");
}else if( strpos(parse_url($_SERVER['HTTP_HOST'], PHP_URL_PATH), "m.") !== FALSE ) { //모바일
    define("APP_TYPE", "mobile");
}else if( strpos(parse_url($_SERVER['HTTP_HOST'], PHP_URL_PATH), "www.") !== FALSE ) { //PC
    define("APP_TYPE", "pc");
} else { //그외 PC
//    define("APP_TYPE", "admin");
    define("APP_TYPE", "mobile");
//    define("APP_TYPE", "pc");
}


$not_allow = array(
        '193.112.9.107'
    ,   '118.126.101.81'
    ,   '103.87.26.190'
    ,   '195.54.160.135'
);

if(in_array($_SERVER['REMOTE_ADDR'] , $not_allow) == true){
    exit;
}

if( strpos(parse_url($_SERVER['HTTP_HOST'], PHP_URL_PATH), "fiamall") !== FALSE ){

    $host_arr = explode('.',$_SERVER['HTTP_HOST']);
    $url = "http://{$host_arr[0]}.cloma.co.kr";
    $url = $url.$_SERVER['REQUEST_URI'];

    header('Location: '.$url);
}

/* com -> co.kr url redirection */
//if( strpos(parse_url($_SERVER['HTTP_HOST'], PHP_URL_PATH), ".fiamall.com") !== FALSE ){
//
//    $host_arr = explode('.',$_SERVER['HTTP_HOST']);
//
//    if( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ) $url = "https://{$host_arr[0]}.fiamall.co.kr";
//    else $url = "http://{$host_arr[0]}.fiamall.co.kr";
//
//    $url = $url.$_SERVER['REQUEST_URI'];
//    header('Location: '.$url);
//
//}


if( $_SERVER['SERVER_ADDR'] == "192.168.1.11" ) {
    define('SERVER_NO', 1);
}
else if( $_SERVER['SERVER_ADDR'] == "192.168.1.12" ) {
    define('SERVER_NO', 2);
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */

//define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

//if( APP_TYPE == "admin" ) {
if( APP_TYPE == "admin" ) {
//    define('ENVIRONMENT', 'development');
    define('ENVIRONMENT', 'production');
}
else {
    define('ENVIRONMENT', 'production');
}

//define('ENVIRONMENT', 'development');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
switch (ENVIRONMENT)
{
    case 'development':
        error_reporting(-1);
        ini_set('memory_limit', '-1');
        ini_set('display_errors', 1);
        error_reporting(E_ALL & ~E_NOTICE);
        break;

    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        if (version_compare(PHP_VERSION, '5.3', '>='))
        {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        }
        else
        {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        }
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1); // EXIT_ERROR
}



session_set_cookie_params(0, "/", ".cloma.co.kr");
ini_set('session.auto_start', 1);

//타임존 설정
date_default_timezone_set('Asia/Seoul');

//디렉터리, 도메일 설정
define("HOMEPATH", str_replace("\\", "/", dirname(__DIR__)));
define("DOCROOT", str_replace("\\", "/", __DIR__));
define("CIROOT", str_replace("\\", "/", HOMEPATH.'/ci'));
define("SITE_NAME_KR", "옷쟁이들");
define("SITE_DOMAIN", "cloma.co.kr");
define("COOKIE_DOMAIN", ".cloma.co.kr");
//https일때 도메인 사용안함. (ssl 인증서 설치시 주석처리할 것)
if( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ) {
    //define("IMG_HTTP", "https://img." . SITE_DOMAIN);
    define("IMG_HTTP", 'https://'.$_SERVER['HTTP_HOST']);
}
else {
    //define("IMG_HTTP", "http://img." . SITE_DOMAIN);
    define("IMG_HTTP", 'http://'.$_SERVER['HTTP_HOST']);
}

/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same directory
 * as this file.
 */
$system_path = HOMEPATH . '/ci/system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder than the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server. If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 */

//관리자일때
if ( APP_TYPE == "admin" ) {
    $application_folder = HOMEPATH . '/ci/application_admin';
}else if ( APP_TYPE == "mobile" ) {
    $application_folder = HOMEPATH . '/ci/application_mobile';
} else {
    $application_folder = HOMEPATH . '/ci/application';
}

/*
 *---------------------------------------------------------------
 * VIEW FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want to move the view folder out of the application
 * folder set the path to the folder here. The folder can be renamed
 * and relocated anywhere on your server. If blank, it will default
 * to the standard location inside your application folder. If you
 * do move this, use the full server path to this folder.
 *
 * NO TRAILING SLASH!
 */
$view_folder = '';


/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here. For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT: If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller. Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 */
// The directory name, relative to the "controllers" folder.  Leave blank
// if your controller is not in a sub-folder within the "controllers" folder
// $routing['directory'] = '';

// The controller class file name.  Example:  mycontroller
// $routing['controller'] = '';

// The controller function you wish to be called.
// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 */
// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

// Set the current directory correctly for CLI requests
if (defined('STDIN'))
{
    chdir(dirname(__FILE__));
}

if (($_temp = realpath($system_path)) !== FALSE)
{
    $system_path = $_temp.'/';
}
else
{
    // Ensure there's a trailing slash
    $system_path = rtrim($system_path, '/').'/';
}

// Is the system path correct?
if ( ! is_dir($system_path))
{
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
    exit(3); // EXIT_CONFIG
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// Path to the system folder
define('BASEPATH', str_replace('\\', '/', $system_path));

// Path to the front controller (this file)
define('FCPATH', dirname(__FILE__).'/');

// Name of the "system folder"
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

// The path to the "application" folder
if (is_dir($application_folder))
{
    if (($_temp = realpath($application_folder)) !== FALSE)
    {
        $application_folder = $_temp;
    }

    define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
}
else
{
    if ( ! is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR))
    {
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
        exit(3); // EXIT_CONFIG
    }

    define('APPPATH', BASEPATH.$application_folder.DIRECTORY_SEPARATOR);
}

// The path to the "views" folder
if ( ! is_dir($view_folder))
{
    if ( ! empty($view_folder) && is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR))
    {
        $view_folder = APPPATH.$view_folder;
    }
    elseif ( ! is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
    {
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
        exit(3); // EXIT_CONFIG
    }
    else
    {
        $view_folder = APPPATH.'views';
    }
}

if (($_temp = realpath($view_folder)) !== FALSE)
{
    $view_folder = $_temp.DIRECTORY_SEPARATOR;
}
else
{
    $view_folder = rtrim($view_folder, '/\\').DIRECTORY_SEPARATOR;
}

define('VIEWPATH', $view_folder);

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 */

require_once BASEPATH.'core/CodeIgniter.php';