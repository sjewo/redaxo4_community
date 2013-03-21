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

    //
    // Authentificate user with simplesamlphp
    // creates local user
    // 
    public function authSaml($authSource = 'default-sp', $simplesamlphpPath ) 
    {
        // load simplesamlph and require authentification
        require_once($simplesamlphpPath);
        $as = new SimpleSAML_Auth_Simple($authSource);
        $as->requireAuth();
        $attributes = $as->getAttributes();

        // create user/login user
        if($as->isAuthenticated())
        {
            // -------------------------- Get User Array
            $eduPersonTargetedID = $attributes['eduPersonTargetedID'][0];
            $idp = $as->getAuthData('saml:sp:IdP');

            // -------------------------- Check if User Exists in Database by eduPersonTargetedID
            $sql = new rex_sql();
            $sql->setQuery('SELECT login FROM rex_com_user WHERE login = "'.$eduPersonTargetedID.'"');

            if($sql->getRows() == 0) { // --- not in DB
                // -------------------------- Sync user to database
                $iu = rex_sql::factory();
                $iu->setTable("rex_com_user");
                $iu->setValue("status",1); // ----- activate user profile
                $iu->setValue("authsource",'simplesamlphp');
                $iu->setValue("samlidp",$idp); // ------ save IdP
                $iu->setValue("login",$eduPersonTargetedID);
                
                // hash passwort if password are encrypted
                $hash_func = $REX['ADDON']['community']['plugin_auth']['passwd_algorithmus'];
                $password = rex_com_auth_simplesamlphp::generatePassword('16');
                if($REX['ADDON']['community']['plugin_auth']['passwd_hashed']) 
                {
                    $password = hash($hash_func, $password);
                }
                $iu->setValue("password", $password);

                // Adding defaultgroups
                if(isset($REX['ADDON']['community']['plugin_auth_simplesamlphp']['defaultgroups'])) 
                {
                    $iu->setValue("rex_com_group",implode(',' , $REX['ADDON']['community']['plugin_auth_simplesamlphp']['defaultgroups']));
                }

                // Translate datafields
                if(!empty($REX['ADDON']['community']['plugin_auth_simplesamlphp']['synctranslation']))
                {
                    foreach($REX['ADDON']['community']['plugin_auth_simplesamlphp']['synctranslation'] as $key => $value)
                    {
                        $iu->setValue($key,$attributes[$value]);
                    }
                }

                // ------------ DB insert
                $iu->insert();

                // 
                rex_com_user::triggerUserCreated($iu->getLastId()); // TODO: params as array()
        
            } else { // User exists 
                // do nothing
            }
        
            // -------- Do Login
            $params =  array("login" => $eduPersonTargetedID, "status" => 1);
            print_r($params);
            rex_com_auth::loginWithParams($params);

            if(rex_com_auth::getUser() && $REX['ADDON']['community']['plugin_auth_simplesamlphp']['redirect'])
            {
                rex_redirect($REX['ADDON']['community']['plugin_auth']['article_login_ok']);
            }

        } else 
        { // ------------------------- Login Error
            if(!(rex_com_auth::getUser()) && $REX['ADDON']['community']['plugin_auth_simplesamlphp']['redirect']) 
            {
                rex_redirect($REX['ADDON']['community']['plugin_auth']['article_login_failed'],'',array('rex_com_auth_info'=>'2'));
            }
        }
    }
}
