<?php
define('DEBUG', false);
if (DEBUG)
{
	echo('Work is still in progress...');
}
else
{
	require 'autoload.php';
	include 'error_class.php';
	spl_autoload_register(function($class)
	{
		$auto = new autoload_class();
		try {
				$auto->$class();
			} catch (Exception $e) {
					echo $e->getMessage().'<br>';
					exit();
				}
	});
	define('ROOT', $_SERVER['HTTP_HOST']);
	define('DIR', __DIR__);
	define('USERS_DIR', __DIR__.'\\Views\\Media\\Users');
	define('USERS_DIR_HTTP', 'http://'.ROOT.'/Views/Media/Users/');
	define('FILES_DIR', __DIR__.'\\Views\\Media\\Files\\');
	define('FILES_DIR_HTTP', 'http://'.ROOT.'/Views/Media/Files/');
	define('ARTICLES_DIR', __DIR__.'\\Views\\Articles\\');
	define('ARTICLES_DIR_HTTP', 'http://'.ROOT.'/Views/Articles/');
	define('SMILES_DIR', __DIR__.'/Views/Images/Smiles/');
	define('SMILES_DIR_HTTP', 'http://'.ROOT.'/Views/Images/Smiles/');
	$request = trim($_SERVER['REQUEST_URI'], '/ ');
	$url = explode('?', filter_var($request, FILTER_SANITIZE_URL))[0];
	$url = explode('/', filter_var($url, FILTER_SANITIZE_URL));
	session_start();
	if (isset($url[0]) && !empty($url[0]))
	{
		$controller_name = $url[0].'_controller';
		if( class_exists($controller_name) )
		{
			$controller = new $controller_name();
			unset($url[0]);
		}
		else
		{
			exit('Invalid request!');
		}
	}
	else
	{
		$controller = new forum_controller();
	}
	if ( ( isset($controller) ) && ( method_exists($controller, $url[1]) ) )
	{
		$method = $url[1];
		unset($url[1]);
	}
	else
	{
		$method = 'index';
	}

	$param = $url ? array_values($url) : [];
	if ( isset($controller) && isset($method) )
	{
		call_user_func_array([$controller, $method], $param);
	}
}