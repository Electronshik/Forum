<?php
class users_view extends base_view
{
	// use base_view;
	public function index($users = NULL)
	{
		$this->get_header();
	?>
		<div id="main">
			<p class="bread"></p>
			<div class="thread-container">
				<div class="thread-header">
				<h3>Участники</h3>
				</div>
	<?php
		foreach ($users as $key => $value)
		{
			if ( $value->downloads ) $value->downloads = unserialize($value->downloads);
	?>
				<div class="users-preview">
					<img class="users-avatar" src="<?=USERS_DIR_HTTP?><?=$value['avatar']?>">
					<a class="icon-ref navigation" style="margin-top: 4px;" href="/user/profile/<?=$value['login']?>"><i class="icon-user" aria-hidden="true"></i><?=$value['login']?></a>
					<p class="users-subinfo">Сообщений: <?=$value->messages?></p>
					<p class="users-subinfo">Загрузок: <?=count($value->downloads)?></p>
					<p class="users-about"><?=$value->about?></p>
				</div>
	<?php
		}
	?>
			</div>	<!-- thread container -->
			<a href="<?=$this->backpath?>" class="navigation back"><i class="icon-reply icon2x" aria-hidden="true"></i><span>Назад</span></a>
		</div>	<!-- main -->
	<?php
		$this->get_footer();
	}
}