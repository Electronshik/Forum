<?php
abstract class base_view
{

	private $ajax = FALSE;
	protected $backpath = 'http://forum.loc';
	protected $domen = 'forum.loc';

	abstract function index();

	public function __construct()
	{
		if ( (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) && (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) )
		{
			if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
			{
				$this->ajax = TRUE;
			}
		}
		if ( stripos($_SERVER['HTTP_REFERER'], $this->domen) )
		{
			$this->backpath = substr($_SERVER['HTTP_REFERER'], (strlen($this->domen) + stripos($_SERVER['HTTP_REFERER'], $this->domen)) );
		}
	}

	public function __call($name, $parameters)
	{
		if ($name === 'get_header')
		{
			if ($this->ajax === FALSE)
			{
				$name = 'get_html_header';
			}
			return call_user_func_array([$this, $name], $parameters);
		}
		if ($name === 'get_footer')
		{
			if ($this->ajax === FALSE)
			{
				$name = 'get_html_footer';
			}
			return call_user_func_array([$this, $name], $parameters);
		}
	}

	private function get_html_header()
	{
		$root = 'http://'.ROOT.'/';
	?>
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="UTF-8">
			<title>Forum</title>
			<link rel="stylesheet" href="<?=$root?>css/style.css">
			<link rel="stylesheet" href="<?=$root?>css/buttons.css">
			<link rel="stylesheet" href="<?=$root?>css/form.css">
			<link rel="stylesheet" href="<?=$root?>css/forum-style.css">
			<link rel="stylesheet" href="<?=$root?>css/user-style.css">
			<link rel="stylesheet" href="<?=$root?>css/categories.css">
			<link rel="stylesheet" href="<?=$root?>css/player.css">
			<link rel="stylesheet" href="<?=$root?>css/mfglabs_iconset.css">
			<link rel="stylesheet" href="<?=$root?>css/animate.css">
			<script src="<?=$root?>js/jquery-3.2.1.min.js"></script>
			<script src="<?=$root?>js/navigator.js"></script>
		</head>
		<body>
			<div id="player-container">
			<audio id="player-audio" src="" controls></audio>
			<p id="player-label"> </p>
				<div id="player-controls">
					<i class="icon-step_backward icon1x" aria-hidden="true"></i>
					<i class="icon-step_forward icon1x" aria-hidden="true"></i>
					<i class="icon-play" aria-hidden="true"></i>
				</div>
			</div> <!-- player-container -->
			<div id="ajax-container">
	<?php
		$this->get_header();
	}

	private function get_header()
	{
		$root = 'http://'.ROOT.'/';
	?>
		<header>
			<div id="header-container">
				<div id="input-form">
	<?php
		if ( (isset($_SESSION['login'])) && (!empty($_SESSION['login'])) ):
	?>
				<a class="register navigation" style="top: -3px;left: 32px;" href="/user/profile/<?=$_SESSION['login']?>">Профиль</a>
				<?php if ($_SESSION['unreaded']): ?>
				<span class="unreaded"><?=$_SESSION['unreaded']?></span>
				<?php endif; ?>
				<form method="post" action="/user/signin" class="login">
					<p>&nbsp</p>
					<p style="padding-left: 62px;">Выход</p>
					<p style="padding-left: 18px;">Привет, <?=ucfirst($_SESSION['login'])?>!</p>
					<p class="login-submit">
					<input type="hidden" name="logout" value="true">
					<button type="submit" class="login-button">Выйти</button>
					</p>
	  			</form>
	<?php
		else:
	?>
				<a class="register" href="/user/signup"><?=$_SESSION['unauth'] ?'Нет такого юзера!':'Регистрация'?></a>
				<form method="post" action="/user/signin" class="login">
				    <p>
				      <input type="text" name="login" id="login" placeholder="Login" required value="<?=$_SESSION['unauth']['login']?>">
				    </p>
				    <p>
				      <input type="password" name="password" id="password" placeholder="password" required value="<?=$_SESSION['unauth']['password']?>">
				    </p>
				    <p class="login-submit">
				    <input type="hidden" name="signin" value="true">
				      <button type="submit" class="login-button">Войти</button>
				    </p>
	  			</form>
	<?php
			session_unset();
		endif;
	?>
				</div> <!-- input-form -->
			</div> <!-- header-container -->
			<div id="menu">
				<div id="ulist"><span></span>
					<ul>
						<li><a href="/forum" class="navigation">Форум</a></li>
						<li><a href="/articles" class="navigation">Статьи</a></li>
						<li><a href="/music" class="navigation">Музыка</a></li>
						<li><a href="/users" class="navigation">Участники</a></li>
						<li><a href="/about" class="navigation">About</a></li>
					</ul>
				</div>
			</div> <!-- menu -->
			<script>
				var ad = document.getElementById('ulist');
				$(window).resize(function () {
					$('#ulist span').html('Width: '+window.innerWidth+', Height: '+window.innerHeight);
				});
				console.log(document.body.offsetWidth);
			</script>
		</header>
	<?php
	}

	private function get_footer()
	{
	?>
			<footer>
				<div id="footer-menu">
					Footer-menu
				</div>
			</footer>
	<?php
	}

	private function get_html_footer()
	{
		$this->get_footer();
	?>
			</div> <!-- ajax-container -->
		</body>
	</html>
	<?php
	}
}
?>