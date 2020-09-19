<?php
class articles_controller extends controller
{
	public function index()
	{
		$model = new articles_model();
		$articles = $model->get_articles();
		$this->view->index($articles);
	}

	public function show($id = NULL)
	{
		$model = new articles_model();
		if (is_numeric($id))
		{
			$article = $model->get_article((int)$id);
			$path = '<a href="/articles" class="navigation">Статьи</a> > <span>'.$article->header.'</span>';
			if ($article)
			{
				$this->view->show($article, $path);
				exit();
			}
		}
		self::log('no parameters');
		header('Location: /articles');
		exit();
	}
}