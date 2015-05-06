<?php
style ('user_cas', 'cas');
?>

<form id="cas" class="section" action="#" method="post">
    <h2><?php p($l->t('CAS')); ?></h2>
    
    <div id="casSettings" >
        
        <strong><?php p($l->t('CAS Authentication backend'));?></strong>
	<ul>
		<li><a href="#casSettings-1"><?php p($l->t('CAS Server'));?></a></li>
		<li><a href="#casSettings-2"><?php p($l->t('Access => LDAP Groups'));?></a></li>
		<li><a href="#casSettings-3"><?php p($l->t('Reverse Proxy'));?></a></li>
	</ul>

	<fieldset id="casSettings-1">
            <table border="0">
		<tr>
                    <td ><label for="cas_server_version"><?php p($l->t('CAS Server Version'));?>&nbsp;&nbsp;</label></td>
                    <td>
                    <select id="cas_server_version" name="cas_server_version">
                        <?php if($_['cas_server_version'] == '2.0') { ?>
                            <option value="2.0" selected>CAS 2.0</option>
                            <option value="1.0">CAS 1.0</option>
                        <?php } else { ?>
                            <option value="2.0">CAS 2.0</option>
                            <option value="1.0" selected>CAS 1.0</option>
                        <?php } ?>
                    </select>
                    </td>
                </tr>                
                <tr><td><label for="cas_server_hostname">CAS server&nbsp;&nbsp;</label></td>
                    <td><input type="text" id="cas_server_hostname" 
                               name="cas_server_hostname" 
                               value="<?php p($_['cas_server_hostname']); ?>"
                               title="<?php p($l->t('Serveur CAS'));?>"
                               >
                    </td>
                </tr>
                <tr><td><label for="cas_server_port">Port&nbsp;&nbsp;</label></td>
                    <td><input type="text" id="cas_server_port" 
                               name="cas_server_port" 
                               value="<?php p($_['cas_server_port']); ?>"
                               title="<?php p($l->t('Port'));?>"
                               >
                    </td>
                </tr>
                <tr><td><label for="cas_server_path">Path&nbsp;&nbsp;</label></td>
                    <td><input type="text" 
                               id="cas_server_path" 
                               name="cas_server_path" 
                               value="<?php p($_['cas_server_path']); ?>"
                               title="<?php p($l->t('Path'));?>"
                               >
                    </td>
                </tr>
            </table>
	</fieldset>
        
	<fieldset id="casSettings-2">
            <table border="0">
                <tr><td><label for="cas_group_mapping">Groups<br/>(Grouper name)&nbsp;&nbsp; </label></td>
                    <td><textarea class="tablecell lwautosave SPE"
                                   placeholder="<?php p($l->t('No groups mapping'));?>" 
                                   title="<?php p($l->t('Laisser vide si non géré'));?>" 
                                   id="cas_group_mapping" 
                                   name="cas_group_mapping" 
                                   rows="5" ><?php  p($_['cas_group_mapping']); ?></textarea>
                    </td>
                </tr>                    
               <!--
               <textarea id="ldap_base" name="ldap_base" class="tablecell lwautosave" placeholder="Un DN racine par ligne" title="Vous pouvez spécifier les DN Racines de vos utilisateurs et groupes via l'onglet Avancé">				</textarea>
                -->
                <tr><td><label for="cas_group_root">Root Group</label></td>
                    <td><input type="text" 
                               id="cas_group_root" 
                               name="cas_group_root" 
                               value="<?php p($_['cas_group_root']); ?>"     
                               placeholder="<?php p($l->t('No root for LDAP groups'));?>"
                               title="<?php p($l->t('Laisser vide si non géré'));?>" 
                               >
                    </td>                    
                </tr>
            </table>
	</fieldset>
	<fieldset id="casSettings-3">
            <table border="0" >
                <tr><td ><label>Service URL</label></td>
                    <td ><input type="text" id="cas_aliasName" 
                                name="cas_aliasName" 
                                value="<?php p($_['cas_aliasName']); ?>"
                                title="<?php p($l->t('A renseigner si utilisation d\'un reverse proxy '));?>"
                                ></td>
                </tr>
            </table>
	</fieldset>
	<input type="submit" value="Submit" />
	</div>
</form>
