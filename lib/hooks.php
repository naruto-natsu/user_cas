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
 */

/**
 * This class contains all hooks.
 */

class OC_USER_CAS_Hooks {
          
	static public function post_login($parameters) {
		$uid = $parameters['uid'];
                $wuid = $uid;
		$casBackend = new OC_USER_CAS();
                $userDB = new OC_User_Database();
                
                /*
                 * Récupération des données du fichier config général /config/config.php
                 */
                $serveur_Search=OCP\Config::getSystemValue('serveur_Search', 'error');
                $port=OCP\Config::getSystemValue('port', 'error');
                $racineAMU=OCP\Config::getSystemValue('racineAMU', 'error');
                $racineAMUGRP=OCP\Config::getSystemValue('racineAMUGRP', 'error');
                $AMU_nuage_dn=OCP\Config::getSystemValue('AMU_nuage_dn', 'error');
                $AMU_nuage_pw=OCP\Config::getSystemValue('AMU_nuage_pw', 'error');
                $PQuota=OCP\Config::getSystemValue('PQuota', 'error');
                $EQuota=OCP\Config::getSystemValue('EQuota', 'error');

                
                
                $LDAP=new LDAP_Infos($serveur_Search,$AMU_nuage_dn,$AMU_nuage_pw,$racineAMUGRP,$racineAMUGRP);
                $restrictGrp=array("cn","member");                

                /*
                 * Récupération tableau Groupes
                 * Si le tableau 'groupMapping' est vide pas de contrôle sur les groupes 
                 */
                $AccesCloud=0;         
                OC_Log::write('user_cas',"Authentification (Mapping groups=".$casBackend->groupMapping.")"  , OC_Log::DEBUG);
                
                if ($casBackend->groupMapping) {                    
                    $wTabGrp = str_replace( array( '<br>', '<br />', "\n", "\r" ), array( '@', '', '@', '' ), $casBackend->groupMapping );
                    $tabGRP = explode("@",$wTabGrp);
                    $i=0;
                    $mesGroupes=array();
                    foreach ($tabGRP as $key => $value) 
                    {
                        $ListeMembre = $LDAP->getMembersOfGroup($value,$restrictGrp);
                        if (in_array($uid,$ListeMembre)) $AccesCloudAMU=1;
                    }
                } else {
                    $AccesCloud=1;
                }
                /*
                 * Si pas d'acces, alors déconnexion
                 */
                if ($AccesCloud==0) {
			/*
			 * On vérifie si le compte utilisé est un compte local
			 */
			if (!$userDB->userExists($uid) ) {
	                        OC_Log::write('user_cas',"Aucun droit d'accès pour l'utilisateur ".$uid  , OC_Log::ERROR);
        	                \OC_User::logout();
			} else {
	                        OC_Log::write('user_cas',"Authentification locale pour l'utilisateur ".$uid  , OC_Log::DEBUG);
        			OC::$REQUESTEDAPP = '';
			        OC_Util::redirectToDefaultPage();
				exit(0);
			}
                }

                /**
                 * Récupère les groupes liés à l'utilisateur avec la racine définie dans le formulaire 'cas_group_root'
                 * Si 'cas_group_root' n'est pas renseingé => pas de récupération de groupes
                 */
                $mesGroupes=array();
                OC_Log::write('user_cas',"Authentification (Racine Groupes LDAP=".$casBackend->groupRoot.")"  , OC_Log::DEBUG);
                if ($casBackend->groupRoot) {
                    $i=0;
                    $ListeGRP = $LDAP->getMemberOf($uid);

                    $a=sizeof($ListeGRP);
                    OC_Log::write('user_cas',"Taille=".$a." UID=".$uid  , OC_Log::ERROR);
                    OC_Log::write('user_cas',"Racine Groupe=".$casBackend->groupRoot  , OC_Log::ERROR);

                    foreach ($ListeGRP as $key => $value) 
                    {  
                        if (strstr($value,$casBackend->groupRoot))
                        {
                            $mesGroupes[$i]= strtoupper(str_replace(':','_',substr($value,8)));
                            OC_Log::write('user_cas',"Groupe[$i]=".$mesGroupes[$i]  , OC_Log::ERROR);
                            $i++;
                        }

                    }
                }
                
		if (phpCAS::checkAuthentication()) {
			//$attributes = phpCAS::getAttributes();
			$cas_uid = phpCAS::getUser();                   
                 
			if ($cas_uid == $uid) {
                            
                            /*
                             * Récupération des information utilisateur (LDAP)
                             */
                             $tabLdapUser = $LDAP->getUserInfo($uid);
                                                                                      
                             if ($tabLdapUser) $DisplayName = $tabLdapUser['Prenom'].' '.$tabLdapUser['Nom'];
                             
                            if (!$userDB->userExists($uid) ) {
                                    if (preg_match( '/[^a-zA-Z0-9 _\.@\-]/', $uid)) {
                                            OC_Log::write('cas','Utilisateur  invalide "'.$uid.'", caracteres autorises "a-zA-Z0-9" and "_.@-" ',OC_Log::DEBUG);
                                            return false;                                                
                                    }
                                    else {
                                        /*
                                         * Dans le cas d'une création
                                         */
                                        $random_password = \OC_Util::generateRandomBytes(20);  
                                        $userDB->createUser($uid, $random_password);
                                        $userDB->setDisplayName($uid, $DisplayName);
                                        /*
                                         * Mise à jour du quota
                                         */
                                        if ($tabLdapUser['eduPersonPrimaryAffiliation']=='student')  update_quota($uid, $EQuota); else update_quota($uid, $PQuota);                                     

                                    }
                            }
                             
                            /*
                             * Mise à jour des groupes associés
                             */
                            if (sizeof($mesGroupes)>0) {
                                $cas_groups = $mesGroupes;
                                update_groups($uid, $cas_groups, $casBackend->protectedGroups, true);
                            }
                            /*
                             * Mise à jour du mail
                             */
                            update_mail($uid, $tabLdapUser['Mail']);
                            /*
                             * Mise à jour du display name
                             */
                            $userDB->setDisplayName($uid, $DisplayName);
                            return true;                                 
			}
		}
		return false;
	}


