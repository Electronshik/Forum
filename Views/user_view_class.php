<?php
class user_view extends base_view
{
	// use base_view;
	public function index(){}

	public function signup()
	{
		$this->get_header();
	?>
		<div id="main">
			<form class="register" enctype="multipart/form-data" method="post" action="/user/signup">
				<input type="text" name="login" placeholder="login" required>
				<input type="password" name="password" placeholder="password" required>
				<label class="file-container" >Avatar<input type="file" name="avatar" accept="image/*" id="preview-image" onchange="getFileInfo();"></label>
				<input type="hidden" name="signup" value="true">
				<button class="new-topic" type="submit">Signup</button>
			</form>
			<img class="profile-preview" id="profile-preview" src="">
		</div>
		<script>
			function getFileInfo()
			{
				var preview = document.getElementById('preview-image');
			    if (preview.files && preview.files[0])
			    {
			        var reader = new FileReader();

			        reader.onload = function (e) {
			        	var img = document.getElementById('profile-preview');
			        	img.src = e.target.result;
						img.style.display = 'block';
			        };

			        reader.readAsDataURL(preview.files[0]);
			    }
			}
		</script>
	<?php
		$this->get_footer();
	}

	public function profile($user, $auth, $pers, $admin)
	{
		$this->get_header();
	?>
		<div id="main">
			<div class="profile-container">
				<img class="profile-image" src="<?=USERS_DIR_HTTP.$user->avatar?>">
				<p class="profile-login"><?=ucfirst($user->login)?></p>
				<p class="profile-date">Зарегистрирован:&nbsp<?=date('d-m-Y', $user->date)?></p>
				<p class="profile-date">Сообщений:&nbsp<?=$user['messages']?></p>
	<?php
		if ($pers):
	?>
				<a href="/user/edit" class="navigation">Редактировать</a>
				<br><br>
				<a href="/user/personal" class="navigation">Личный кабинет</a>
				<br><br>
				<a href="/user/addfile" class="navigation">Загрузить файл</a>
	<?php
			if ($admin)
			{
	?>
				<br><br><a href="/admin" class="new-topic navigation">Админка</a>
	<?php
			};
		elseif ($auth):
	?>
			<button class="button-large" id="button-mess" style="margin-left: 20px;" onclick="openMess();">
				<i class="icon-pen icon1x" aria-hidden="true"></i>Сообщение</button>
			<form method="post" class="forms" onsubmit="void(null);" action="/user/profile/<?=$user->login?>" id="form-mess" style="display: none;">
				<textarea name="message" placeholder="Придумай что-то умное.." required></textarea>
				<input type="hidden" name="user_mess" value="true">
			</form>
			<script>
				function openMess()
				{
					var form = document.getElementById('form-mess');
					if(form.style.display != 'block')
					{
						form.style.display = 'block';
						$(form).addClass('animated lightSpeedIn');
						var button = document.getElementById('button-mess');
						button.innerHTML = '<i class="icon-arrow_right icon1x" aria-hidden="true"></i>Отправить';
					}
					else
					{
						if ($(form).attr('action'))
        				{
        					// $('.lightSpeedIn').addClass('lightSpeedOut');
        				  // setPage($(this).attr('href'), false);
        				  console.log($(form).attr('action'));

        				  var msg = $(form).serialize();
        				  if(($('textarea').val() != '') && ($('textarea').val() != 'Тут же пусто!'))
        				  {
        				  	$('.lightSpeedIn').addClass('lightSpeedOut');
        				  	setTimeout( function(){
        				  		console.log(msg);
        				  		$.ajax({
        				  		type: 'POST',
        				  		url: $(form).attr('action'),
        				  		data: msg,
        				  		success: function(data) {
        				  		  $('#ajax-container').html(data);
        				  		},
        				  		error:  function(xhr, str){
        				  		alert('Возникла ошибка: ' + xhr.responseCode);
        				  		}
        				  		});
        				  	}, 700);
        				  }
        				  else
        				  {
        				  	$('textarea').attr('placeholder', 'Тут же пусто!');
        				  }
        				}
						// form.submit();
					}
				}
			</script>
	<?php
		else:
	?>
			<br><br><p style="font-size: 14px;">Авторизуйтесь, чтобы заспамить этого юзера</p>
	<?php
		endif;
	?>
			</div> <!-- profile-container -->
			<a href="'.$this->backpath.'" class="navigation back"><i class="icon-reply icon2x" aria-hidden="true"></i><span>Назад</span></a>
		</div> <!-- main -->
	<?php
		$this->get_footer();
	}

