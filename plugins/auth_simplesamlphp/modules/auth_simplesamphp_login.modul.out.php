<?php
if($REX['REDAXO']) {

    if('REX_LINK_ID[1]'!='') {
        echo 'Weiterleitung nach erfolgreichem Login: '.'REX_LINK_ID[1]';
    }
    if('REX_LINK_ID[2]'!='') {
        echo 'Weiterleitung nach erfolglosen Login: '.'REX_LINK_ID[2]';
    }
    if('REX_LINK_ID[3]'!='') {
        echo 'Weiterleitung bei fehlenden Meta-Daten: '.'REX_LINK_ID[3]';
    }

    if('REX_VALUE[1]'!='') {
        echo 'Authentifizierungsquelle:'.'REX_VALUE[1]';
    }
} else {
    $login = new  rex_com_auth_simplesamlphp();
    if('REX_VALUE[1]'!='') {
        $login->setAuthSource('REX_VALUE[1]');
    }
    if('REX_LINK_ID[1]'!='') {
        $login->setArticleLoginOk('REX_LINK_ID[1]');
    }
    if('REX_LINK_ID[2]'!='') {
        $login->setArticleLoginFailed('REX_LINK_ID[2]');
    }
    if('REX_LINK_ID[3]'!='') {
        $login->setArticleLoginIdMissing('REX_LINK_ID[3]');
    }
    $login->authSaml();
}
?>