	static public function logout($parameters) {
            if (phpCAS::isAuthenticated()) {
                \OCP\Util::writeLog('user_cas',"Deconexion", \OCP\Util::DEBUG);
                //phpCAS::logoutWithUrl('www.univ-amu.fr');
                phpCAS::logout();
            }	
            return true;
	}      
}

function update_mail($uid, $email) {
	if ($email != OC_Preferences::getValue($uid, 'settings', 'email', '')) {
		OC_Preferences::setValue($uid, 'settings', 'email', $email);
		OC_Log::write('cas','Set email "'.$email.'" for the user: '.$uid, OC_Log::DEBUG);
	}
}

function update_quota($uid, $quota) {
	if ($quota != OC_Preferences::getValue($uid, 'files', 'quota', '')) {
		OC_Preferences::setValue($uid, 'files', 'quota', $quota);
		OC_Log::write('cas','Set quota "'.$quota.'" for the user: '.$uid, OC_Log::DEBUG);
	}
}
function update_groups($uid, $groups, $protected_groups=array(), $just_created=false) {

	if(!$just_created) {
		$old_groups = OC_Group::getUserGroups($uid);
		foreach($old_groups as $group) {
			if(!in_array($group, $protected_groups) && !in_array($group, $groups)) {
				OC_Group::removeFromGroup($uid,$group);
				OC_Log::write('cas','Removed "'.$uid.'" from the group "'.$group.'"', OC_Log::DEBUG);
			}
		}
	}

	foreach($groups as $group) {
		if (preg_match( '/[^a-zA-Z0-9 _\.@\-]/', $group)) {
			OC_Log::write('cas','Invalid group "'.$group.'", allowed chars "a-zA-Z0-9" and "_.@-" ',OC_Log::DEBUG);
		}
		else {
			if (!OC_Group::inGroup($uid, $group)) {
				if (!OC_Group::groupExists($group)) {
					OC_Group::createGroup($group);
					OC_Log::write('cas','New group created: '.$group, OC_Log::DEBUG);
				}
				OC_Group::addToGroup($uid, $group);
				OC_Log::write('cas','Added "'.$uid.'" to the group "'.$group.'"', OC_Log::DEBUG);
			}
		}
	}
}
