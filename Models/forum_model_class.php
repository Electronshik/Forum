<?php
class forum_model extends base_model
{
	public function get_categories()
	{
		$cats = R::find('categories');
		// foreach ($cat as $key => $value)
		// {
		// 	$categories[$cat[$key]->bind] = $cat[$key]->title;
		// }
		return $cats ? $cats : FALSE;
	}
	public function get_category($id)
	{	//дает категорию по id СУБКАТЕГОРИИ
		$subcat = R::findOne('subcategories', 'id = :id', [':id' => $id]);
		if ($subcat)
		{
			$cat = R::findOne('categories', 'bind = :bind', [':bind' => $subcat['bind']]);
		}
		return $cat ? $cat : FALSE;
	}
	public function get_subcategory($id)
	{
		$subcat = R::findOne('subcategories', 'id = :id', [':id' => $id]);
		return $subcat ? $subcat : FALSE;
	}
	public function get_subcategories($bind)
	{
		$subs = R::find( 'subcategories', 'bind = :bind', [':bind' => $bind]);
		if ($subs)
		{
			foreach ($subs as $key => $value)
			{
				$subcats[$key] = $subs[$key];
			}
		}
		return $subcats ? $subcats : FALSE;
	}
	public function get_all_subcategories()
	{
		$cats = $this->get_categories();
		foreach ($cats as $key => $value)
		{
			$data[$cats[$key]['bind']] = $this->get_subcategories($cats[$key]['bind']);
		}
		return $data ? $data : FALSE;
	}
	public function get_thread($id)
	{
		$threads = R::find('threads'.$id);
		return $threads ? $threads : FALSE;
	}
	public function new_topic($header, $message, $subid, $login)
	{
		$retid = $subid;
		$subid = 'threads'.$subid;
		$subcats = R::dispense( $subid );
		$subcats->header = $header;
		$subcats->message = $message;
		$subcats->login = $login;
		$subcats->time = time();
		$subcats->messages = 1;
		$id = R::store( $subcats );
		$topic_count = count(R::findAll( $subid ));
		$subcat = R::findOne('subcategories', 'id = :id', [':id' => $retid]);
		$subcat->topics = $topic_count;
		R::store($subcat);
		$retid = $retid.'-'.$id;
		$id = $subid.'t'.$id;
		$topic = R::dispense( $id );
		$topic->login = $login;
		$topic->message = $message;
		$topic->time = time();
		R::store( $topic );
		$user = R::findOne( 'users', 'login= :login', [':login' => $login]);
		$user->messages = $user->messages + 1;
		R::store($user);
		return $retid;
	}
	public function get_topic($id)
	{
		$id = str_replace('-', 't', $id);
		$id = 'threads'.$id;
		$topic = R::find( $id );
		return $topic ? $topic : FALSE;
	}
	public function delete_topic($id)
	{
		$topic = $this->get_topic($id);
		if ($topic)
		{
			R::trash($topic);
		}
	}
	public function get_message($topic_id, $msg_id)
	{
		$topic_id = str_replace('-', 't', $topic_id);
		$topic_id = 'threads'.$topic_id;
		$msg = R::findOne( $topic_id, 'id= :msg_id', [':msg_id' => $msg_id]);
		return $msg ? $msg : FALSE;
	}
	public function delete_message($topic_id, $msg_id)
	{
		$thread_id = 'threads'.explode('-', $topic_id)[0];
		$thread_topic_id = explode('-', $topic_id)[1];
		$topic_id = str_replace('-', 't', $topic_id);
		$topic_id = 'threads'.$topic_id;
		$msg = R::findOne( $topic_id, 'id= :msg_id', [':msg_id' => $msg_id]);
		if ($msg)
		{
			$user = R::findOne('users', 'login= :login', ['login' => $msg->login]);
			if ($user->messages > 0)
			{
				$user->messages = $user->messages - 1;
				R::store($user);
			}
			R::trash($msg);
			$msg_count = R::findAll( $topic_id );
			if ($msg_count)
			{
				$msg_count = count($msg_count);
				$thread = R::findOne( $thread_id, 'id= :thread_topic_id', [':thread_topic_id' => $thread_topic_id]);
				$thread->messages = $msg_count;
				R::store($thread);
			}
		}
	}
	public function edit_message($topic_id, $msg_id, $msg)
	{
		$topic_id = str_replace('-', 't', $topic_id);
		$topic_id = 'threads'.$topic_id;
		$message = R::findOne( $topic_id, 'id= :msg_id', [':msg_id' => $msg_id]);
		if ($message)
		{
			$message->message = $msg;
			$message->time = time();
			$message->edited = 1;
			R::store($message);
		}
	}
	public function get_topic_users_data($id)
	{
		$id = 'threads'.$id;
		$users = R::getAssoc('SELECT DISTINCT login FROM '.$id);
		if ($users)
		{
			foreach ($users as $key => $value)
			{
				$user = R::findOne('users', 'login= :login', ['login' => $value]);
				$users[$key] = $user;
			}
		}
		return $users ? $users : FALSE;
	}
	public function new_msg($id, $login, $msg)
	{
		$msg_id = 'threads'.$id;
		$message = R::dispense( $msg_id );
		$msg_count = count(R::findAll($msg_id));
		$message->login = $login;
		$message->message = $msg;
		$message->time = time();
		if(R::store($message))
		{
			$user = R::findOne('users', 'login= :login', ['login' => $login]);
			$user->messages = $user->messages + 1;
			R::store( $user );
			$msg_count++;
			// echo $msg_count;
			$sub_id = 'threads'.explode('t', $id)[0];
			$thread_id = explode('t', $id)[1];
			$thread = R::findOne( $sub_id, 'id = :id', [':id' => $thread_id]);
			$thread->messages = $msg_count;
			R::store( $thread );
			return TRUE;
		}
		return FALSE;
	}
	public function check_ban($login)
	{
		$user = R::findOne('users', 'login= :login', ['login' => $login]);
		if (isset($user->banned))
		{
			if ($user->banned)
			{
				return 'banned';
			}
			else
			{
				return 'not_banned';
			}
		}
		else
		{
			return FALSE;
		}
	}
}