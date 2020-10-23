<?php
class Error_Log
{
	public static function add_log($text)
	{
		echo get_called_class();
		echo $text;
	}
}