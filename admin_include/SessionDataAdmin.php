<?php
class SessionDataAdmin
{
	function __construct()
	{
		session_start();
		if (time() - $_SESSION['last_use'] > ADMIN_SESSION_DURATION_LIM)
			$this->reset();
	}
	
	function reset()
	{
		session_unset();
		session_destroy();
		session_start();
	}


	function checkOrg($id)
	{
		return ($id == 'new') || ($this->user['fields']['Groups'] && (array_search($id, $this->user['fields']['Groups']) !== false));
	}
	

	function updateOrg($id)
	{
		if (!$this->neighborhoods)
		{
			$neighborhoods = AirtableAdmin::readTable('Ref - Neighborhoods');
			usort($neighborhoods, function ($a, $b) { 
				return $a['fields']['Neighborhood Name'] <=> $b['fields']['Neighborhood Name']; 
			});
			$this->neighborhoods = $neighborhoods;
		}	
		$this->org = $this->loadAirtableOrg($id);
		$this->orgIsNew = $this->org['is_new'];
	}
	
	function updateUser()
	{
		$this->user = $this->loadAirtableUser($this->email);
	}
	
	function isset($field)
	{
		return isset($_SESSION[$field]);
	}

	function setEmail($email)
	{
		if ($this->email == $email)
			return;
		$this->email = $email;
		$this->embeddedAuth = isset($_GET['embedded']);
		if (!$this->delayedEmailUpdate)					//
			$this->updateUser();						//
		$this->isNew = $this->user['is_new'];
		return $this->genAuthToken();
	}
	
	function setDelayedEmail()							///
	{
		global $AdminUserFormFields;
		$this->delayedEmailUpdate = false;
		$rr[$AdminUserFormFields['email']] = $this->email;
		$this->airtableResponse = AirtableAdmin::updateUser($this->user['id'], $rr);
		return $this->airtableResponse['records'] <> null;
	}
	
	function setProfile($dd)
	{
		global $AdminUserFormFields;
		$rr = [];
		$user = $this->user;
		$user['is_new'] = false;
		foreach ($AdminUserFormFields as $fv=>$fa)
			if ($_POST[$fv] && $_POST[$fv] <> $user['fields'][$fa])
			{
				if ($fv == 'email')										//	
					if (AirtableAdmin::checkUserByEmail($_POST[$fv]))		//
						$this->delayedEmailIsDouble = true;			//
					else
						$this->delayedEmailUpdate = true;			//
				else {										//
					$user['fields'][$fa] = $_POST[$fv];
					$rr[$fa] = $_POST[$fv];
				}
			}
			
		$this->user = $user;
		if (!$rr)
			return;
		$this->airtableResponse = AirtableAdmin::updateUser($this->user['id'], $rr);
		return $this->airtableResponse['records'] <> null;
	}
	
	function genAuthToken()
	{
		$this->token = md5(uniqid());
		return $this->token;
	}
	
	function checkAuthToken($token)
	{
		if ($this->token <> $token)
			return false;
		$this->token = null;
		return true;
	}
	
	function loadAirtableUser($email)
	{
		global $AdminUserFormFields;
		$raw = AirtableAdmin::findUserByEmail($email)['records'][0];
		$raw['fields'] = array_intersect_key($raw['fields'], array_fill_keys($AdminUserFormFields, true));
		return $raw;
	}

	
	function loadAirtableOrg($id)
	{
		global $AdminGrFormFields;
		$raw = AirtableAdmin::getOrg($id, $this->user['id']);
		$raw['fields'] = array_intersect_key($raw['fields'], array_fill_keys($AdminGrFormFields, true));
		return $raw;
	}

	function deleteOrg($id)
	{
		if ($id && ($id <> 'new') && $this->checkOrg($id))
			return AirtableAdmin::deleteOrg($id);
	}
	
