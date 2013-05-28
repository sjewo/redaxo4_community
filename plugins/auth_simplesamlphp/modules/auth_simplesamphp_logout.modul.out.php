<?php
if(!$REX["REDAXO"])
{
$logout = new  rex_com_auth_simplesamlphp();
$logout->logoutSaml();
}
?>
