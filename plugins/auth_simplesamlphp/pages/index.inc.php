<?php

$info = '';
$warning = '';

if(rex_request("func","string")=="update")
{
	$REX['ADDON']['community']['plugin_auth_simplesamlphp']['simplesamlphpPath'] = rex_request("simplesamlphpPath","string");
	$REX['ADDON']['community']['plugin_auth_simplesamlphp']['authSource'] = rex_request("authSource","string");
	$REX['ADDON']['community']['plugin_auth_simplesamlphp']['defaultgroups'] = rex_request("defaultgroups","array");
	
	$content = '
$REX[\'ADDON\'][\'community\'][\'plugin_auth_simplesamlphp\'][\'simplesamlphpPath\'] = "'.$REX['ADDON']['community']['plugin_auth_simplesamlphp']['simplesamlphpPath'].'";
$REX[\'ADDON\'][\'community\'][\'plugin_auth_simplesamlphp\'][\'authSource\'] = "'.$REX['ADDON']['community']['plugin_auth_simplesamlphp']['authSource'].'";
';
	
	for($i = 0; $i < count($REX['ADDON']['community']['plugin_auth_simplesamlphp']['defaultgroups']); $i++)
	{
		$content .= '$REX[\'ADDON\'][\'community\'][\'plugin_auth_simplesamlphp\'][\'defaultgroups\'][\''.$i.'\'] = '.$REX['ADDON']['community']['plugin_auth_simplesamlphp']['defaultgroups'][$i].';
';
	}
	
	if(rex_replace_dynamic_contents($REX['INCLUDE_PATH'].'/addons/community/plugins/auth_simplesamlphp/config.inc.php', $content) !== false)
		echo rex_info($I18N->msg('com_auth_simplesamlphp_settings_update'));
	else
		echo rex_warning($I18N->msg('com_auth_simplesamlphp_settings_failupdate'));
}

//
// Form Output
//

echo '
	<div class="rex-form" id="rex-form-system-setup">
  	<form action="index.php" method="post">
    	<input type="hidden" name="page" value="community" />
    	<input type="hidden" name="subpage" value="plugin.auth_simplesamlphp" />
    	<input type="hidden" name="func" value="update" />
		
			<div class="rex-area-col-2">
				<div class="rex-area-col-a">
	
					<h3 class="rex-hl2">'.$I18N->msg("description").'</h3>
	
					<div class="rex-area-content">
<p class="rex-tx1">'.$I18N->msg("com_auth_simplesamlphp_settings_description").'</p>
<h3 class="rex-hl3">'.$I18N->msg("com_auth_simplesamlphp_help_login_hl").'</h3>
<p class="rex-tx1">'.$I18N->msg("com_auth_simplesamlphp_help_login").'</p>	
					</div>
				</div>
			
				<div class="rex-area-col-b">
					
					<h3 class="rex-hl2">'.$I18N->msg("com_auth_simplesamlphp_settings").'</h3>
					
					<div class="rex-area-content">
					
						<fieldset class="rex-form-col-1">
							<legend>'.$I18N->msg("com_auth_simplesamlphp_settings_simplesamlphp").'</legend>
							
							<div class="rex-form-wrapper">
							
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-simplesamlphpPath">'.$I18N->msg("com_auth_simplesamlphp_settings_simplesamlphpPath").'</label>
										<input class="rex-form-text" type="input" id="rex-form-simplesamlphpPath" name="simplesamlphpPath" value="'.$REX['ADDON']['community']['plugin_auth_simplesamlphp']['simplesamlphpPath'].'" />
									</p>
								</div>
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-aauthSource">'.$I18N->msg("com_auth_simplesamlphp_settings_authSource").'</label>
										<input class="rex-form-text" type="input" id="rex-form-authSource" name="authSource" value="'.$REX['ADDON']['community']['plugin_auth_simplesamlphp']['authSource'].'" />
									</p>
								</div>
							
							</div>
						</fieldset><fieldset class="rex-form-col-1">';

//
// Generating Selectbox for User-Groups
//
if(OOPlugin::isAvailable('community','group'))
{
	$sql = new rex_sql();
	$sql->setQuery('SELECT id,name FROM rex_com_group');
	
	$groupselect = new rex_select();
	$groupselect->setName('defaultgroups[]');
	$groupselect->setSize(6);
	$groupselect->setMultiple(true);
	//$groupselect->addOption($I18N->msg('com_simplesamlphp_settings_none'),0);
	foreach($sql->getArray() as $group) {
		$groupselect->addOption($group['name'].' ['.$group['id'].']',$group['id']);
	}
	
	if(isset($REX['ADDON']['community']['plugin_auth_simplesamlphp']['defaultgroups']))
  	$groupselect->setSelected($REX['ADDON']['community']['plugin_auth_simplesamlphp']['defaultgroups']);


				echo '	<legend>'.$I18N->msg("com_auth_simplesamlphp_settings_newuser").'</legend>
							<div class="rex-form-wrapper">
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-defaultgroups">'.$I18N->msg("com_auth_simplesamlphp_settings_memberof").'</label>
										'.$groupselect->get().'
									</p>
								</div>
							</div>';
}

echo '					<div class="rex-form-wrapper">
							<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-submit">
										<input type="submit" class="rex-form-submit" name="sendit" value="'.$I18N->msg("specials_update").'"'. rex_accesskey($I18N->msg('specials_update'), $REX['ACKEY']['SAVE']) .' />
									</p>
								</div>
							</div>
						</fieldset>';

echo'					</div> <!-- Ende rex-area-content //-->					
				</div> <!-- Ende rex-area-col-b //-->
			</div> <!-- Ende rex-area-col-2 //-->
			
		</form>
	</div>
  ';


