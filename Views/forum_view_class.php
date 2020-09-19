<?php
class forum_view extends base_view
{
	// use base_view;
	private $bbcode_array = ['[quote]','[/quote]','[b]','[/b]','[i]','[/i]','[u]','[/u]','[del]','[/del]'];
	private $bbcode_replace_array = ['<q>','</q><br>','<b>','</b>','<var>','</var>','<u>','</u>','<del>','</del>'];

	public function __construct()
	{
		parent::__construct();
		$dirs = scandir(SMILES_DIR);
		if ($dirs[0] == '.') unset($dirs[0]);
		if ($dirs[1] == '..') unset($dirs[1]);
		foreach ($dirs as $root => $dir)
		{
			$files = scandir(SMILES_DIR.$dir.'\\');
			foreach ($files as $key => $value)
			{
				if ( $num = strripos($value, '.png') )
				{
					$this->smiles[$dir.'-'.substr($value, 0, $num)] = $dir.'/'.$value;
				}
			}
		}
	}

	private function convert_from_bbcode($text)
	{
		$text = str_replace( $this->bbcode_array, $this->bbcode_replace_array, $text);
		$smiles_names = array_flip($this->smiles);
		foreach ($this->smiles as $key => $value)
		{
			$text = str_replace( '['.$key.']', '<img class="forum-smile" src="'.SMILES_DIR_HTTP.$value.'" alt="['.$key.']">', $text);
		}
		return $text;
	}

	private function convert_to_bbcode($text)
	{
		$text = str_replace( $this->bbcode_replace_array, $this->bbcode_array, $text);
		return $text;
	}

	public function index($cats = NULL, $subcats = NULL)
	{
		$this->get_header();
	?>
			<div id="main">
				<p class="bread"><p>
	<?php
		foreach ($cats as $bind => $arr):
	?>
				<div class="thread-header">
					<h3><?=$arr['title']?></h3>
					<h4>Обсуждений</h4>
				</div>
	<?php
			$sub_bind = $cats[$bind]['bind'];
			foreach ($subcats[$sub_bind] as $key => $value):
	?>
				<div class="thread">
					<a href="/forum/thread/<?=$value['id']?>" class="navigation">
						<span class="header"><?=$value['title']?></span>
						<span class="messages"><?=$value['topics']?></span>
					</a>
				</div>
	<?php
			endforeach;
		endforeach;
	?>
			</div> <!-- main -->
	<?php
		$this->get_footer();
	}

	public function thread($id, $thread, $path, $header)
	{
		$this->get_header();
	?>
			<div id="main">
				<p class="bread"><?=$path?></p>
				<div class="thread-header">
					<h3><?=$header?></h3>
					<h4>Сообщений</h4>
					<h4>Автор</h4>
					<h4>Дата создания</h4>
				</div>
	<?php
		foreach ($thread as $topic => $values):
	?>
				<div class="thread">
					<a href="/forum/topic/<?=$id?>-<?=$values['id']?>" class="navigation">
						<span class="header"><?=$values['header']?></span>
						<span class="messages"><?=$values['messages']?></span>
						<span class="autor"><?=ucfirst($values['login'])?></span>
						<span class="time"><?=date('d-m G:i', $values['time'])?></span>
					</a>
				</div>
	<?php
		endforeach;
		if ($this->auth === 'not_banned'):
	?>
				<a class="button-large navigation" href="/forum/newtopic/<?=$id?>">
					<i class="icon-plus" aria-hidden="true"></i>Новая тема</a>
	<?php
		endif;
	?>
				<a href="<?=$this->backpath?>" class="navigation back">
					<i class="icon-reply icon2x" aria-hidden="true"></i>
					<span>Назад</span>
				</a>
			</div> <!-- main -->
	<?php
		$this->get_footer();
	}

