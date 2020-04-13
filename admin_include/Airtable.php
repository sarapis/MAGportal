<?php 
class AirtableAdmin
{
	static function getOrg($id, $usr_id)
	{
		$hh = ["Authorization: Bearer " . ADMIN_AIRTABLE_KEY];
		$url = sprintf(
						'https://api.airtable.com/v0/%s/%s/%s', 
						rawurlencode(ADMIN_AIRTABLE_DOC),
						rawurlencode(ADMIN_AIRTABLE_GR),
						$id
					);
		$resp = Curl::exec($url, [CURLOPT_HTTPHEADER => $hh]);
		$rr = json_decode($resp, true);
		return $rr['id'] ? $rr : self::newOrg(['user' => [$usr_id]])['records'][0];
	}

	
	static function findUserByEmail($email)
	{
		$email = addslashes($email);
		$hh = ["Authorization: Bearer " . ADMIN_AIRTABLE_KEY];
		$url = sprintf(
						'https://api.airtable.com/v0/%s/%s?filterByFormula=%s', 
						rawurlencode(ADMIN_AIRTABLE_DOC),
						rawurlencode(ADMIN_AIRTABLE_USR),
						rawurlencode("AND({Email}='{$email}')")
					);
		$resp = Curl::exec($url, [CURLOPT_HTTPHEADER => $hh]);
		$rr = json_decode($resp, true);
		return $rr['records'] ? $rr : self::newUser(['Email' => $email]);
	}


	static function newUser($fields=['Name' => ''])
	{
		$hh = ['Authorization: Bearer ' . ADMIN_AIRTABLE_KEY, 'Content-Type: application/json'];
		$url = sprintf(
						'https://api.airtable.com/v0/%s/%s', 
						rawurlencode(ADMIN_AIRTABLE_DOC),
						rawurlencode(ADMIN_AIRTABLE_USR)
					);
		$resp = Curl::exec($url, [CURLOPT_HTTPHEADER => $hh], ['records' => [['fields' => $fields]]], 'json');
		//var_dump($resp);
		$rr = json_decode($resp, true);
		if ($rr['records'])
			$rr['records'][0]['is_new'] = true;
		return $rr;
	}

	
	static function newOrg($fields=['Name' => ''])
	{
		$hh = ['Authorization: Bearer ' . ADMIN_AIRTABLE_KEY, 'Content-Type: application/json'];
		$url = sprintf(
						'https://api.airtable.com/v0/%s/%s', 
						rawurlencode(ADMIN_AIRTABLE_DOC),
						rawurlencode(ADMIN_AIRTABLE_GR)
					);
		$resp = Curl::exec($url, [CURLOPT_HTTPHEADER => $hh], ['records' => [['fields' => $fields]]], 'json');
		//return json_decode($resp, true)['records'][0]['id'];
		$rr = json_decode($resp, true);
		if ($rr['records'])
			$rr['records'][0]['is_new'] = true;
		return $rr;
	}

	
	static function updateOrg($id, $data)
	{
		$hh = ['Authorization: Bearer ' . ADMIN_AIRTABLE_KEY, 'Content-Type: application/json'];
		$url = sprintf(
						'https://api.airtable.com/v0/%s/%s',
						rawurlencode(ADMIN_AIRTABLE_DOC),
						rawurlencode(ADMIN_AIRTABLE_GR)
					);
		$dd = ['records' => [['id' => $id, 'fields' => $data]]];
		//print_r(json_encode($dd));
		$resp = Curl::exec($url, [CURLOPT_HTTPHEADER => $hh], $dd, 'PATCH');
		//return json_decode($resp, true)['records'][0]['id'];
		return json_decode($resp, true);
	}

	
	static function updateUser($id, $data)
	{
		$hh = ['Authorization: Bearer ' . ADMIN_AIRTABLE_KEY, 'Content-Type: application/json'];
		$url = sprintf(
						'https://api.airtable.com/v0/%s/%s',
						rawurlencode(ADMIN_AIRTABLE_DOC),
						rawurlencode(ADMIN_AIRTABLE_USR)
					);
		$dd = ['records' => [['id' => $id, 'fields' => $data]]];
		//print_r($dd);
		$resp = Curl::exec($url, [CURLOPT_HTTPHEADER => $hh], $dd, 'PATCH');
		//return json_decode($resp, true)['records'][0]['id'];
		return json_decode($resp, true);
	}


	static function newVrequest($fields=['Name' => ''])
	{
		$hh = ['Authorization: Bearer ' . ADMIN_AIRTABLE_KEY, 'Content-Type: application/json'];
		$url = sprintf(
						'https://api.airtable.com/v0/%s/%s', 
						rawurlencode(ADMIN_AIRTABLE_DOC),
						rawurlencode(ADMIN_AIRTABLE_POSTS)
					);
		$resp = Curl::exec($url, [CURLOPT_HTTPHEADER => $hh], ['records' => [['fields' => $fields]]], 'json');
		//return json_decode($resp, true)['records'][0]['id'];
		$rr = json_decode($resp, true);
		if ($rr['records'])
			$rr['records'][0]['is_new'] = true;
		return $rr;
	}

	
	static function getPosts($grName)
	{
		$grName = addslashes($grName);
		$hh = ['Authorization: Bearer ' . ADMIN_AIRTABLE_KEY, 'Content-Type: application/json'];
		$url = sprintf(
						'https://api.airtable.com/v0/%s/%s?filterByFormula=%s', 
						rawurlencode(ADMIN_AIRTABLE_DOC),
						rawurlencode(ADMIN_AIRTABLE_POSTS),
						rawurlencode("FIND('{$grName}', {Group})")
					);
		//print_r($dd);
		$resp = Curl::exec($url, [CURLOPT_HTTPHEADER => $hh]);
		//return json_decode($resp, true)['records'][0]['id'];
		return json_decode($resp, true);
	}


	static function deleteOrg($id)
	{
		$hh = ['Authorization: Bearer ' . ADMIN_AIRTABLE_KEY, 'Content-Type: application/json'];
		$url = sprintf(
						'https://api.airtable.com/v0/%s/%s/%s',
						rawurlencode(ADMIN_AIRTABLE_DOC),
						rawurlencode(ADMIN_AIRTABLE_GR),
						$id
					);
		$resp = Curl::exec($url, [CURLOPT_HTTPHEADER => $hh, CURLOPT_CUSTOMREQUEST => 'DELETE']);
		return json_decode($resp, true);
	}

	
	static function readTable($table)
	{
		$res = [];
		$offs = null;
		while (True)
		{
			$resp = self::readTablePage($table, $offs);
			foreach ((array)$resp['records'] as $r)
				$res[$r['id']] = $r;
			$offs = $resp['offset'];
			if (!$offs)
				return $res;
		}
	}
	
	static function readTablePage($table, $offs=null)
	{
		$hh = ["Authorization: Bearer " . ADMIN_AIRTABLE_KEY];
		$url = sprintf(
						'https://api.airtable.com/v0/%s/%s?%s', 
						rawurlencode(ADMIN_AIRTABLE_DOC),
						rawurlencode($table),
						($offs ? "offset={$offs}" : '')
					);
		$resp = Curl::exec($url, [CURLOPT_HTTPHEADER => $hh]);
		return json_decode($resp, true);
	}

}