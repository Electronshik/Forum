<?php
class music_controller extends controller
{
	public function index()
	{
		$model = new user_model();
		$files = $model->get_all_downloads();
		$user = FALSE;
		if ($this->auth)
		{
			$user = $model->get_profile($_SESSION['login']);
		}
		$this->view->index($files, $user);
	}
	public function liked($id = NULL)
	{
		if ($this->auth && is_numeric($id))
		{
			$model = new user_model();
			$login = $_SESSION['login'];
			$model->user_liked_file((int)$id, $login);
		}
		self::log('no parameters');
		header('Location: /music');
		exit();
	}
}