	public function newtopic($id, $subs, $path)
	{
		foreach ($subs as $cat => $array)
		{
			$selected = NULL;
			foreach ($array as $subid => $subcat)
			{
				if ($id == $subid)
				{
					$selected = ' selected';
				}
				$list = $list.'<option '.$selected.' value="'.$subid.'">'.$subcat->title.'</option>';
				$selected = NULL;
			}
		}
		$this->get_header();
	?>
			<div id="main">
				<p class="bread"><?=$path?></p>
				<form id="new-topic" class="forms" method="post" action="/forum/newtopic/<?=$id?>">
					<p>Название:</p>
					<input type="text" name="header" required>
					<p>Ветка:</p>
					<select name="category"><?=$list?></select>
					<p>Сообщение:</p>
					<textarea rows="7" cols="92" name="message" required></textarea>
					<input type="hidden" name="newtopic" value="true">
					<button type="submit" class="new-topic">Опубликовать</button>
				</form>
				<a href="<?=$this->backpath?>" class="navigation back">
					<i class="icon-reply icon2x" aria-hidden="true"></i>
					<span>Назад</span>
				</a>
			</div> <!-- main -->
	<?php
		$this->get_footer();
	}

	public function topic($id, $topic, $users, $path, $header)
	{
		$this->get_header();
	?>
			<div id="main">
				<p class="bread"><?=$path?></p>
				<div class="thread-header">
					<h3><?=$header?></h3>
				</div>

	<?php
		foreach ($topic as $key => $value)
		{
			$value->message = $this->convert_from_bbcode($value->message);
			if ($users[$value->login]['banned']) $banned = ' banned';
	?>
				<div class="topic-container<?=$banned?>">
					<img class="topic-avatar" src="<?=USERS_DIR_HTTP.'\\'.$users[$value['login']]['avatar']?>">
					<div class="topic-login">
						<a href="/user/profile/<?=strtolower($value['login'])?>"><?=ucfirst($value['login'])?></a>
					</div>
					<div class="topic-message">
						<span><?=$value['message']?></span>
						<div class="topic-time"><?=date('d-m G:i', $value['time'])?></div>
	<?php
			if (isset($_SESSION['login']) && !empty($_SESSION['login']))
			{
				if ( (($_SESSION['login'] === $value->login) && (!$banned) ) || ($_SESSION['login'] === 'admin') ):
	?>
							<a class="silent msg-edit" onclick="editMsg(this,'<?=$id?>','<?=$value->id?>');">
								<i class="icon-pen icon1x" aria-hidden="true"></i>
							</a>
	<?php
					if ($value->id != 1):
	?>
							<a class="silent msg-delete" href="/forum/delmsg/<?=$id.'/'.$value->id?>">
								<i class="icon-trash_can icon1x" aria-hidden="true"></i>
							</a>
	<?php
					endif;
				endif;
			}
			$banned = NULL;
	?>
					</div> <!-- topic-message -->
				</div> <!-- topic-container -->
	<?php
		}
	?>
				<script>
					$('.topic-message').each(function(){
						if ($(this).height() < 50)
						{
							$(this).parent().addClass('small-msg');
						}
					});
					function editMsg(href, topicId, msgId)
					{
						var topicMsg = href.parentNode.childNodes[1];
						var txtMsg = topicMsg.innerHTML;
						txtMsg = txtMsg.replace(/<img class="forum-smile" src="[^"]{1,100}Smiles\/([0-9]{2})\/([0-9a-z_-]{2,24}).[a-z]{3,4}"[^>]{1,34}>/g, "[$1-$2]");
						txtMsg = txtMsg.replace(/<q>/g, '[quote]');
						txtMsg = txtMsg.replace(/<\/q><br>/g, '[\/quote]');
						txtMsg = txtMsg.replace(/<b>/g, '[b]');
						txtMsg = txtMsg.replace(/<\/b>/g, '[\/b]');
						txtMsg = txtMsg.replace(/<var>/g, '[i]');
						txtMsg = txtMsg.replace(/<\/var>/g, '[\/i]');
						txtMsg = txtMsg.replace(/<u>/g, '[u]');
						txtMsg = txtMsg.replace(/<\/u>/g, '[\/u]');
						txtMsg = txtMsg.replace(/<del>/g, '[del]');
						txtMsg = txtMsg.replace(/<\/del>/g, '[\/del]');
						// console.log(txtMsg);
						topicMsg.innerHTML = '<form onsubmit="void(null);" class="forms" method="post" action="/forum/editmsg">'+
							'<input type="hidden" name="editmsg" value="true"><input type="hidden" name="topicid" value="'+topicId+'">'+
							'<input type="hidden" name="msgid" value="'+msgId+'">'+
							'<textarea name="message">'+txtMsg+'</textarea>'+
							'<input type="submit" value="save"></form>';
					}
				</script>
	<?php
		if ($this->auth === 'not_banned'): ?>

				<form onsubmit="void(null);" class="new-msg forms" method="post" action="/forum/newmsg/<?=$id?>">
					<textarea id="msg" rows="5" cols="70" name="msg" required placeholder="Писать здесь!"></textarea>
					<input type="hidden" name="newmsg" value="true">
					<button class="button-large" type="submit">
					<i class="icon-pen" aria-hidden="true"></i>Ответить</button>
				</form>

				<button class="button-small bbcode" onclick="bbCode('b');"><b>B</b></button>
				<button class="button-small bbcode" onclick="bbCode('i');"><var>I</var></button>
				<button class="button-small bbcode" onclick="bbCode('u');"><u>U</u></button>
				<button class="button-small bbcode" onclick="bbCode('del');"><del>Del</del></button>
				<span class="bbcode">Выделите текст для обрачивания в тэг</span>

				<div class="smile-container">
	<?php
			foreach ($this->smiles as $key => $value):
	?>
				<button class="smile-button" onclick="smileInsert('<?=$key?>');" style="background: url(<?=SMILES_DIR_HTTP?><?=$value?>); background-size: contain;"></button>
	<?php
			endforeach;
	?>
				</div>
				<script>
					function bbCode(tag)
					{
						var Msg = document.getElementById('msg');
						var txt = Msg.value;
						var startPoint = Msg.selectionStart;
						var endPoint = Msg.selectionEnd;
						if (startPoint == endPoint)
						{
							Msg.value = txt.substring(0, startPoint)+'['+tag+']'+'[\/'+tag+']'+txt.substring(endPoint);
						}
						else
						{
							Msg.value = txt.substring(0, startPoint)+'['+tag+']'+txt.substring(startPoint, endPoint)+'[\/'+tag+']'+txt.substring(endPoint);
						}

						// console.log(Msg.value);
					}
					// $('#msg').select(function(){console.log(document.getElementById('msg').selectionStart);});
					function smileInsert(code)
					{
						// console.log(code);
						var Msg = document.getElementById('msg');
						var txt = Msg.value;
						var endPoint = Msg.selectionEnd;
						Msg.value = txt.substring(0, endPoint)+'['+code+']'+txt.substring(endPoint);
					}
				</script>
	<?php
		else:
	?>

				<form onsubmit="void(null);" class="new-msg forms" method="post" action="#" disabled>
					<textarea id="msg" rows="5" cols="70" name="msg" required placeholder="Авторизуйтесь для отправки комментария" disabled></textarea>
					<input type="hidden" name="newmsg" value="true">
					<button class="button-large" disabled>
					<i class="icon-pen" aria-hidden="true"></i>Ответить</button>
				</form>

				<button class="button-small bbcode" onclick="void(null);" disabled ><b>B</b></button>
				<button class="button-small bbcode" onclick="void(null);" disabled ><var>I</var></button>
				<button class="button-small bbcode" onclick="void(null);" disabled ><u>U</u></button>
				<button class="button-small bbcode" onclick="void(null);" disabled ><del>Del</del></button>
				<span class="bbcode">Выделите текст для обрачивания в тэг</span>

				<div class="smile-container">
	<?
			foreach ($this->smiles as $key => $value):
	?>
				<button class="smile-button" onclick="void(null);" style="background: url(<?=SMILES_DIR_HTTP?><?=$value?>); background-size: contain;" disabled></button>
	<?php
			endforeach;
	?>
				</div>
	<?php
		endif;
	?>
				<a href="<?=$this->backpath?>" class="navigation back"><i class="icon-reply icon2x" aria-hidden="true"></i><span>Назад</span></a>
			</div> <!-- main -->

	<?php
		$this->get_footer();
	}
}
