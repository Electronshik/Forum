<?php
class music_view extends base_view
{
	// use base_view;
	public function index($files = NULL, $user = NULL)
	{

		$this->get_header();
	?>
		<div id="main">
			<p class="bread"></p>
			<div class="thread-header">
				<h3 style="width: 48%">Последние загруженные</h3>
				<h4>Загрузил</h4>
			</div>
	<?php
		foreach ($files as $key => $value)
		{
			$liked = '';
			if ( $user )
			{
				$href =  ' href="/music/liked/'.$value['id'].'"';
				if ( !empty($user->likes) && in_array($value->id, $user->likes) ) $liked = ' liked';
			}
			else
			{
				$href =  ' style="cursor: default"';
			}
			$type = ($value['type'] == 'audio/mpeg') ? 'mp3' : 'хз';
	?>
			<div class="music-files">
				<a class="icon-ref silent" <?=$href?>><i class="icon-heart<?=$liked?>" aria-hidden="true"></i></a>
				<span class="likes"><?=$value->likes?></span>
				<a class="icon-ref" onclick="playFile('<?=$value['filename']?>','<?=$value['name']?>', this);"><i class="icon-play" aria-hidden="true"></i></a>
				<span class="header"><?=$value['name']?></span>
				<a class="icon-ref navigation" style="width: 100px" href="/user/profile/<?=$value['login']?>"><i class="icon-user" aria-hidden="true"></i><?=$value['login']?></a>
				<span class="time"><?=date('d-m G:i', $value['date'])?></span>
				<span class="about"><?=$value['about']?></span>
			</div>
	<?php
		}
	?>
			<script>
				function playFile(filename, name, block)
				{
					$('.flash').removeClass('flash');
					var player = document.getElementById('player-audio');
					player.src = 'http://<?=ROOT?>/Views/Media/Files/'+filename;
					player.play();
					player.style.display = 'block';
					var playerLabel = document.getElementById('player-label');
					playerLabel.innerText = name;
					$(block).next().addClass('animated flash');
					console.log($(block).next());
				}
			</script>
			<a href="/forum" class="navigation back"><i class="icon-reply icon2x" aria-hidden="true"></i><span>Назад</span></a>
		</div> <!-- main -->
	<?php
		$this->get_footer();
	}
}