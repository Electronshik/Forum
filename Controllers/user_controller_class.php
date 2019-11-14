<?php
class user_controller extends controller
{
	use translate;

	public function index(){}
	public function signup()
	{
		$model = new user_model();
		if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['login'])) )
		{
			$this->prepare_post();
			$this->post['login'] = strtolower($this->post['login']);
			if ($this->post['signup'] == 'true')
			{
				$error = $_FILES['avatar']['error'];
				if ($error == UPLOAD_ERR_OK)
				{
					$filename = basename($_FILES['avatar']['name']);
					$filename = $this->translate_filename($filename);
					if (mkdir(USERS_DIR.'/'.$this->post['login']))
					{
						if ( move_uploaded_file($_FILES['avatar']['tmp_name'], USERS_DIR.'/'.$this->post['login'].'/'.$filename) )
						{
							$avatar = $this->post['login'].'/'.$filename;
						}
					}
				}
				call_user_func_array([$model, add_new_user], [$this->post['login'], $this->post['password'], $avatar]);
				$_SESSION['login'] = $this->post['login'];
			}
		}
		unset($_POST);
		$this->view->signup();
	}

	public function signin()
	{
		$model = new user_model();
		$this->prepare_post();
		$login = strtolower($this->post['login']);
		$password = $this->post['password'];
		if ($this->post['signin'] == 'true')
		{
			if(isset($login) && !empty($login))
			{
				if (isset($password) && !empty($password))
				{
					$auth = $model->check_user($login, $password);
					if ($auth)
					{
						$_SESSION['login'] = $login;
						$_SESSION['unreaded'] = $model->get_unreaded($login);
					}
					else
					{
						$_SESSION['unauth']['login'] = $login;
						$_SESSION['unauth']['password'] = $password;
					}
				}
			}
		}
		if ($this->post['logout'])
		{
			session_unset();
		}
		unset($_POST);
		header('Location: /');
	}

	public function profile($login)
	{
		if (isset($login) && !empty($login))
		{
			$model = new user_model();
			$pers = FALSE;
			$admin = FALSE;
			if ($this->auth)
			{
				if ($login === $_SESSION['login'])
				{
					$pers = TRUE;
					if ($login === 'admin')
					{
						$admin = TRUE;
					}
				}
				else
				{
					if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['user_mess'])))
					{
						$this->prepare_post();
						if ($this->post['user_mess'] == 'true')
						{
							$model->send_message($_SESSION['login'], $login, $this->post['message']);
							if ($this->post['from_personal'])
							{
								header('Location: /user/personal/sent');
							}
							else
							{
								header('Location: /user/profile/'.$login);
							}
								exit();
						}
					}
				}
				// $user = $model->get_profile($login);
				// $this->view->profile($user, $pers, $admin);
			}
			$user = $model->get_profile($login);
			if ($user)
			{
				$this->view->profile($user, $this->auth, $pers, $admin);
				exit();
			}

		}
		header('Location: /forum/index');
		exit();
	}

	public function personal($dest = 'inbox', $letter_id = '')
	{
		if ($this->auth)
		{
			$model = new user_model();
			$letter = '';
				if (isset($letter_id) && !empty($letter_id))
				{
					if (($dest === 'inbox') or ($dest === 'sent'))
					{
						$id = trim(strip_tags($letter_id));
						$id = trim($id,'\,." /|.,:;');
						$letter = $model->get_letter($id);
						if ($letter->fromlogin === $_SESSION['login'])
						{
							$letter->sent = TRUE;
						}
						if ($letter->tologin === $_SESSION['login'])
						{
							$letter->inbox = TRUE;
						}
						if (!($letter->sent xor $letter->inbox))
						{
							$letter = 'Error!';
						}
					}
				}
				$user = $model->get_profile($_SESSION['login']);
				$inbox = $model->get_inbox($_SESSION['login']);
				$outbox = $model->get_outbox($_SESSION['login']);
				$downloads = $model->get_downloads($_SESSION['login']);
				$_SESSION['unreaded'] = 0;
				$model->clear_unreaded($_SESSION['login']);
				if ($user)
				{
					$this->view->personal($user, $inbox, $outbox, $downloads, $dest, $letter);
				}
				exit();
		}
		header('Location: /forum/index');
		exit();
	}

	public function edit()
	{
		if ($this->auth)
		{
			$model = new user_model();
			$user = $model->get_profile($_SESSION['login']);
			if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['edit'])))
			{
				$this->prepare_post();
				if ($this->post['edit'] == 'true')
				{
					$error = $_FILES['avatar']['error'];
					if ($error == UPLOAD_ERR_OK)
					{
						$filename = basename($_FILES['avatar']['name']);
						$filename = $this->translate_filename($filename);
						$deleted = FALSE;
						if ( $user->avatar === $user->login.'/'.$filename )
						{
							if (file_exists(USERS_DIR.'/'.$user->avatar))
							{
								unlink(USERS_DIR.'/'.$user->avatar);
								$deleted = TRUE;
							}
						}
						if ( move_uploaded_file($_FILES['avatar']['tmp_name'], USERS_DIR.'/'.$user->login.'/'.$filename) )
						{
							if ($user->avatar != 'avatar_def.jpg')
							{
								if (file_exists(USERS_DIR.'/'.$user->avatar) && !$deleted)
								{
									unlink(USERS_DIR.'/'.$user->avatar);
								}
							}
							$avatar = $user->login.'/'.$filename;
							$model->edit_user($user->login, $avatar);
							$user = $model->get_profile($_SESSION['login']);
						}
					}
				}
				header('Location: /user/profile/'.$user->login);
			}
			if ($user)
			{
				$this->view->edit($user);
				exit();
			}
		}
		header('Location: /forum/index');
		exit();
	}

	public function addfile()
	{
		if ($this->auth)
		{
			$model = new user_model();
			$user = $model->get_profile($_SESSION['login']);
			if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['addfile'])))
			{
				$this->prepare_post();
				if ($this->post['addfile'] == 'true')
				{
					$error = $_FILES['download']['error'];
					if ($error == UPLOAD_ERR_OK)
					{
						$filename = basename($_FILES['download']['name']);
						$filename = $this->translate_filename($filename);
						if (!file_exists(FILES_DIR.$filename))
						{
							if ( move_uploaded_file($_FILES['download']['tmp_name'], FILES_DIR.$filename) )
							{
								$file_id = $model->download_file($user->login, $filename, $this->post['type'], $this->post['name'], $this->post['about']);
								$user = $model->get_profile($_SESSION['login']);
							}
						}
					}
				}
				header('Location: /user/addfile/'.$user->login);
			}
			$this->view->addfile($user, $model->get_downloads($user->login));
		}
	}
	
	public function delfile($id)
	{
		if ($this->auth)
		{
			$model = new user_model();
			$filename = $model->delete_file($id, $_SESSION['login']);
			unlink(FILES_DIR.$filename);
		}
		header('Location: /user/personal/downloads');
		exit();
	}

	public function delmsg($dest, $id)
	{
		if ($this->auth)
		{
			$model = new user_model();
			$model->delete_message($id);
		}
		header('Location: /user/personal/'.$dest);
		exit();
	}
}