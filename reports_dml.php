<?php

// Data functions for table reports

// This script and data application were generated by AppGini 5.23
// Download AppGini for free from http://bigprof.com/appgini/download/

function reports_insert(){
	global $Translation;

	if($_GET['insert_x']!=''){$_POST=$_GET;}

	// mm: can member insert record?
	$arrPerm=getTablePermissions('reports');
	if(!$arrPerm[1]){
		return false;
	}

	$data['date'] = intval($_POST['dateYear']) . '-' . intval($_POST['dateMonth']) . '-' . intval($_POST['dateDay']);
	$data['date'] = parseMySQLDate($data['date'], '1');
	$data['company'] = makeSafe($_POST['company']);
		if($data['company'] == empty_lookup_value){ $data['company'] = ''; }
	$data['created'] = parseCode('<%%creationDate%%>', true, true);
	$data['created_by'] = parseCode('<%%creatorUsername%%>', true);

	// hook: reports_before_insert
	if(function_exists('reports_before_insert')){
		$args=array();
		if(!reports_before_insert($data, getMemberInfo(), $args)){ return false; }
	}

	$o=array('silentErrors' => true);
	sql('insert into `reports` set       `date`=' . (($data['date'] !== '' && $data['date'] !== NULL) ? "'{$data['date']}'" : 'NULL') . ', `company`=' . (($data['company'] !== '' && $data['company'] !== NULL) ? "'{$data['company']}'" : 'NULL') . ', `created`=' . "'{$data['created']}'" . ', `created_by`=' . "'{$data['created_by']}'", $o);
	if($o['error']!=''){
		echo $o['error'];
		echo "<a href=\"reports_view.php?addNew_x=1\">{$Translation['< back']}</a>";
		exit;
	}

	$recID=mysql_insert_id();

	// hook: reports_after_insert
	if(function_exists('reports_after_insert')){
		$res = sql("select * from `reports` where `report_id`='" . makeSafe($recID) . "' limit 1", $eo);
		if($row = mysql_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID);
		$args=array();
		if(!reports_after_insert($data, getMemberInfo(), $args)){ return (get_magic_quotes_gpc() ? stripslashes($recID) : $recID); }
	}

	// mm: save ownership data
	sql("insert into membership_userrecords set tableName='reports', pkValue='$recID', memberID='".getLoggedMemberID()."', dateAdded='".time()."', dateUpdated='".time()."', groupID='".getLoggedGroupID()."'", $eo);

	return (get_magic_quotes_gpc() ? stripslashes($recID) : $recID);
}

function reports_delete($selected_id, $AllowDeleteOfParents=false, $skipChecks=false){
	// insure referential integrity ...
	global $Translation;
	$selected_id=makeSafe($selected_id);

	// mm: can member delete record?
	$arrPerm=getTablePermissions('reports');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='reports' and pkValue='$selected_id'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='reports' and pkValue='$selected_id'");
	if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
		// delete allowed, so continue ...
	}else{
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: reports_before_delete
	if(function_exists('reports_before_delete')){
		$args=array();
		if(!reports_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'];
	}

	// child table: entries
	$res = sql("select `report_id` from `reports` where `report_id`='$selected_id'", $eo);
	$report_id = mysql_fetch_row($res);
	$rires = sql("select count(1) from `entries` where `report`='".addslashes($report_id[0])."'", $eo);
	$rirow = mysql_fetch_row($rires);
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks){
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace("<RelatedRecords>", $rirow[0], $RetMsg);
		$RetMsg = str_replace("<TableName>", "entries", $RetMsg);
		return $RetMsg;
	}elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks){
		$RetMsg = $Translation["confirm delete"];
		$RetMsg = str_replace("<RelatedRecords>", $rirow[0], $RetMsg);
		$RetMsg = str_replace("<TableName>", "entries", $RetMsg);
		$RetMsg = str_replace("<Delete>", "<input tabindex=\"2\" type=\"button\" class=\"button\" value=\"".$Translation['yes']."\" onClick=\"window.location='reports_view.php?SelectedID=".urlencode($selected_id)."&delete_x=1&confirmed=1';\">", $RetMsg);
		$RetMsg = str_replace("<Cancel>", "<input tabindex=\"2\" type=\"button\" class=\"button\" value=\"".$Translation['no']."\" onClick=\"window.location='reports_view.php?SelectedID=".urlencode($selected_id)."';\">", $RetMsg);
		return $RetMsg;
	}

	sql("delete from `reports` where `report_id`='$selected_id'", $eo);

	// hook: reports_after_delete
	if(function_exists('reports_after_delete')){
		$args=array();
		reports_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("delete from membership_userrecords where tableName='reports' and pkValue='$selected_id'", $eo);
}