	function getUserView()
	{
		global $AdminUserFormFields;
		$rr = ['orgs' => [], 'id' => $this->user['id']];
		foreach ($AdminUserFormFields as $fv=>$fa)
			switch ($fv)
			{
				case "group_id":
				case "group_name":
					$f = str_replace('group_', '', $fv);
					foreach ((array)$this->user['fields'][$fa] as $i=>$v)
						$rr['orgs'][$i][$f] = $v;
					break;
				case "group_posts_num":
					$f = str_replace('group_', '', $fv);
					foreach ((array)$this->user['fields'][$fa] as $i=>$v)
						$rr['orgs'][$i][$f] = "{$v} request" . ($v <> 1 ? 's' : '');
					break;
				case "group_last_modified":
					foreach ((array)$this->user['fields'][$fa] as $i=>$v)
						$rr['orgs'][$i]['last_modified'] = date('Y-m-d H:i:s', strtotime($v));
					break;
				default: 
					$rr[$fv] = $this->user['fields'][$fa];
			}
		return $rr;
	}
	
	
	function getIntercomView()
	{
		if ($this->user['fields'])
			return [
					'name' => implode(' ', [$this->user['fields']['First Name'], $this->user['fields']['Last Name']]),
					'email' => $this->email,
					'reg_date' => $this->user['fields']['registered_at'],
				];
		else
				return [
					'name' => 'New User',
					'email' => $this->email,
					'reg_date' => date('m/d/Y H:i:s'),
				];
	}

	
	function userIsNew()
	{
		return $this->user['is_new'];
	}
	
	function getGroupView()
	{
		global $AdminGrFormFields, $AdminFormStaticSelects;
		$rr = ['isNew' => $this->orgIsNew];
		
		foreach ($AdminGrFormFields as $fv=>$fa)
			switch ($fv)
			{
				case "geoscope":
					$node = [];
					foreach ($AdminFormStaticSelects[$fv] as $i=>$v)
						$node[] = ['v' => $v, 'text' => $v, 'selected' => array_search($v, (array)$this->org['fields'][$fa]) !== false];
					$rr[$fv] = $node;
					break;
				case "neighborhoods":
					$node = [];
					$captField = 'Neighborhood Name';
					foreach ((array)$this->neighborhoods as $id=>$v)
						$node[] = ['v' => $v['id'], 'text' => $v['fields'][$captField], 'selected' => array_search($v['id'], (array)$this->org['fields'][$fa]) !== false];
					$rr[$fv] = $node;
					break;
				case "coverimg":
					$rr[$fv] = $this->buildAttachmentsResponce($fv);
					break;
				case 'user_id':
					$rr[$fv] = $this->user['id'];
					break;
				case 'id':
					$rr['id'] = $this->org['id'];
					break;
				default: 
					$rr[$fv] = $this->org['fields'][$fa];
			}
		return $rr;
	}

	
	function getPostsView()
	{
		global $PostsFields;
		$posts = AirtableAdmin::getPosts($this->org['fields']['Group Name']);
		usort($posts['records'], function ($a, $b) {
			$da = strtotime($a['fields']['Status Last Modified'] ?? $a['fields']['Created Time']);
			$db = strtotime($b['fields']['Status Last Modified'] ?? $b['fields']['Created Time']);
			return $db <=> $da;
		});
		$this->posts = $posts;
		
		$rr = [];
		foreach ($this->posts['records'] as $post)
		{
			$r = [];
			foreach ($PostsFields as $fv=>$fa)
				switch ($fv)
				{
					case "id":
						$r[$fv] = $post['id'];
						break;
					case "start":
					case "end":
					case "created_at":
					case "modified_at":
						$t = str_replace('Z', '', $post['fields'][$fa]);
						$r[$fv] = $post['fields'][$fa] ? date('D, M jS H:i:s', strtotime($t)) : '';
						break;
					default: 
						$r[$fv] = $post['fields'][$fa];
				}
			$rr[] = $r;
		}
		return $rr;
	}

	
	function save2airtable()
	{
		if ($this->checkIfReload())
			return false;
		$this->post = $_POST;
		$ds = $this->buildDataset4Save();
		$this->airtableResponse = AirtableAdmin::updateOrg($this->org['id'], $ds);
		return $this->airtableResponse['records'] <> null;
	}


