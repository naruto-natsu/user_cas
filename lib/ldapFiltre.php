<?php
/**
 * Created by PhpStorm.
 * User: pavalle
 * Date: 01/03/2016
 * Time: 13:55
 */


class LDAP_filtre
{

    public function __construct()
    {
        OCP\Util::writeLog('cas', 'ldapFiltre Construct DB', OCP\Util::DEBUG);

        $dbname = OCP\Config::getSystemValue('dbname', 'error');
        $this->dbtableprefix = OCP\Config::getSystemValue('dbtableprefix', 'error');
        $dbhost = OCP\Config::getSystemValue('dbhost', 'error');
        $dbuser = OCP\Config::getSystemValue('dbuser', 'error');
        $dbpassword = OCP\Config::getSystemValue('dbpassword', 'error');

        OCP\Util::writeLog('cas', 'ldapFiltre Construct DB dbname='.$dbname, OCP\Util::DEBUG);


        $this->link = mysqli_connect ($dbhost, $dbuser, $dbpassword, $dbname);

        if (!$this->link) {
            OCP\Util::writeLog('cas', 'ldapFiltre Connexion DB ERROR ['. mysqli_connect_errno() .'] - ['.mysqli_connect_error().']', OCP\Util::DEBUG);
        }
    }

    /**
     *
     */
    public function findFiltre()
    {
        $configvalue=null;
        $query = "SELECT configvalue FROM ".$this->dbtableprefix."appconfig WHERE configkey = 'ldap_login_filter' AND appid = 'user_ldap'";

        OCP\Util::writeLog('cas', 'ldapFiltre  query ['. $query .']', OCP\Util::DEBUG);

        if ( $result = mysqli_query($this->link,$query) ) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $configvalue = $row["configvalue"];
        }
        mysqli_free_result($result);

        OCP\Util::writeLog('cas', 'ldapFiltre  ['. $configvalue .']', OCP\Util::DEBUG);
        return ($configvalue);
    }
}