<?php
/**
 * ownCloud - user_cas
 *
 * Base de départ : Développement de Sixto Martin <sixto.martin.garcia@gmail.com> 2012
 * 
 * @author Pascal AVALLE <pascal.avalle@univ-amu.fr>
 * @copyright Aix Marseille Université - 2014
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 * --------------------------------------------------
 * Mdofiation apportées par Pascal AVALLE - DOSI Université d'Aix-Marseille
 * Ajout de la laison avec Grouper (https://www.esup-portail.org/display/PROJESUPGRP/GT+ESUP-Grouper)
 * Ajout de la prise en charge de Nginx
 */


global $tabLdapUser;
$baseAuthentification="CAS";
         
if (OCP\App::isEnabled('user_cas') && isset($_SERVER["REQUEST_URI"]) && strstr($_SERVER['REQUEST_URI'],'remote.php')  ) {
    $baseAuthentification="local";
}

if (OCP\App::isEnabled('user_cas') && isset($_SERVER["REQUEST_URI"]) && strstr($_SERVER['REQUEST_URI'],'cron.php')) {
    $baseAuthentification="local";
}

//if (OCP\App::isEnabled('user_cas') && !strstr($_SERVER['REQUEST_URI'],'remote.php') && !strstr($_SERVER['REQUEST_URI'],'cron.php')) {
if (OCP\App::isEnabled('user_cas') && $baseAuthentification == "CAS") {

    $CAS_DIR=OCP\Config::getSystemValue('cas_dir', 'error');

    require_once 'user_cas/user_cas.php';
    include_once ($CAS_DIR.'/CAS.php');
    include_once ('user_cas/lib/ldap.php');

    OCP\App::registerAdmin('user_cas', 'settings');

    if( isset($_GET['app']) ) OC_Log::write('user_cas', 'APP GET ='.$_GET['app'], OC_Log::DEBUG);       
                 
    if ( isset($_SERVER["HTTP_USER_AGENT"]) && !strstr($_SERVER['HTTP_USER_AGENT'],'csyncoC') && !strstr($_SERVER['HTTP_USER_AGENT'],'mirall') ) {
         OC_Log::write('user_cas:app', 'APP='.$_SERVER['HTTP_USER_AGENT'], OC_Log::DEBUG);
        // register user backend 
        OC_User::useBackend( 'CAS' );
        OC::$CLASSPATH['OC_USER_CAS_Hooks'] = 'user_cas/lib/hooks.php';
        OCP\Util::connectHook('OC_User', 'post_login', 'OC_USER_CAS_Hooks', 'post_login');
        OCP\Util::connectHook('OC_User', 'logout', 'OC_USER_CAS_Hooks', 'logout');
    }


    if( isset($_GET['app']) && $_GET['app'] == 'user_cas' ) {

        OC_USER_CAS :: InitCAS();
        phpCAS::setNoCasServerValidation(); 

        if (!OC_User::login('', '')) {
                $error = true;
                OC_Log::write('cas','Error trying to authenticate the user', OC_Log::DEBUG);
        }
        OC::$REQUESTEDAPP = '';
        OC_Util::redirectToDefaultPage();
    }

    if (!OCP\User::isLoggedIn()) {

            // Load js code in order to render the CAS link and to hide parts of the normal login form
            OCP\Util::addScript('user_cas', 'utils');
    }


}
