<?php
class forum_controller extends controller
{
	public function index()
	{
		$model = new forum_model();
		$this->view->index($model->get_categories(), $model->get_all_subcategories());
	}
	public function thread($id = '1')
	{
		$model = new forum_model();
		$thread = $model->get_thread($id);
		$cat = $model->get_category($id);
		$subcat = $model->get_subcategory($id);
		$path = '<a href="/forum/index" class="navigation">'.$cat['title'].'</a> > <span href="/forum/thread/'.$id.'">'.$subcat['title'].'</span>';
		if ($this->auth)
		{
			$this->view->auth = $model->check_ban($_SESSION['login']);
		}
		$this->view->thread($id, $thread, $path, $subcat['title']);
	}
	public function newtopic($id = '1')
	{
		if ($this->auth)
		{
			if ( ($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_POST['newtopic'])) )
			{
				$this->prepare_post();
				$login = $_SESSION['login'];
				$model = new forum_model();
				$id = $model->new_topic($this->post['header'], $this->post['message'], $this->post['category'], $login);
				header('Location: /forum/topic/'.$id);
				exit();
			}
			else
			{
				$model = new forum_model();
				$subs = $model->get_all_subcategories();
				$cat = $model->get_category($id);
				$subcat = $model->get_subcategory($id);
				$path = '<a href="/forum/index" class="navigation">'.$cat['title'].'</a> > <span href="/forum/thread/'.$id.'">'.$subcat['title'].'</span>';
				$this->view->newtopic($id, $subs, $path);
				exit();
			}
		}
		else
		{
			header('Location: /');
			exit();
		}
	}
	public function topic($id)
	{
		$model = new forum_model();
		$idsql = str_replace('-', 't', $id);
		$topic = $model->get_topic($idsql);
		$idarr = explode('-', $id);
		$users = $model->get_topic_users_data($idsql);
		$cat = $model->get_category($idarr[0]);
		$subcat = $model->get_subcategory($idarr[0]);
		$thread = $model->get_thread($idarr[0]);
		$path = '<a href="/forum/index" class="navigation">'.$cat['title'].'</a> > <a href="/forum/thread/'.$subcat['id'].'" class="navigation">'.$subcat['title'].'</a> > <span href="/forum/topic/'.$id.'">'.$thread[$idarr[1]]['header'].'</span>';
		if ($this->auth)
		{
			$this->view->auth = $model->check_ban($_SESSION['login']);
		}
		$this->view->topic($id, $topic, $users, $path, $thread[$idarr[1]]['header']);
	}
	public function newmsg($id)
	{
		if ($this->auth)
		{
			if ( ($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_POST['newmsg'])) )
			{
				$this->prepare_post();
				if($this->post['newmsg'])
				{
					$login = $_SESSION['login'];
					$model = new forum_model();
					$idsql = str_replace('-', 't', $id);
					$model->new_msg($idsql, $login, $this->post['msg']);
				}
			}
		}
		header('Location: /forum/topic/'.$id);
		exit();
	}
	public function delmsg($topic_id, $msg_id)
	{
		$model = new forum_model();
		$message = $model->get_message($topic_id, $msg_id);
		if ($msg_id != 1)
		{
			if ($this->auth)
			{
				if ( ($_SESSION['login'] === $message->login) || ($_SESSION['login'] === 'admin') )
				{
						$model->delete_message($topic_id, $msg_id);
				}
			}
		}
		header('Location: /forum/topic/'.$topic_id);
		exit();
	}
	public function editmsg()
	{
		$this->prepare_post();
		$model = new forum_model();
		$message = $model->get_message($this->post['topicid'], $this->post['msgid']);
		if ( ($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_POST['editmsg'])))
		{
			if ( $this->auth && $this->post['editmsg'])
			{
				if ( ($_SESSION['login'] === $message->login) || ($_SESSION['login'] === 'admin') )
				{
					if (!empty($this->post['message']) && !empty($this->post['topicid']) && !empty($this->post['msgid']))
					{
						$model->edit_message($this->post['topicid'], $this->post['msgid'], $this->post['message']);
						header('Location: /forum/topic/'.$this->post['topicid']);
					}
				}
			}
		}
		exit();
	}
}