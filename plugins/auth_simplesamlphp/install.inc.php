<?php
/**
 * Plugin simpleSAMLphp-Auth
 * @author Sebastian.Jeworutzki[at]rub[dot]de Sebastian Jeworutzki
 * 
 * based on simplesamlphp-auth by m.lorch[at]it-kult[dot]de Markus Lorch
 * <a href="http://www.it-kult.de">www.it-kult.de</a>
 */

$REX['ADDON']['install']['auth_simplesamlphp'] = 1;
$REX['ADDON']['installmsg']['auth_simplesamlphp'] = '';

if (isset($I18N) && is_object($I18N))
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/community/plugins/auth_simplesamlphp/lang'); 

## Checking dependencies
## http://www.redaxo.org/de/forum/post96341.html#p96341
## Not working with redaxo5 -> use in e.g. r5: OOAddon::isAvailable('simplesamlphp_sdk')
if($ADDONSsic['status']['community'])
{
	//
	// Install Database
	//
	$sql = new rex_sql();
	
	## Field: authsource
	$sql->setQuery("SHOW COLUMNS FROM rex_com_user WHERE Field='authsource'");
	$REX['ADDON']['installmsg']['auth_simplesamlphp'] = $sql->getError();
	
	if(!$sql->getRows() && $REX['ADDON']['installmsg']['auth_simplesamlphp'] == '')
	{
		$sql->setQuery("INSERT INTO rex_xform_field (id, table_name, prio, type_id, type_name, f1, f2, list_hidden, search) VALUES (NULL, 'rex_com_user', '100', 'value', 'text', 'authsource', 'translate:com_auth_authsource', '1', '0')");
		$REX['ADDON']['installmsg']['auth_simplesamlphp'] = $sql->getError();

		if($REX['ADDON']['installmsg']['auth_simplesamlphp'] == '')
		{
			$sql->setQuery("ALTER TABLE rex_com_user ADD authsource TEXT NOT NULL");
			$REX['ADDON']['installmsg']['auth_simplesamlphp'] = $sql->getError();
		}
	}

    ## Field: saml_idp
    $sql->setQuery("SHOW COLUMNS FROM rex_com_user WHERE Field='samlidp'");
    $REX['ADDON']['installmsg']['auth_simplesamlphp'] = $sql->getError();

    if(!$sql->getRows() && $REX['ADDON']['installmsg']['auth_simplesamlphp'] == '')
	{
		$sql->setQuery("INSERT INTO rex_xform_field (id, table_name, prio, type_id, type_name, f1, f2, list_hidden, search) VALUES (NULL, 'rex_com_user', '100', 'value', 'text', 'samlidp', 'translate:com_auth_simplesamlphp_samlidp', '1', '0')");
		$REX['ADDON']['installmsg']['auth_simplesamlphp'] = $sql->getError();

		if($REX['ADDON']['installmsg']['auth_simplesamlphp'] == '')
		{
			$sql->setQuery("ALTER TABLE rex_com_user ADD samlidp TEXT NOT NULL");
			$REX['ADDON']['installmsg']['auth_simplesamlphp'] = $sql->getError();
		}
	}

}
else
{
	$REX['ADDON']['installmsg']['auth_simplesamlphp'] = $I18N->msg('com_auth_simplesamlphp_error_missingaddons');
}


?>
