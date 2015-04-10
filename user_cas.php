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

class OC_USER_CAS extends OC_User_Backend {

	// cached settings
	public $autocreate;
	public $updateUserData;
	public $protectedGroups;
	public $defaultGroup;
	public $mailMapping;
	public $groupMapping;
        public $groupRoot;
        protected static $_InitCAS = false;
        
	public function __construct() {
	
		$this->autocreate = OCP\Config::getAppValue('user_cas', 'cas_autocreate', true);
		$this->updateUserData = OCP\Config::getAppValue('user_cas', 'cas_update_user_data', true);
		$this->defaultGroup = OCP\Config::getAppValue('user_cas', 'cas_default_group', 'TEST');
		$this->protectedGroups = explode (',', str_replace(' ', '', OCP\Config::getAppValue('user_cas', 'cas_protected_groups', '')));
		$this->mailMapping = OCP\Config::getAppValue('user_cas', 'cas_email_mapping', '');
		$this->groupMapping = OCP\Config::getAppValue('user_cas', 'cas_group_mapping', '');
                $this->groupRoot = OCP\Config::getAppValue('user_cas', 'cas_group_root', '');
		$this->aliasName = OCP\Config::getAppValue('user_cas', 'cas_aliasName', '');
 
            	$casVersion = OCP\Config::getAppValue('user_cas', 'cas_server_version', '2.0');
	        $casHostname = OCP\Config::getAppValue('user_cas', 'cas_server_hostname', 'ident.domain.fr');
	        $casPort = OCP\Config::getAppValue('user_cas', 'cas_server_port', '443');
	        $casPath = OCP\Config::getAppValue('user_cas', 'cas_server_path', '/cas');                

                self :: InitCAS();

        }

        public static function InitCAS () {
            if(!self :: $_InitCAS) {
                $aliasName = OCP\Config::getAppValue('user_cas', 'cas_aliasName', '');
            	$casVersion = OCP\Config::getAppValue('user_cas', 'cas_server_version', '2.0');
	        $casHostname = OCP\Config::getAppValue('user_cas', 'cas_server_hostname', 'ident.domain.fr');
	        $casPort = OCP\Config::getAppValue('user_cas', 'cas_server_port', '443');
	        $casPath = OCP\Config::getAppValue('user_cas', 'cas_server_path', '/cas');
                
                phpCAS::client($casVersion,$casHostname,(int)$casPort,$casPath,false);
                if ( $aliasName ) phpCAS::setFixedServiceURL($aliasName);
                phpCAS::setNoCasServerValidation();
                        
                self :: $_InitCAS = true;
            }
            return self :: $_InitCAS;

        }
        
	public function checkPassword($uid, $password) {
            	
		if(!phpCAS::forceAuthentication()) {
			return false;
		}

		$uid = phpCAS::getUser();
		if ($uid === false) {
			OC_Log::write('user_cas','phpCAS return no user !', OC_Log::ERROR);
			return false;
		}
		return $uid;
	}

}
