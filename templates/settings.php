
<link rel="stylesheet" type="text/css" href="/apps/user_cas/css/cas.css" />

<form id="cas" class="section" action="#" method="post">
	<div id="casSettings" >
    <strong><?php p($l->t('CAS Authentication backend'));?></strong>
	<ul>
		<li><a href="#casSettings-1"><?php p($l->t('CAS Server'));?></a></li>
		<li><a href="#casSettings-2"><?php p($l->t('Access => Grouper'));?></a></li>
		<li><a href="#casSettings-3"><?php p($l->t('Reverse Proxy'));?></a></li>
	</ul>

	<fieldset id="casSettings-1">
            <table border="0">
		<tr><td ><label for="cas_server_version"><?php p($l->t('CAS Server Version'));?>&nbsp;&nbsp;
</label></td><td>
		<select id="cas_server_version" name="cas_server_version">
	<?php
		if($_['cas_server_version'] == '2.0') {
	?>
			<option value="2.0" selected>CAS 2.0</option>
			<option value="1.0">CAS 1.0</option>
	<?php
		}
		else {
	?>
                        <option value="2.0">CAS 2.0</option>
                        <option value="1.0" selected>CAS 1.0</option>
	<?php
		}
	?>
		</select>
		</td></tr>
                
                    <tr><td><label for="cas_server_hostname">CAS server&nbsp;&nbsp;</label></td><td><input type="text" id="cas_server_hostname" name="cas_server_hostname" value="<?php p($_['cas_server_hostname']); ?>"></td></tr>
                    <tr><td><label for="cas_server_port">Port&nbsp;&nbsp;</label></td><td><input type="text" id="cas_server_port" name="cas_server_port" value="<?php p($_['cas_server_port']); ?>"></td></tr>
                    <tr><td><label for="cas_server_path">Path&nbsp;&nbsp;</label></td><td><input type="text" id="cas_server_path" name="cas_server_path" value="<?php p($_['cas_server_path']); ?>"></td></tr>
                </table>
	</fieldset>
	<fieldset id="casSettings-2">
            <table border="0">
                <tr><td ><label for="cas_group_mapping">Groups<br/>(Grouper name)&nbsp;&nbsp; </label></td>
                    <td ><textarea class="SPE" class="tablecell lwautosave" placeholder="niv0:niv1:niv2:niv3" title="Un groupe par ligne"id="cas_group_mapping" name="cas_group_mapping" rows="5" ><?php p($_['cas_group_mapping']); ?></textarea></td></tr>    
                
               <!--
               <textarea id="ldap_base" name="ldap_base" class="tablecell lwautosave" placeholder="Un DN racine par ligne" title="Vous pouvez spécifier les DN Racines de vos utilisateurs et groupes via l'onglet Avancé">				</textarea>
                -->
                
                
                <tr><td><label for="cas_group_root">Root Group</label></td><td><input type="text" id="cas_group_root" name="cas_group_root" value="<?php p($_['cas_group_root']); ?>"></td></tr>
            </table>
	</fieldset>
	<fieldset id="casSettings-3">
            <table border="0" >
                <tr><td ><label>Service URL</label></td>
                    <td ><input type="text" id="cas_aliasName" name="cas_aliasName" value="<?php p($_['cas_aliasName']); ?>"></td></tr>
            </table>
	</fieldset>
	<input type="submit" value="Submit" />
	</div>
</form>
