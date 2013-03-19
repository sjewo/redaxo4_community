<?php

require_once('/usr/share/simplesamlphp/lib/_autoload.php');
$as = new SimpleSAML_Auth_Simple('default-sp');
$attributes = $as->getAttributes();

if($as->isAuthenticated()) {
    // -------------------------- Get User Array
    $eduPersonTargetedID = $attributes['eduPersonTargetedID'][0];
    $idp = $as->getAuthData('saml:sp:IdP');

    // -------------------------- Check if User Exists in Database by eduPersonTargetedID
    $sql = new rex_sql();
    $sql->setQuery('SELECT login FROM rex_com_user WHERE login = "'.$eduPersonTargetedID.'"');

    if($sql->getRows() == 0) { // not in DB

        // -------------------------- Sync user to database
        $iu = rex_sql::factory();
		$iu->setTable("rex_com_user");
        $iu->setValue("status",1); # activate user profile
        $iu->setValue("authsource",$idp);
        $iu->setValue("login",$eduPersonTargetedID);
        $iu->setValue("password",rex_com_auth_simplesamlphp::generatePassword('16'));

        ## Adding defaultgroups
        if(isset($REX['ADDON']['community']['plugin_auth_simplesamlphp']['defaultgroups'])) {
            $iu->setValue("rex_com_group",implode(',' , $REX['ADDON']['community']['plugin_auth_simplesamlphp']['defaultgroups']));
         }
         
         ## Translate datafields
         foreach($REX['ADDON']['community']['plugin_auth_simplesamlphp']['synctranslation'] as $key => $value)
         {
           $iu->setValue($key,$attributes[$value]);
         }
       
         $iu->insert();

         rex_com_user::triggerUserCreated($iu->getLastId()); // TODO: params as array()

    } else {

          // -------------------------- User exists -> only update
          // TODO: gu is missing.... 
          $iu->setWhere('id='.$gu->getValue("id"));
          $iu->update();
          
          rex_com_user::triggerUserUpdated($gu->getValue("id")); // TODO: params as array()
          
        }
        
    // Login
    $params =  array("login" => $eduPersonTargetedID, "status" => 1);
    rex_com_auth::loginWithParams($params);

}

if(rex_com_auth::getUser() && $REX['ADDON']['community']['plugin_auth_simplesamlphp']['redirect']) rex_redirect($REX['ADDON']['community']['plugin_simplesamlphp']['article_login_ok']);

?>
