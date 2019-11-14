<?php
class articles_model extends base_model
{
	public function get_articles()
	{
		$articles = R::findAll('articles');
		return $articles;
	}
	public function get_article($id)
	{
		$article = R::findOne('articles', 'id = :id', [':id' => $id]);
		return $article;
	}
}