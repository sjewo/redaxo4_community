<?php

class rex_com_auth_simplesamlphp
{
	//
	// Generates a simple random password
	//
	public function generatePassword($lenght = 10)
	{
		## Soruce: http://www.tsql.de/php/zufaelliges-passwort-erzeugen-md5
		## Not realy safe, but very smart ;)
		$string = md5((string)mt_rand().$_SERVER['REMOTE_ADDR'].time());
		$start = rand(0,strlen($string)-$lenght);
		return substr($string, $start, $lenght);
	}
}
