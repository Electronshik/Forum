<?php
abstract class base_model
{
	public function __construct()
	{
		R::setup('mysql:host=localhost; dbname=forum;', 'root', '645');
		// R::fancyDebug(TRUE);
	}
	public function __destruct()
	{
		if (R::testConnection())
		{
			R::close();
		}
	}

	public static function log( $controller, $method, $text, $num )
	{
		if (R::testConnection())
		{
			$errors = R::dispense( 'errors' );
			$errors->controller = $controller;
			$errors->method = $method;
			$errors->text = $text;
			$errors->num = $num;
			$errors->time = time();
			$errors->timeformat = date('d-m G:i', $errors->time);
			R::store($errors);
		}
	}
}