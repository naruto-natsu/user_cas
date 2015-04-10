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

OC_Util::checkAdminUser();

$params = array('cas_server_version', 'cas_server_hostname', 'cas_server_port', 'cas_server_path', 'cas_group_mapping', 'cas_group_root', 'cas_aliasName');
OCP\Util::addscript('user_cas', 'settings');

if ($_POST) {

	foreach($params as $param) {
		if (isset($_POST[$param])) {
			OCP\Config::setAppValue('user_cas', $param, $_POST[$param]);
		}  
		elseif ('cas_autocreate' == $param) {
			OCP\Config::setAppValue('user_cas', $param, 0);
		}
		elseif ('cas_update_user_data' == $param) {
			OCP\Config::setAppValue('user_cas', $param, 0);
		}
	}
}

// fill template
$tmpl = new OCP\Template( 'user_cas', 'settings');
foreach ($params as $param) {
		$value = htmlentities(OCP\Config::getAppValue('user_cas', $param,''));
		$tmpl->assign($param, $value);
}

// settings with default values
$tmpl->assign( 'cas_server_version', OCP\Config::getAppValue('user_cas', 'cas_server_version', '2.0'));
$tmpl->assign( 'cas_server_hostname', OCP\Config::getAppValue('user_cas', 'cas_server_hostname', 'ident.domain.fr'));
$tmpl->assign( 'cas_server_port', OCP\Config::getAppValue('user_cas', 'cas_server_port', '443'));
$tmpl->assign( 'cas_server_path', OCP\Config::getAppValue('user_cas', 'cas_server_path', '/cas'));
$tmpl->assign( 'cas_group_mapping', OCP\Config::getAppValue('user_cas', 'cas_group_mapping', ''));
$tmpl->assign( 'cas_group_root', OCP\Config::getAppValue('user_cas', 'cas_group_root', ''));
$tmpl->assign( 'cas_aliasName', OCP\Config::getAppValue('user_cas', 'cas_aliasName', ''));

return $tmpl->fetchPage();
