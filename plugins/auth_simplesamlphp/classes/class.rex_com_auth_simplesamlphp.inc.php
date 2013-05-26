<?php
class rex_com_auth_simplesamlphp
{

  public function __construct() {
    global $REX;
    $this->authSource = $REX['ADDON']['community']['plugin_auth_simplesamlphp']['authSource'];
    $this->simplesamlphpPath = $REX['ADDON']['community']['plugin_auth_simplesamlphp']['simplesamlphpPath'];
    $this->hash_func = $REX['ADDON']['community']['plugin_auth']['passwd_algorithmus'];            
    $this->isHashed = $REX['ADDON']['community']['plugin_auth']['passwd_hashed'];
    $this->article_login_failed = $REX['ADDON']['community']['plugin_auth']['article_login_failed'];
    $this->article_login_ok = $REX['ADDON']['community']['plugin_auth']['article_login_ok'];
    $this->logoutUrl = '/index.php?article_id='.$REX['ADDON']['community']['plugin_auth']['article_logout'].'&rex_com_auth_logout=1';
    $this->redirect = $REX['ADDON']['community']['plugin_auth_simplesamlphp']['redirect'];
    $this->defaultgroups = $REX['ADDON']['community']['plugin_auth_simplesamlphp']['defaultgroups'];
    $this->synctranslation = $REX['ADDON']['community']['plugin_auth_simplesamlphp']['synctranslation'];
  }
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
  public function authSaml() 
  {
    // load simplesamlph and require authentification
    require_once($this->simplesamlphpPath);
    $as = new SimpleSAML_Auth_Simple($this->authSource);
    $as->requireAuth(array(
      'RedirectTo' => $REX['SERVER'].rex_getUrl($this->article_login_ok),
      'KeepPost' => false
    )
  );
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

        // Don't create User if eduPearsontargetID is empty -> results in empty username!
        if($eduPersonTargetedID=='') {
          rex_redirect($this->article_login_failed);
        }


        // -------------------------- Sync user to database
        $iu = rex_sql::factory();
        $iu->setTable("rex_com_user");
        $iu->setValue("status",1); // ----- activate user profile
        $iu->setValue("authsource",'simplesamlphp');
        $iu->setValue("samlidp",$idp); // ------ save IdP
        $iu->setValue("login",$eduPersonTargetedID);

        // hash passwort if password are encrypted
        $password = rex_com_auth_simplesamlphp::generatePassword('16');
        if($this->isHashed) 
        {
          $password = hash($this->hash_func, $password);
        }
        $iu->setValue("password", $password);

        // Adding defaultgroups
        if(isset($this->defaultgroups)) 
        {
          $iu->setValue("rex_com_group",implode(',' , $this->defaultgroups));
        }

        // Translate datafields
        if(!empty($this->synctranslation))
        {
          foreach($this->synctranslation as $key => $value)
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

      if(rex_com_auth::getUser() && $this->redirect)
      {
        rex_redirect($this->article_login_ok);
      }

    } else 
      { // ------------------------- Login Error
        if(!(rex_com_auth::getUser()) && $this->redirect) 
        {
          rex_redirect($this->article_login_failed);
        }
      }
  }

  public function logoutSaml()
  {
    // load simplesamlph and require authentification
    require_once($this->simplesamlphpPath);
    $as = new SimpleSAML_Auth_Simple($this->authSource);
    $as->logout($this->logoutUrl);
  }
}
