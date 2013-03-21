<?php
/**
 * Plugin simpleSAMLphp-Auth
 * @author Sebastian.Jeworutzki[at]rub[dot]de Sebastian Jeworutzki
 * 
 * based on facebook-auth by m.lorch[at]it-kult[dot]de Markus Lorch
 * <a href="http://www.it-kult.de">www.it-kult.de</a>
 */

//
// Plugin Settings
//

$mypage = "auth_simplesamlphp";
$REX['ADDON']['version'][$mypage] = '0.0.1';

// --- DYN
$REX['ADDON']['community']['plugin_auth_simplesamlphp']['simplesamlphpPath'] = "";
$REX['ADDON']['community']['plugin_auth_simplesamlphp']['authSource'] = "";
// --- /DYN
$REX['ADDON']['community']['plugin_auth_simplesamlphp']['redirect'] = true;

//
// Synctranslation
//
## login, password status, authsource, are default fields and already set - don't add!
$REX['ADDON']['community']['plugin_auth_simplesamlphp']['synctranslation'] = array(
##	'rex_com_user field' => 'simplesamlphp field'
	);

//
// Initialisierung
//
include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth_simplesamlphp/classes/class.rex_com_auth_simplesamlphp.inc.php";

## Include Lang
if (isset($I18N) && is_object($I18N))
{
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/community/plugins/auth_simplesamlphp/lang');
	
	## Adding language key for compat reasons	
	if(!$I18N->hasMsg('com_auth_authsource'))
		$I18N->addMsg('com_auth_authsource','Auth-Plugin');
}

# Settings..
$REX['ADDON']['community']['plugin_auth_simplesamlphp']['simplesamlphp_conf'] = array(
	'simplesamlphpPath'=>$REX['ADDON']['community']['plugin_auth_simplesamlphp']['simplesamlphpPath'],
	'authsource'=>$REX['ADDON']['community']['plugin_auth_simplesamlphp']['authSource']
	);

if($REX["REDAXO"])
{
	## Adding to Backend Menu
	if($REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[simplesamlphp]")))
		$REX['ADDON']['community']['SUBPAGES'][] = array('plugin.auth_simplesamlphp','SimpleSAML');

}

?>
