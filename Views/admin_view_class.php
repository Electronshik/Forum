<?php
class admin_view extends base_view
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
				<br>
				<a href="/admin/addarticle" class="new-topic">Добавить статью</a>
			</div>
			<a href="<?=$this->backpath?>" class="navigation back">
				<i class="icon-reply icon2x" aria-hidden="true"></i>
				<span>Назад</span>
			</a>
		</div> <!-- main -->
	<?php
		$this->get_footer();
	}

	public function addarticle()
	{
		$this->get_header();
	?>
		<div id="main">
			<p class="bread"></p>
				<div class="thread-header">
					<h3>Admin is god here!</h3>
				</div>
			<img style="width: 100px; height: 100px; float: left; object-fit: fill; margin: 25px 0 0 20px;" id="profile-preview" src="">

			<form class="new-msg register" style="width: 90%;"  method="post" action="/admin/addarticle" enctype="multipart/form-data">
				<textarea rows="2" cols="70" name="header" required placeholder="Header"></textarea>
				<label class="file-container" >Image<input type="file" name="image" accept="image/*" id="preview-image" onchange="getFileInfo();"></label>
				<textarea rows="50" cols="70" name="text" required placeholder="Text"></textarea>
				<input type="hidden" name="addarticle" value="true">
				<button class="new-topic" type="submit">
				<i class="icon-pen" aria-hidden="true"></i>Add article</button>
			</form>
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
			<a href="<?=$this->backpath?>" class="navigation back">
				<i class="icon-reply icon2x" aria-hidden="true"></i>
				<span>Назад</span>
			</a>
		</div>
	<?php
		$this->get_footer();
	}
}