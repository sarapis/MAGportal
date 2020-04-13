<?php

class SendGridMail
{
	static function authMail($to, $isNew, $token)
	{
		$host = HOST;
		//echo "{$host}/vendoraccount.php?token={$token}\n";
		$html = $isNew
			? 
<<<EOD
<h1>Welcome to the Mutual Aid Group Portal</h1>
<p>To register <a href="{$host}/admin/dashboard.php?token={$token}">click here</a></p>

EOD
			: 
<<<EOD
<h1>Welcome to the Mutual Aid Group Portal</h1>
<p>To log in <a href="{$host}/admin/dashboard.php?token={$token}">click here</a></p>

EOD
;
		return self::sendMail($to, 'Mutual Aid Group Portal Login', $html);
	}


	static function sendMail($to, $subj, $msg)
	{
		$email = new \SendGrid\Mail\Mail();
		$email->setFrom(ADMIN_AUTH_EMAIL, ADMIN_AUTH_SENDER);
		$email->setSubject($subj);
		$to = (array)$to;
		$email->addTo(array_shift($to), array_shift($to) ?? '');
		$email->addContent("text/plain", self::html2txt($msg));
		$email->addContent(
			"text/html", $msg
		);

		$sendgrid = new \SendGrid(SENDGRID_API_KEY);
		try {
			$response = $sendgrid->send($email);
			if ($response->statusCode() == 202)
				return true;
			print $response->statusCode() . "\n";
			print_r($response->headers());
			print $response->body() . "\n";
		} catch (Exception $e) {
			echo 'Caught exception: '. $e->getMessage() ."\n";
		}
	}

	
	static function html2txt($html, $monostr=false)
	{
		$html = html_entity_decode($html, ENT_QUOTES | ENT_XML1, 'UTF-8');
		$rpl = [
			'&nbsp;'									=> 	' ',
			'\s*(<!--.*?-->|<script[^>]*>.*?</script>)\s*'							=> 	'',
			">\s*[\r\n]+\s*<"							=>	'><',
			'\s*<h\d>\s*|(\s*</div>\s*|\s*</h\d>\s*|\s*<br[^>]{1,4}>\s*)+'	=>	"\n",
			'\s*<li>\s*'								=> 	'-',
			'\s*</li>\s*'								=> 	"\n",
			'<[^<>]+>'									=> 	'',
			"[\n\r]{3,}"								=>	"\n\n",
			"^\s+|\s+$"									=>	'',
		];
		if ($monostr)
			$rpl["\s*[\r\n]+[ \t]*"] = '\n';
		foreach ($rpl as $from=>$to)
			$html = mb_eregi_replace($from, $to, $html);
		return $html;
	}
}