	function saveVrequest2airtable()
	{
		if ($this->checkIfReload())
			return false;
		$this->post = $_POST;
		$ds = $this->buildVrequest4Save();
		$this->airtableResponse = AirtableAdmin::newVrequest($ds);
		return $this->airtableResponse['records'] <> null;
	}
	
	
	function checkIfReload()
	{
		function match($a, $b) 
		{
			if (is_array($a))
			{	
				foreach ($a as $k=>$v)
					if (!match($v, ((array)$b)[$k]))
						return false;
				return true;
			} else
				return $a == $b;
		}
		return match($this->post, $_POST);
	}

	
	function buildDataset4Save()
	{
		global $AdminGrFormFields;
		$rr = [];
		foreach ($AdminGrFormFields as $fv=>$fa)
			if ($_POST[$fv])
				$rr[$fa] = $_POST[$fv];
		foreach (['Cover Image'] as $fa)
			$rr[$fa] = $this->org['fields'][$fa];
		return $rr;
	}

	
	function buildVrequest4Save()
	{
		global $PostsFields;
		$rr = [];
		foreach ($PostsFields as $fv=>$fa)
		{
			if ($_POST[$fv])
				$rr[$fa] = $_POST[$fv];
			if ($fv == 'user')
				$rr[$fa] = [$this->user['id']];
		}	
		return $rr;
	}

	
	function buildAttachmentsResponce($fv)		// $fv = field view named, $fa = field airtable named
	{
		$fa = $this->orgFieldV2A($fv);
		$rr = ['initialPreview' => [], 'initialPreviewConfig' => [], 'append' => false];
		foreach ((array)$this->org['fields'][$fa] as $k=>$v)
		{
			$fn = $this->buildAttachmentFIleName($fv, $v['filename']);
			$url = $this->buildAttachmentUrl($fv, $v['filename']);
				$rr['initialPreview'][] = $v['filename'];	//$url;
			$rr['initialPreviewConfig'][] = [
				'caption' => $v['filename'],
				'size' => file_exists($fn) ? filesize($fn) : 100000,
				'width' => '120px',
				'downloadUrl' => $url,
				'key' => implode('|', [$this->org['id'], $fv, $v['filename']]),
			];
		}
		return $rr;
	}

	function attachmentUpload($fv, $files)
	{
		$fa = $this->orgFieldV2A($fv);
		$filesArr = (array)$_SESSION['org']['fields'][$fa];
		foreach ((array)$files['tmp_name'] as $i=>$tmpName)
		{
			$filename = $files['name'][$i];
			move_uploaded_file($tmpName, $this->buildAttachmentFIleName($fv, $filename));
			foreach ($filesArr as $k=>$v)
				if ($v['filename'] == $filename)
					unset($filesArr[$k]);
			$filesArr[] = [
				'url' => $this->buildAttachmentUrl($fv, $filename),
				'filename' => $filename,
			];
		}
		$_SESSION['org']['fields'][$fa] = $filesArr;
		return $this->buildAttachmentsResponce($fv);
	}

	function attachmentDelete($key)
	{
		list($id, $fv, $filename) = explode('|', $key);
		$fa = $this->orgFieldV2A($fv);
		foreach ($_SESSION['org']['fields'][$fa] as $k=>$v)
			if ($v['filename'] == $filename)
			{
				unlink($this->buildAttachmentFIleName($fv, $filename));
				unset($_SESSION['org']['fields'][$fa][$k]);
			}
	}
	
	function buildAttachmentUrl($field, $filename)
	{
		return implode('/', [HOST, ADMIN_FILES_DIR, $this->org['id'], $field, $filename]);
	}
	
	function buildAttachmentFIleName($field, $filename)
	{
		$fullpath = implode('/', [ROOTDIR, 'html', ADMIN_FILES_DIR, $this->org['id'], $field, $filename]);
		$dir = dirname($fullpath);
		if (!is_dir($dir))
			mkdir($dir, 0777, true);
		return $fullpath;
	}
	
///////////////////////////////////////////////////////////////////////////////////

	function orgFieldV2A($f)
	{
		global $AdminGrFormFields;
		return $AdminGrFormFields[$f];
	}

	function userFieldV2A($f)
	{
		global $AdminUserFormFields;
		return $AdminUserFormFields[$f];
	}

	function __get($name)
	{
		$_SESSION['last_use'] = time();
		return $_SESSION[$name];
	}

	function __set($name, $value)
	{
		$_SESSION['last_use'] = time();
		$_SESSION[$name] = $value;
	}

}