function reports_update($selected_id){
	global $Translation;

	if($_GET['update_x']!=''){$_POST=$_GET;}

	// mm: can member edit record?
	$arrPerm=getTablePermissions('reports');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='reports' and pkValue='".makeSafe($selected_id)."'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='reports' and pkValue='".makeSafe($selected_id)."'");
	if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){ // allow update?
		// update allowed, so continue ...
	}else{
		return false;
	}

	$data['date'] = intval($_POST['dateYear']) . '-' . intval($_POST['dateMonth']) . '-' . intval($_POST['dateDay']);
	$data['date'] = parseMySQLDate($data['date'], '1');
	$data['company'] = makeSafe($_POST['company']);
		if($data['company'] == empty_lookup_value){ $data['company'] = ''; }
	$data['created'] = parseMySQLDate('', '<%%creationDate%%>');
	$data['selectedID']=makeSafe($selected_id);

	// hook: reports_before_update
	if(function_exists('reports_before_update')){
		$args=array();
		if(!reports_before_update($data, getMemberInfo(), $args)){ return false; }
	}

	$o=array('silentErrors' => true);
	sql('update `reports` set       `date`=' . (($data['date'] !== '' && $data['date'] !== NULL) ? "'{$data['date']}'" : 'NULL') . ', `company`=' . (($data['company'] !== '' && $data['company'] !== NULL) ? "'{$data['company']}'" : 'NULL') . ', `created`=' . (($data['created'] != '') ? "'{$data['created']}'" : 'NULL') . " where `report_id`='".makeSafe($selected_id)."'", $o);
	if($o['error']!=''){
		echo $o['error'];
		echo '<a href="reports_view.php?SelectedID='.urlencode($selected_id)."\">{$Translation['< back']}</a>";
		exit;
	}


	// hook: reports_after_update
	if(function_exists('reports_after_update')){
		$res = sql("SELECT * FROM `reports` WHERE `report_id`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = mysql_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = $data['report_id'];
		$args = array();
		if(!reports_after_update($data, getMemberInfo(), $args)){ return; }
	}

	// mm: update ownership data
	sql("update membership_userrecords set dateUpdated='".time()."' where tableName='reports' and pkValue='".makeSafe($selected_id)."'", $eo);

}

