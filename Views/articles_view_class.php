<?php
class articles_view extends base_view
{
	// use base_view;
	public function index($articles = NULL)
	{
		$this->get_header();
	?>
		<div id="main">
			<p class="bread"></p>
			<div class="thread-header">
				<h3>Полезное и не очень</h3>
			</div>
	<?php foreach ($articles as $key => $value): ?>
			<a class="articles-container navigation" href="/articles/show/<?=$value->id?>">
				<img src="http://<?=ROOT?>/Views/Articles/<?=$value->image?>" class="articles-img" alt="Empty">
				<h4><?=$value->header?></h4>
				<p><?php echo substr($value->text, 0, 480); ?>...</p>
			</a>
	<?php endforeach; ?>
			<a href="<?=$this->backpath?>" class="navigation back"><i class="icon-reply icon2x" aria-hidden="true"></i><span>Назад</span></a>
		</div> <!-- main -->
	<?php
		$this->get_footer();
	}

	public function show($article, $path)
	{
		$this->get_header();
	?>
		<div id="main">
			<p class="bread"><?=$path?></p>
			<div class="thread-header">
				<h3>Полезное и не очень</h3>
			</div>
			<div class="article-container">
				<img src="http://<?=ROOT?>/Views/Articles/<?=$article->image?>" class="articles-img" alt="Empty">
				<h4><?=$article->header?></h4>
				<p><?=$article->text?></p>
			</div>
			<a href="<?=$this->backpath?>" class="navigation back"><i class="icon-reply icon2x" aria-hidden="true"></i><span>Назад</span></a>
		</div> <!-- main -->
	<?php
		$this->get_footer();
	}
}