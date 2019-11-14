<?php
class admin_model  extends base_model
{
	public function add_article($header, $text, $image)
	{
		$articles = R::dispense('articles');
		$articles->header = $header;
		$articles->text = $text;
		$articles->image = $image;
		R::store($articles);
	}
}