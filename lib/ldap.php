<?php
/** Class d'interrogation d'un serveur LDAP
 */

class LDAP_Infos
{
	
    public function __construct() {
        /*
         * Récupération des données du fichier config général /config/config.php
         */                                  
        $serveur_Search=OCP\Config::getSystemValue('serveur_Search', 'error');
        $port=OCP\Config::getSystemValue('port', 'error');
        $this->racineAMU=OCP\Config::getSystemValue('racineAMU', 'error');
        $this->racineAMUGRP=OCP\Config::getSystemValue('racineAMUGRP', 'error');
        $AMU_nuage_dn=OCP\Config::getSystemValue('AMU_nuage_dn', 'error');
        $AMU_nuage_pw=OCP\Config::getSystemValue('AMU_nuage_pw', 'error');

        $this->ds=ldap_connect($serveur_Search,$port) or die( "Impossible de se connecter au serveur LDAP $serveur_Search" );

        ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ds, LDAP_OPT_NETWORK_TIMEOUT,5 );

        $ldapbind = ldap_bind($this->ds, $AMU_nuage_dn, $AMU_nuage_pw);   
        if ( $ldapbind !='1') return false;   
        
    }
   
    
    /**
     * Renvoi un tableau des GROUPES (grouper) auxquels appartient la personne dont l'uid est spécifié
     * @param type $uid l'uid/login de la personne 
     * @return array() $arGroups Liste des groupes de l'utilisateur 
     */
    function getMemberOf($uid)
    {

        $filtre="(&(objectclass=*)(member=uid=$uid,$this->racineAMU))";
        $restriction=array("cn");
        $sr=ldap_search($this->ds, "dc=univ-amu,dc=fr", $filtre,$restriction);   
        
        $Infos = ldap_get_entries($this->ds, $sr);
        if ($Infos["count"] > 0) {
            $arGroups=array();
            foreach($Infos as $oneGroup) {
                $arGroups[]=$oneGroup['cn'][0];
            }
        }       
      return $arGroups;
    }

    /**
     * Renvoi un tableau des membres d'un groupe (GROUPER)
     * @param type $groupName
     * @param type $restriction
     * @param type $debug
     * @return type 
     */
    function getMembersOfGroup($groupName,$restriction=array("cn","member"))
    {
      $filtre="(&(objectclass=*)(cn=".$groupName."))"; 
      $sr=ldap_search($this->ds, $this->racineAMUGRP, $filtre,$restriction);
 
      $Infos = ldap_get_entries($this->ds, $sr);
          
      $arUsers=array();

      foreach($Infos as $key => $oneMember)         
      { 
          $arU = preg_replace("/(uid=)(([a-z0-9.\-_]{1,}))(,ou=.*)/","$3",$oneMember['member']);
          if ($arU !="") $arUsers=$arU;         
      }

      return $arUsers;

    }
    
    /**
     * Renvoi un tableau d'information utilisateur du LDAP
     * @param type $uid_login
     * @return type $tabLdapUser tableau
     */    
    function getUserInfo($uid_login) {

        $tabLdapUser = array();
        $Infos = array();
                       
        $restriction = array("uid","sn","givenname","mail","supanncivilite","amumail","edupersonprimaryaffiliation");
        $filtre="(&(uid=".$uid_login."))";
        
        $sr=ldap_search($this->ds, $this->racineAMU, $filtre, $restriction);
          
        $Infos = ldap_get_entries($this->ds, $sr);    
                
        if ($Infos["count"] == 0) {           
            $Trouver=false;
        } else {
            $Trouver=true;
            $tabLdapUser['identification']="Yes";
            $tabLdapUser['Nom']=utf8_decode($Infos[0]["sn"][0]);
            $tabLdapUser['Prenom']=utf8_decode($Infos[0]["givenname"][0]);
            $tabLdapUser['Email']=$Infos[0]["mail"][0];
            $tabLdapUser['Civilite']=$Infos[0]["supanncivilite"][0];             
            $tabLdapUser['login']=$Infos[0]["uid"][0];
            $tabLdapUser['Mail']=$Infos[0]["mail"][0]; 
            $tabLdapUser['eduPersonPrimaryAffiliation']=$Infos[0]["edupersonprimaryaffiliation"][0]; 
            $tabLdapUser['userPassword']=$Infos[0]["userpassword"][0];
        }
        return ($tabLdapUser);
    }    
}
