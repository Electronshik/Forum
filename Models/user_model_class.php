<?php
class user_model extends base_model
{
	public function add_new_user($login, $password, $avatar)
	{
		$users = R::dispense( 'users' );
		$users->login = strtolower($login);
		$users->password = $password;
		$users->date = time();
		$users->avatar = $avatar ? $avatar : 'avatar_def.jpg';
		$users->messages = 0;
		$users->about = '';
		$users->downloads = [];
		$users->downloads = serialize($users->downloads);
		$users->likes = [];
		$users->likes = serialize($users->likes);
		$users->banned = FALSE;
		R::store( $users );
	}
	public function edit_user($login, $avatar)
	{
		$users = R::findOne( 'users', 'login= :login', [':login' => $login]);
		$users->avatar = $avatar;
		R::store( $users );
	}
	public function check_user($login, $password)
	{
		$user = R::findOne( 'users', 'login= :login', [':login' => $login]);
		if ($user->password === $password)
		{
			return $auth = TRUE;
		}
		else
		{
			return $auth = FALSE;
		}
	}
	public function get_profile($login)
	{
		$user = R::findOne( 'users', 'login= :login', [':login' => $login]);
		if ($user)
		{
			$user->likes = unserialize($user->likes);
			return $user;
		}
		else
		{
			return FALSE;
		}
	}
	public function send_message($from, $to, $message)
	{
		$mess = R::dispense( 'messages' );
		$mess->fromlogin = $from;
		$mess->tologin = $to;
		$mess->message = $message;
		$mess->time = time();
		$mess->unreaded = 1;
		R::store($mess);
	}
	public function get_inbox($login)
	{
		$messages = R::findAll('messages', 'tologin = :login ORDER BY time DESC', [':login' => $login]);
		return $messages;
	}
	public function get_outbox($login)
	{
		$messages = R::findAll('messages', 'fromlogin = :login ORDER BY time DESC', [':login' => $login]);
		return $messages;
	}
	public function get_letter($id)
	{
		$letter = R::findOne('messages', 'id = :id', [':id' => $id]);
		return $letter;
	}
	public function get_unreaded($login)
	{
		$inbox = $this->get_inbox($login);
		$unreaded = 0;
		foreach ($inbox as $key => $value)
		{
			if ($value->unreaded == 1)
			{
				$unreaded++;
			}
		}
		return $unreaded;
	}
	public function clear_unreaded($login)
	{
		$msg = R::findAll('messages', 'tologin = :login', [':login' => $login]);
		foreach ($msg as $key => $value)
		{
			if ($value->unreaded)
			{
				$value->unreaded = 0;
				R::store($msg[$key]); //Redbean don't save the entire array returned from "findAll" array, only a "bean", or I don't know how to do it
			}
		}
	}
	public function download_file($login, $filename, $type, $name, $about)
	{
		$files = R::dispense('files');
		$files->filename = $filename;
		$files->type = $type;
		$files->login = $login;
		$files->date = time();
		$files->name = $name;
		$files->about = $about;
		$files->likes = 0;
		$file_id = R::store($files);
		$user = R::findOne('users', 'login= :login', [':login' => $login]);
		if ($user->downloads)
		{
			$downloads = unserialize($user->downloads);
		}
		$downloads[] = $file_id;
		$user->downloads = serialize($downloads);
		R::store($user);

	}
	public function delete_file($id, $login)
	{
		$file = R::findOne( 'files', 'id= :id', [':id' => $id]);
		if ($file->login !== $login)
		{
			return FALSE;
		}
		$users = R::findAll('users'); //all likes for all users
			foreach ($users as $key => $value)
			{
				$user = R::findOne('users', 'login= :login', [':login' => $value->login]);
				$likes = unserialize($user->likes);
				if ( !empty($likes) && in_array($id, $likes) ) //if user liked file, remove the like
				{
					$arr_id = array_search($id, $likes);
					unset($likes[$arr_id]);
					if (!empty($likes))
					{
						$likes = array_values($likes);
					}
					$user->likes = serialize($likes);
					R::store($user);
				}
			}
		$user = R::findOne('users', 'login= :login', [':login' => $login]);
		$downloads = unserialize($user->downloads); //remove file from user's downloads
		if ( !empty($downloads) && in_array($id, $downloads) )
		{
			$arr_id = array_search($id, $downloads);
			unset($downloads[$arr_id]);
			if (!empty($downloads))
			{
				$downloads = array_values($downloads);
			}
			$user->downloads = serialize($downloads);
		}
		R::store($user);
		// $file = R::findOne( 'files', 'id= :id', [':id' => $id]);
		$filename = $file->filename;
		R::trash($file);
		return $filename;
	}
	public function get_downloads($login)
	{
		$files = R::findAll( 'files', 'login= :login', [':login' => $login]);
		return $files;
	}
	public function get_all_downloads()
	{
		$files = R::findAll( 'files' , ' ORDER BY id DESC LIMIT 10 ' );
		return $files;
	}
	public function get_users()
	{
		$users = R::findAll( 'users' , ' ORDER BY date DESC LIMIT 10 ' );
		return $users;
	}
	public function user_liked_file($id, $login)
	{
		$user = R::findOne('users', 'login= :login', [':login' => $login]);
		$user->likes = unserialize($user->likes);
		if ( !empty($user->likes) && in_array($id, $user->likes) )
		{
			$arr_id = array_search($id, $user->likes);
			unset($user->likes[$arr_id]);
			if ( !empty($user->likes) )
			{
				$user->likes = array_values($user->likes);
			}
			$user->likes = serialize($user->likes);
			R::store($user);
			$files = R::findOne( 'files', 'id= :id', [':id' => $id]);
			if ( $files->likes > 0 )
			{
				$files->likes = $files->likes - 1;
			}
			else
			{
				$files->likes = 0;
			}
			R::store($files);
		}
		else
		{
			$user->likes[] = $id;
			$user->likes = serialize($user->likes);
			R::store($user);
			$files = R::findOne( 'files', 'id= :id', [':id' => $id]);
			$files->likes = $files->likes + 1;
			R::store($files);
		}
	}
	public function delete_message($id)
	{
		$msg = R::findOne( 'messages', 'id= :id', [':id' => $id]);
		R::trash($msg);
	}
}