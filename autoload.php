<?php
class autoload_class
{
	public function R()
	{
		require 'rb-mysql.php';
	}
	public function Model_Topics()
	{

	}
	public function __call($name, $arg)
	{
		if(method_exists($this, $name))
		{
			throw new Exception("Error Autoload Methods", 1);
		}
		else
		{
			try {
					if ( file_exists($name.'_class.php') )
					{
						include $name.'_class.php';
					}
					else
					{
						if(explode('_', $name)[1] == 'controller')
						{
							$path = 'Controllers/'.$name.'_class.php';
							if ( file_exists($path))
							{
								include $path;
							}
						}
						if(explode('_', $name)[1] == 'view')
						{
							$path = 'Views/'.$name.'_class.php';
							if ( file_exists($path))
							{
								include $path;
							}
						}
						if(explode('_', $name)[1] == 'model')
						{
							$path = 'Models/'.$name.'_class.php';
							if ( file_exists($path))
							{
								include $path;
							}
						}
						if ( !class_exists($name) )
						{
							throw new Exception("Autoload Error! No such class!", 1);	
						}
					}
			} catch (Exception $e) {
				// echo $e->getMessage(); //this is for R classes
				// exit();
			}
		}
	}
}