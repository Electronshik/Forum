<?php
class admin_controller extends controller
{
	use translate;
	
	public function index()
	{
		if ($this->auth)
		{
			if ( $_SESSION['login'] === 'admin' )
			{
				$model = new admin_model();
				$this->view->index();
				exit();
			}
		}
		header('Location: /');
		exit();
	}

	public function addarticle()
	{
		if ($this->auth)
		{
			if ( $_SESSION['login'] === 'admin' )
			{
				$model = new admin_model();
				if (($_SERVER['REQUEST_METHOD'] === 'POST') && (!empty($_POST['addarticle'])))
				{
					$this->prepare_post();
					$error = $_FILES['image']['error'];
					if ($error == UPLOAD_ERR_OK)
					{
						$filename = basename($_FILES['image']['name']);
						$filename = $this->translate_filename($filename);
						if ( move_uploaded_file($_FILES['image']['tmp_name'], ARTICLES_DIR.$filename) )
						{
							$image = $filename;
						}
					}
					$model->add_article($this->post['header'], $this->post['text'], $image);
				}
				$this->view->addarticle();
				exit();
			}
		}
		header('Location: /');
		exit();
	}
}