	public function personal($user, $inbox, $outbox, $downloads, $dest, $letter)
	{
		$this->get_header();
	?>
		<div id="main">
			<div class="profile-container">
				<div class="profile-left-column">
					<img class="profile-image" src="<?=USERS_DIR_HTTP.$user->avatar?>">
					<p class="profile-login"><?=ucfirst($user->login)?></p>
					<p class="profile-date">Зарегистрирован:&nbsp<?=date('d-m-Y', $user->date)?></p>
					<p class="profile-date">Сообщений:&nbsp<?=$user['messages']?></p>
					<a href="/user/edit/<?=$user->login?>" class="navigation">Редактировать</a>
				</div> <!-- profile-left-column -->
	<?php
		$incount = count($inbox);
		$outcount = count($outbox);
		$checked = array();
		$mail_checked[1] = 'checked';
		if ( $dest === 'downloads' )
		{
			$checked[3] = 'checked';
		}
		else
		{
			$checked[1] = 'checked';
			if ( $dest === 'sent' )
			{
				$mail_checked[2] = 'checked';
				unset($mail_checked[1]);
			}
		}
		// $refinbox = [ '<a href="/user/personal/inbox">', '</a>' ];
		// $refsent = [ '<a class="letter-active" href="/user/personal/sent">', '</a>' ];
		if ($letter->sent xor $letter->inbox)
		{
			if ($letter->inbox)
			{
				$mail_checked[1] = 'checked';
			}
			else
			{
				$mail_checked[2] = 'checked';
			}
			$mail_text = '<h4>'.ucfirst($letter->fromlogin).' to '.ucfirst($letter->tologin).':</h4><p>'.$letter->message.'</p>';
			$letter_text = TRUE;
			$refinbox = [ '<a class="letter-active navigation" href="/user/personal/inbox">', '</a>' ];
			$refsent = [ '<a class="letter-active navigation" href="/user/personal/sent">', '</a>' ];
		}
	?>
				<ul class="tabs">
					<li>
						<input type="radio" name="tabs" id="tab-1" <?=$checked[1]?> >
						<label for="tab-1">Почта</label>
						<div class="tab-content">
								<ul class="tabs mail-tabs">
									<li>
										<input type="radio" name="mail-tabs" id="mail-tab-1" <?=$mail_checked[1]?>>
										<label for="mail-tab-1"><?=$refinbox[0]?>Вход (<?=$incount?>)<?=$refinbox[1]?></label>
										<div class="tab-content mail-tab-content">
	<?php
				if (!$letter_text)
				{
					foreach ($inbox as $key => $value):
	?>
						<a href="/user/personal/inbox/<?=$value['id']?>" class="navigation">
							<span class="from">От: </span>
							<span class="author"><?=ucfirst($value['fromlogin'])?></span>
							<span class="message"><?=$value['message']?></span>
							<span class="date"><?=date('d-m G:i', $value['time'])?></span>
						</a>
						<a class="silent" href="/user/delmsg/inbox/<?=$value->id?>"><i class="icon-trash_can icon1x" aria-hidden="true"></i></a>
	<?php
					endforeach;
				}
				else
				{
					if ($mail_checked[1] == 'checked'):
	?>
						<?=$mail_text?>
						<form onsubmit="void(null);" class="new-msg forms" style="margin: 0 auto; method="post" action="/user/profile/<?=$letter->fromlogin?>">
							<textarea name="message" required placeholder="Ответить"></textarea>
							<input type="hidden" name="user_mess" value="true">
							<input type="hidden" name="from_personal" value="true">
							<button type="submit" class="new-topic">Send</button>
						</form>
	<?php
					endif;
				}
	?>
										</div>
									</li>
									<li>
										<input type="radio" name="mail-tabs" id="mail-tab-2" <?=$mail_checked[2]?> >
										<label for="mail-tab-2"><?=$refsent[0]?>Отпр (<?=$outcount?>)<?=$refsent[1]?></label>
										<div class="tab-content mail-tab-content">
	<?php
				if (!$letter_text)
				{
					foreach ($outbox as $key => $value):
	?>
						<a href="/user/personal/sent/<?=$value['id']?>" class="navigation">
							<span class="from">To: </span>
							<span class="author"><?=ucfirst($value['tologin'])?></span>
							<span class="message"><?=$value['message']?></span>
							<span class="date"><?=date('d-m G:i', $value['time'])?></span>
						</a>
						<a class="silent" href="/user/delmsg/sent/<?=$value->id?>"><i class="icon-trash_can icon1x" aria-hidden="true"></i></a>
	<?php
					endforeach;
				}
				else
				{
					if ($mail_checked[2] == 'checked'):
	?>
						<?=$mail_text?>
	<?php
					endif;
				}
	?>
										</div>
									</li>
								</ul>
						<div> <!--tab-content-->
					</li>
					<li>
						<input type="radio" name="tabs" id="tab-2" <?=$checked[2]?> >
						<label for="tab-2">Информация</label>
						<div class="tab-content">
							<div class="about">
								<p><?=$user['about']?></p>
							</div>
						<div> <!--tab-content-->
					</li>
					<li>
						<input type="radio" name="tabs" id="tab-3" <?=$checked[3]?> >
						<label for="tab-3">Загрузки</label>
						<div class="tab-content">
	<?php
				foreach ($downloads as $key => $value):
	?>
							<div class="music-files">
								<a><i class="icon-heart liked" aria-hidden="true"></i></a><span class="likes"><?=$value->likes?></span>
								<a class="icon-ref" onclick="playFile('<?=$value['filename']?>', '<?=$value['name']?>');"><i class="icon-play" aria-hidden="true"></i></a>
								<span class="header"><?=$value['name']?></span>
								<span class="time"><?=date('d-m G:i', $value['date'])?></span>
								<a class="silent" href="/user/delfile/<?=$value->id?>"><i class="icon-trash_can icon1x" aria-hidden="true"></i></a>
								<span class="about"><?=$value['about']?></span>
							</div>
	<?php
				endforeach;
	?>
						<div> <!--tab-content-->
					</li>
				</ul>
			</div> <!-- profile-container -->
			<script>
				function playFile(filename, name)
				{
					console.log('player');
					var player = document.getElementById('player-audio');
					player.src = 'http://<?=ROOT?>/Views/Media/Files/'+filename;
					player.play();
					player.style.display = 'block';
					var playerLabel = document.getElementById('player-label');
					playerLabel.innerText = name;
				}
			</script>
		<a href="/user/profile/<?=$user->login?>" class="navigation back"><i class="icon-reply icon2x" aria-hidden="true"></i><span>Назад</span></a>
		</div> <!-- main -->
	<?php
		$this->get_footer();
	}

