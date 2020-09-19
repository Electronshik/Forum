<?php
class users_controller extends controller
{
	public function index()
	{
		$model = new user_model();
		$users = $model->get_users();
		$this->view->index($users);
	}
}