function reports_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0){
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;

	// mm: get table permissions
	$arrPerm=getTablePermissions('reports');
	if(!$arrPerm[1] && $selected_id==''){ return ''; }
	// print preview?
	$dvprint = false;
	if($selected_id && $_REQUEST['dvprint_x'] != ''){
		$dvprint = true;
	}

	$filterer_company = thisOr(undo_magic_quotes($_REQUEST['filterer_company']), '');

	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: date
	$combo_date = new DateCombo;
	$combo_date->DateFormat = "dmy";
	$combo_date->MinYear = 1900;
	$combo_date->MaxYear = 2100;
	$combo_date->DefaultDate = parseMySQLDate('1', '1');
	$combo_date->MonthNames = $Translation['month names'];
	$combo_date->NamePrefix = 'date';
	// combobox: company
	$combo_company = new DataCombo;
	// combobox: created
	$combo_created = new DateCombo;
	$combo_created->DateFormat = "dmy";
	$combo_created->MinYear = 1900;
	$combo_created->MaxYear = 2100;
	$combo_created->DefaultDate = parseMySQLDate('<%%creationDate%%>', '<%%creationDate%%>');
	$combo_created->MonthNames = $Translation['month names'];
	$combo_created->NamePrefix = 'created';

	if($selected_id){
		// mm: check member permissions
		if(!$arrPerm[2]){
			return "";
		}
		// mm: who is the owner?
		$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='reports' and pkValue='".makeSafe($selected_id)."'");
		$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='reports' and pkValue='".makeSafe($selected_id)."'");
		if($arrPerm[2]==1 && getLoggedMemberID()!=$ownerMemberID){
			return "";
		}
		if($arrPerm[2]==2 && getLoggedGroupID()!=$ownerGroupID){
			return "";
		}

		// can edit?
		if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){
			$AllowUpdate=1;
		}else{
			$AllowUpdate=0;
		}

		$res = sql("select * from `reports` where `report_id`='".makeSafe($selected_id)."'", $eo);
		$row = mysql_fetch_array($res);
		$urow = $row; /* unsanitized data */
		$hc = new CI_Input();
		$row = $hc->xss_clean($row); /* sanitize data */
		$combo_date->DefaultDate = $row['date'];
		$combo_company->SelectedData = $row['company'];
		$combo_created->DefaultDate = $row['created'];
	}else{
		$combo_company->SelectedData = $filterer_company;
	}
	$combo_company->HTML = $combo_company->MatchText = '<span id="company-container' . $rnd1 . '"></span><input type="hidden" name="company" id="company' . $rnd1 . '">';

	ob_start();
	?>

	<script>
		// initial lookup values
		var current_company__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['company'] : $filterer_company); ?>"};
		
		jQuery(function() {
			company_reload__RAND__();
		});
		function company_reload__RAND__(){
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint){ ?>

			jQuery("#company-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c){
					jQuery.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: current_company__RAND__.value, t: 'reports', f: 'company' }
					}).done(function(resp){
						c({
							id: resp.results[0].id,
							text: resp.results[0].text
						});
						jQuery('[name="company"]').val(resp.results[0].id);


						if(typeof(company_update_autofills__RAND__) == 'function') company_update_autofills__RAND__();
					});
				},
				width: '100%',
				formatNoMatches: function(term){ return '<?php echo addslashes($Translation['No matches found!']); ?>'; },
				minimumResultsForSearch: 10,
				loadMorePadding: 200,
				ajax: {
					url: 'ajax_combo.php',
					dataType: 'json',
					cache: true,
					data: function(term, page){ return { s: term, p: page, t: 'reports', f: 'company' }; },
					results: function(resp, page){ return resp; }
				}
			}).on('change', function(e){
				current_company__RAND__.value = e.added.id;
				current_company__RAND__.text = e.added.text;
				jQuery('[name="company"]').val(e.added.id);


				if(typeof(company_update_autofills__RAND__) == 'function') company_update_autofills__RAND__();
			});
		<?php }else{ ?>

			jQuery.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: current_company__RAND__.value, t: 'reports', f: 'company' }
			}).done(function(resp){
				jQuery('#company-container__RAND__').html('<span id="company-match-text">' + resp.results[0].text + '</span>');

				if(typeof(company_update_autofills__RAND__) == 'function') company_update_autofills__RAND__();
			});
		<?php } ?>

		}
	</script>
	<?php
	
	$lookups = str_replace('__RAND__', $rnd1, ob_get_contents());
	ob_end_clean();


	// code for template based detail view forms

	// open the detail view template
	if($dvprint){
		$templateCode = @file_get_contents('./templates/reports_templateDVP.html');
	}else{
		$templateCode = @file_get_contents('./templates/reports_templateDV.html');
	}

	// process form title
	$templateCode=str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Report details', $templateCode);
	$templateCode=str_replace('<%%RND1%%>', $rnd1, $templateCode);
	// process buttons
	if($arrPerm[1]){ // allow insert?
		if(!$selected_id) $templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return reports_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return reports_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	}else{
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if($_REQUEST['Embedded']){
		$backAction = 'window.parent.jQuery(\'.modal\').modal(\'hide\'); return false;';
	}else{
		$backAction = '$$(\'form\')[0].writeAttribute(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id){
		if(!$_REQUEST['Embedded']) $templateCode=str_replace('<%%DVPRINT_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$$(\'form\')[0].writeAttribute(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate){
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return reports_validateData();"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		}else{
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		}
		if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
			$templateCode=str_replace('<%%DELETE_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" onclick="return confirm(\'' . $Translation['are you sure?'] . '\');"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		}else{
			$templateCode=str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
		$templateCode=str_replace('<%%DESELECT_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	}else{
		$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode=str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		$templateCode=str_replace('<%%DESELECT_BUTTON%%>', ($ShowCancel ? '<button tabindex="2" type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>' : ''), $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate && !$arrPerm[1]) || (!$selected_id && !$arrPerm[1])){
		$jsReadOnly .= "\tjQuery('#date').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#dateDay, #dateMonth, #dateYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#company').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#company_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";

		$noUploads = true;
	}

	// process combos
	$templateCode=str_replace('<%%COMBO(date)%%>', ($selected_id && !$arrPerm[3] ? '<p class="form-control-static">' . $combo_date->GetHTML(true) . '</p>' : $combo_date->GetHTML()), $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(date)%%>', $combo_date->GetHTML(true), $templateCode);
	$templateCode=str_replace('<%%COMBO(company)%%>', $combo_company->HTML, $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(company)%%>', $combo_company->MatchText, $templateCode);
	$templateCode=str_replace('<%%URLCOMBOTEXT(company)%%>', urlencode($combo_company->MatchText), $templateCode);
	$templateCode=str_replace('<%%COMBO(created)%%>', ($selected_id && !$arrPerm[3] ? '<p class="form-control-static">' . $combo_created->GetHTML(true) . '</p>' : $combo_created->GetHTML()), $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(created)%%>', $combo_created->GetHTML(true), $templateCode);

	// process foreign key links
	if($selected_id){
		$templateCode=str_replace('<%%PLINK(company)%%>', ($combo_company->SelectedData ? "<span id=\"companies_plink1\" class=\"hidden\"><a class=\"btn btn-default\" href=\"companies_view.php?SelectedID=" . urlencode($combo_company->SelectedData) . "\"><i class=\"glyphicon glyphicon-search\"></i></a></span>" : ''), $templateCode);
	}

	// process images
	$templateCode=str_replace('<%%UPLOADFILE(report_id)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(date)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(company)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(created)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(created_by)%%>', '', $templateCode);

	// process values
	if($selected_id){
		$templateCode=str_replace('<%%VALUE(report_id)%%>', htmlspecialchars($row['report_id'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(report_id)%%>', urlencode($urow['report_id']), $templateCode);
		$templateCode=str_replace('<%%VALUE(date)%%>', @date('d/m/Y', @strtotime(htmlspecialchars($row['date'], ENT_QUOTES))), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(date)%%>', urlencode(@date('d/m/Y', @strtotime(htmlspecialchars($urow['date'], ENT_QUOTES)))), $templateCode);
		$templateCode=str_replace('<%%VALUE(company)%%>', htmlspecialchars($row['company'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(company)%%>', urlencode($urow['company']), $templateCode);
		$templateCode=str_replace('<%%VALUE(created)%%>', @date('d/m/Y', @strtotime(htmlspecialchars($row['created'], ENT_QUOTES))), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(created)%%>', urlencode(@date('d/m/Y', @strtotime(htmlspecialchars($urow['created'], ENT_QUOTES)))), $templateCode);
		$templateCode=str_replace('<%%VALUE(created_by)%%>', htmlspecialchars($row['created_by'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(created_by)%%>', urlencode($urow['created_by']), $templateCode);
	}else{
		$templateCode=str_replace('<%%VALUE(report_id)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(report_id)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(date)%%>', '1', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(date)%%>', urlencode('1'), $templateCode);
		$templateCode=str_replace('<%%VALUE(company)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(company)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(created)%%>', '<%%creationDate%%>', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(created)%%>', urlencode('<%%creationDate%%>'), $templateCode);
		$templateCode=str_replace('<%%VALUE(created_by)%%>', '<%%creatorUsername%%>', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(created_by)%%>', urlencode('<%%creatorUsername%%>'), $templateCode);
	}

	// process translations
	foreach($Translation as $symbol=>$trans){
		$templateCode=str_replace("<%%TRANSLATION($symbol)%%>", $trans, $templateCode);
	}

	// clear scrap
	$templateCode=str_replace('<%%', '<!-- ', $templateCode);
	$templateCode=str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if($_POST['dvprint_x']==''){
		$templateCode.="\n\n<script>jQuery(function(){\n";
		$arrTables=getTableList();
		foreach($arrTables as $name => $caption){
			$templateCode .= "\tjQuery('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\tjQuery('#xs_{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\tjQuery('[id^=\"{$name}_plink\"]').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;

		if(!$selected_id){
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode.="<script>";
	$templateCode.="document.observe('dom:loaded', function() {";


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields

	// don't include blank images in lightbox gallery
	$templateCode=preg_replace('/blank.gif" rel="lightbox\[.*?\]"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	// hook: reports_dv
	if(function_exists('reports_dv')){
		$args=array();
		reports_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}
?>