	public function edit($user)
	{
		$this->get_header();
	?>
			<div id="main">
	<?php
			if (isset($_SESSION['login']) && !empty($_SESSION['login']))
			{
				if ($user->login === $_SESSION['login']):
	?>
				<div class="profile-container">
					<img class="profile-image" id="profile-preview" src="<?=USERS_DIR_HTTP.$user->avatar?>">
					<p class="profile-login"><?=ucfirst($user->login)?></p>
					<form class="register" method="post" action="/user/edit/<?=$user->login?>" enctype="multipart/form-data">
						<label class="file-container" >Avatar<input type="file" name="avatar" accept="image/*" id="preview-image" onchange="getFileInfo();"></label>
						<input class="new-topic" type="submit" value="Save">
						<input type="hidden" name="edit" value="true">
					</form>
				</div>
				<script>
				function getFileInfo()
				{
					var preview = document.getElementById('preview-image');
				    if (preview.files && preview.files[0])
				    {
				        var reader = new FileReader();

				        reader.onload = function (e) {
				        	var img = document.getElementById('profile-preview');
				        	img.src = e.target.result;
							img.style.display = 'block';
				        };

				        reader.readAsDataURL(preview.files[0]);
				    }
				}
				</script>
	<?php
				endif;
			}
	?>
				<a href="/user/profile/'.$user->login.'" class="navigation back"><i class="icon-reply icon2x" aria-hidden="true"></i><span>Назад</span></a>
			</div>
	<?php
		$this->get_footer();
	}
	public function addfile($user, $downloads)
	{
		$this->get_header();
	?>
			<div id="main">
	<?php
			if (isset($_SESSION['login']) && !empty($_SESSION['login']))
			{
				if ($user->login === $_SESSION['login'])
				{
	?>
				<div class="profile-container">
					<img class="profile-image" id="profile-preview" src="<?=USERS_DIR_HTTP.$user->avatar?>">
					<p class="profile-login"><?=ucfirst($user->login)?></p>
					<form class="register" method="post" action="/user/addfile/<?=$user->login?>" enctype="multipart/form-data">
						<input type="text" name="name" id="file-name" placeholder="Название" required>
						<input style="width: 340px; position: relative; left: 32px;" type="text" name="about" placeholder="Краткое описание">
						<label class="file-container" value="asd">Выбрать<input type="file" name="download" accept="audio/mp3" id="preview-image" onchange="getFileInfo();"></label><span id="file-label" style="display:block;width: 320px;"></span>
						<input class="new-topic" id="send-button" style="display:none;" type="submit" value="Download">
						<input type="hidden" name="addfile" value="true">
						<input type="hidden" id="file-type" name="type" value="mp3">
					</form>
				</div>
	<?php
					foreach ($downloads as $key => $value):
	?>
						<p><?=$value['name']?></p>
						<audio src="<?=FILES_DIR_HTTP.$value->filename?>" controls></audio>
	<?php
					endforeach;
	?>
				<script>
				function getFileInfo()
				{
					var preview = document.getElementById('preview-image');
				    if (preview.files && preview.files[0])
				    {
						var pos = preview.files[0].name.search('.mp3');
						if (pos > 0)
						{
							document.getElementById('file-label').innerText = preview.files[0].name;
							document.getElementById('file-name').value = preview.files[0].name.substr(0, pos);
							document.getElementById('file-type').value = preview.files[0].type;
							var sendButton = document.getElementById('send-button');
							sendButton.style.display = 'block';
						}
				    }
				}
				</script>
	<?php
				}
			}
	?>
				<a href="/user/profile/<?=$user->login?>" class="navigation back"><i class="icon-reply icon2x" aria-hidden="true"></i><span>Назад</span></a>
			</div>
	<?php
		$this->get_footer();
	}
}