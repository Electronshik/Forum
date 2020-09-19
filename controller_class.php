<?php
abstract class controller extends Error
{
	protected $view;
	protected $post;
	protected $auth = FALSE;

	public function __construct()
	{
		$view_name = explode('_', get_class($this))[0].'_view';
		$this->view = new $view_name();
		if ( (isset($_SESSION['login'])) && (!empty($_SESSION['login'])) )
		{
			$this->auth = TRUE;
		}
	}

	protected function clear_input($input)
	{
		$input = htmlspecialchars(substr($input, 0, 1024));
		$input = trim(strip_tags($input));
		$input = trim($input,'\,." /|.,:;');
		return $input;
	}

	protected function prepare_post()
	{
		if ((count($_POST) < 10) && (count($_POST) >= 1))
		{
			$this->post = array_map($this->clear_input, $_POST);
			$this->post = array_change_key_case($this->post, CASE_LOWER);
		}
		else
		{
			$error = new error_model();
			$error->add_error('count $_POST < 1 or > 10');
			header('Location: /');
			exit();
		}
	}

	abstract public function index();

	public static function log($text, $num = 0)
	{
		base_model::log(get_called_class(), debug_backtrace()[1]['function'], $text, $num);
	}
}

trait translate
{
	private $translit_replace = array(
		"А"=>"A","а"=>"a",
		"Б"=>"B","б"=>"b",
		"В"=>"V","в"=>"v",
		"Г"=>"G","г"=>"g",
		"Д"=>"D","д"=>"d",
		"Е"=>"E","е"=>"e",
		"Ё"=>"E","ё"=>"e",
		"Ж"=>"Zh","ж"=>"zh",
		"З"=>"Z","з"=>"z",
		"И"=>"I","и"=>"i",
		"Й"=>"I","й"=>"i",
		"К"=>"K","к"=>"k",
		"Л"=>"L","л"=>"l",
		"М"=>"M","м"=>"m",
		"Н"=>"N","н"=>"n",
		"О"=>"O","о"=>"o",
		"П"=>"P","п"=>"p",
		"Р"=>"R","р"=>"r",
		"С"=>"S","с"=>"s",
		"Т"=>"T","т"=>"t",
		"У"=>"U","у"=>"u",
		"Ф"=>"F","ф"=>"f",
		"Х"=>"Kh","х"=>"kh",
		"Ц"=>"Tc","ц"=>"tc",
		"Ч"=>"Ch","ч"=>"ch",
		"Ш"=>"Sh","ш"=>"sh",
		"Щ"=>"Shch","щ"=>"shch",
		"Ы"=>"Y","ы"=>"y",
		"Э"=>"E","э"=>"e",
		"Ю"=>"Iu","ю"=>"iu",
		"Я"=>"Ia","я"=>"ia",
		"ъ"=>"","ь"=>""
	);

	private function translate_filename($string)
	{
		return strtr($string, $this->translit_replace);
	}
}