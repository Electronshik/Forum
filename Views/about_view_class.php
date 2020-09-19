<?php
class about_view extends base_view
{
	// use base_view;
	public function index()
	{
		$this->get_header();
	?>
		<div id="main">
			<p class="bread"></p>
			<div class="thread-container">
				<div class="thread-header">
					<h3>О нас</h3>
				</div>
				<div class="thread">
					<p>Ни хрена не скажем</p>
				</div>
			</div>
			<a href="<?=$this->backpath?>" class="navigation back">
				<i class="icon-reply icon2x" aria-hidden="true"></i>
				<span>Назад</span>
			</a>
		</div> <!-- main -->
	<?php
		$this->get_footer();
	}
}