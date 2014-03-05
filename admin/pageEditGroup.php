<?php
	$currDir=dirname(__FILE__);
	require("$currDir/incCommon.php");

	// get groupID of anonymous group
	$anonGroupID=sqlValue("select groupID from membership_groups where name='".$adminConfig['anonymousGroup']."'");

	// request to save changes?
	if($_POST['saveChanges']!=''){
		// validate data
		$name=makeSafe($_POST['name']);
		$description=makeSafe($_POST['description']);
		switch($_POST['visitorSignup']){
			case 0:
				$allowSignup=0;
				$needsApproval=1;
				break;
			case 2:
				$allowSignup=1;
				$needsApproval=0;
				break;
			default:
				$allowSignup=1;
				$needsApproval=1;
		}
		###############################
		$clients_insert=checkPermissionVal('clients_insert');
		$clients_view=checkPermissionVal('clients_view');
		$clients_edit=checkPermissionVal('clients_edit');
		$clients_delete=checkPermissionVal('clients_delete');
		###############################
		$companies_insert=checkPermissionVal('companies_insert');
		$companies_view=checkPermissionVal('companies_view');
		$companies_edit=checkPermissionVal('companies_edit');
		$companies_delete=checkPermissionVal('companies_delete');
		###############################
		$sic_insert=checkPermissionVal('sic_insert');
		$sic_view=checkPermissionVal('sic_view');
		$sic_edit=checkPermissionVal('sic_edit');
		$sic_delete=checkPermissionVal('sic_delete');
		###############################
		$reports_insert=checkPermissionVal('reports_insert');
		$reports_view=checkPermissionVal('reports_view');
		$reports_edit=checkPermissionVal('reports_edit');
		$reports_delete=checkPermissionVal('reports_delete');
		###############################
		$entries_insert=checkPermissionVal('entries_insert');
		$entries_view=checkPermissionVal('entries_view');
		$entries_edit=checkPermissionVal('entries_edit');
		$entries_delete=checkPermissionVal('entries_delete');
		###############################
		$outcome_areas_insert=checkPermissionVal('outcome_areas_insert');
		$outcome_areas_view=checkPermissionVal('outcome_areas_view');
		$outcome_areas_edit=checkPermissionVal('outcome_areas_edit');
		$outcome_areas_delete=checkPermissionVal('outcome_areas_delete');
		###############################
		$outcomes_insert=checkPermissionVal('outcomes_insert');
		$outcomes_view=checkPermissionVal('outcomes_view');
		$outcomes_edit=checkPermissionVal('outcomes_edit');
		$outcomes_delete=checkPermissionVal('outcomes_delete');
		###############################
		$beneficiary_groups_insert=checkPermissionVal('beneficiary_groups_insert');
		$beneficiary_groups_view=checkPermissionVal('beneficiary_groups_view');
		$beneficiary_groups_edit=checkPermissionVal('beneficiary_groups_edit');
		$beneficiary_groups_delete=checkPermissionVal('beneficiary_groups_delete');
		###############################
		$indicators_insert=checkPermissionVal('indicators_insert');
		$indicators_view=checkPermissionVal('indicators_view');
		$indicators_edit=checkPermissionVal('indicators_edit');
		$indicators_delete=checkPermissionVal('indicators_delete');
		###############################
		$tax_entry_insert=checkPermissionVal('tax_entry_insert');
		$tax_entry_view=checkPermissionVal('tax_entry_view');
		$tax_entry_edit=checkPermissionVal('tax_entry_edit');
		$tax_entry_delete=checkPermissionVal('tax_entry_delete');
		###############################

		// new group or old?
		if($_POST['groupID']==''){ // new group
			// make sure group name is unique
			if(sqlValue("select count(1) from membership_groups where name='$name'")){
				echo "<div class=\"alert alert-danger\">Error: Group name already exists. You must choose a unique group name.</div>";
				include("$currDir/incFooter.php");
			}

			// add group
			sql("insert into membership_groups set name='$name', description='$description', allowSignup='$allowSignup', needsApproval='$needsApproval'", $eo);

			// get new groupID
			$groupID=mysql_insert_id();

		}else{ // old group
			// validate groupID
			$groupID=intval($_POST['groupID']);

			if($groupID==$anonGroupID){
				$name=$adminConfig['anonymousGroup'];
				$allowSignup=0;
				$needsApproval=0;
			}

			// make sure group name is unique
			if(sqlValue("select count(1) from membership_groups where name='$name' and groupID!='$groupID'")){
				echo "<div class=\"alert alert-danger\">Error: Group name already exists. You must choose a unique group name.</div>";
				include("$currDir/incFooter.php");
			}

			// update group
			sql("update membership_groups set name='$name', description='$description', allowSignup='$allowSignup', needsApproval='$needsApproval' where groupID='$groupID'", $eo);

			// reset then add group permissions
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='clients'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='companies'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='sic'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='reports'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='entries'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='outcome_areas'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='outcomes'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='beneficiary_groups'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='indicators'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tax_entry'", $eo);
		}

		// add group permissions
		if($groupID){
			// table 'clients'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='clients', allowInsert='$clients_insert', allowView='$clients_view', allowEdit='$clients_edit', allowDelete='$clients_delete'", $eo);
			// table 'companies'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='companies', allowInsert='$companies_insert', allowView='$companies_view', allowEdit='$companies_edit', allowDelete='$companies_delete'", $eo);
			// table 'sic'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='sic', allowInsert='$sic_insert', allowView='$sic_view', allowEdit='$sic_edit', allowDelete='$sic_delete'", $eo);
			// table 'reports'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='reports', allowInsert='$reports_insert', allowView='$reports_view', allowEdit='$reports_edit', allowDelete='$reports_delete'", $eo);
			// table 'entries'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='entries', allowInsert='$entries_insert', allowView='$entries_view', allowEdit='$entries_edit', allowDelete='$entries_delete'", $eo);
			// table 'outcome_areas'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='outcome_areas', allowInsert='$outcome_areas_insert', allowView='$outcome_areas_view', allowEdit='$outcome_areas_edit', allowDelete='$outcome_areas_delete'", $eo);
			// table 'outcomes'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='outcomes', allowInsert='$outcomes_insert', allowView='$outcomes_view', allowEdit='$outcomes_edit', allowDelete='$outcomes_delete'", $eo);
			// table 'beneficiary_groups'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='beneficiary_groups', allowInsert='$beneficiary_groups_insert', allowView='$beneficiary_groups_view', allowEdit='$beneficiary_groups_edit', allowDelete='$beneficiary_groups_delete'", $eo);
			// table 'indicators'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='indicators', allowInsert='$indicators_insert', allowView='$indicators_view', allowEdit='$indicators_edit', allowDelete='$indicators_delete'", $eo);
			// table 'tax_entry'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tax_entry', allowInsert='$tax_entry_insert', allowView='$tax_entry_view', allowEdit='$tax_entry_edit', allowDelete='$tax_entry_delete'", $eo);
		}

		// redirect to group editing page
		redirect("admin/pageEditGroup.php?groupID=$groupID");

	}elseif($_GET['groupID']!=''){
		// we have an edit request for a group
		$groupID=intval($_GET['groupID']);
	}

	include("$currDir/incHeader.php");

	if($groupID!=''){
		// fetch group data to fill in the form below
		$res=sql("select * from membership_groups where groupID='$groupID'", $eo);
		if($row=mysql_fetch_assoc($res)){
			// get group data
			$name=$row['name'];
			$description=$row['description'];
			$visitorSignup=($row['allowSignup']==1 && $row['needsApproval']==1 ? 1 : ($row['allowSignup']==1 ? 2 : 0));

			// get group permissions for each table
			$res=sql("select * from membership_grouppermissions where groupID='$groupID'", $eo);
			while($row=mysql_fetch_assoc($res)){
				$tableName=$row['tableName'];
				$vIns=$tableName."_insert";
				$vUpd=$tableName."_edit";
				$vDel=$tableName."_delete";
				$vVue=$tableName."_view";
				$$vIns=$row['allowInsert'];
				$$vUpd=$row['allowEdit'];
				$$vDel=$row['allowDelete'];
				$$vVue=$row['allowView'];
			}
		}else{
			// no such group exists
			echo "<div class=\"alert alert-danger\">Error: Group not found!</div>";
			$groupID=0;
		}
	}
?>
<div class="page-header"><h1><?php echo ($groupID ? "Edit Group '$name'" : "Add New Group"); ?></h1></div>
<?php if($anonGroupID==$groupID){ ?>
	<div class="alert alert-warning">Attention! This is the anonymous group.</div>
<?php } ?>
<input type="checkbox" id="showToolTips" value="1" checked><label for="showToolTips">Show tool tips as mouse moves over options</label>
<form method="post" action="pageEditGroup.php">
	<input type="hidden" name="groupID" value="<?php echo $groupID; ?>">
	<div class="table-responsive"><table class="table table-striped">
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption">Group name</div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="name" <?php echo ($anonGroupID==$groupID ? "readonly" : ""); ?> value="<?php echo $name; ?>" size="20" class="formTextBox">
				<br />
				<?php if($anonGroupID==$groupID){ ?>
					The name of the anonymous group is read-only here.
				<?php }else{ ?>
					If you name the group '<?php echo $adminConfig['anonymousGroup']; ?>', it will be considered the anonymous group<br />
					that defines the permissions of guest visitors that do not log into the system.
				<?php } ?>
				</td>
			</tr>
		<tr>
			<td align="right" valign="top" class="tdFormCaption">
				<div class="formFieldCaption">Description</div>
				</td>
			<td align="left" class="tdFormInput">
				<textarea name="description" cols="50" rows="5" class="formTextBox"><?php echo $description; ?></textarea>
				</td>
			</tr>
		<?php if($anonGroupID!=$groupID){ ?>
		<tr>
			<td align="right" valign="top" class="tdFormCaption">
				<div class="formFieldCaption">Allow visitors to sign up?</div>
				</td>
			<td align="left" class="tdFormInput">
				<?php
					echo htmlRadioGroup(
						"visitorSignup",
						array(0, 1, 2),
						array(
							"No. Only the admin can add users.",
							"Yes, and the admin must approve them.",
							"Yes, and automatically approve them."
						),
						($groupID ? $visitorSignup : $adminConfig['defaultSignUp'])
					);
				?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="Save changes">
				</td>
			</tr>
		<tr>
			<td colspan="2" class="tdFormHeader">
				<table class="table table-striped">
					<tr>
						<td class="tdFormHeader" colspan="5"><h2>Table permissions for this group</h2></td>
						</tr>
					<?php
						// permissions arrays common to the radio groups below
						$arrPermVal=array(0, 1, 2, 3);
						$arrPermText=array("No", "Owner", "Group", "All");
					?>
					<tr>
						<td class="tdHeader"><div class="ColCaption">Table</div></td>
						<td class="tdHeader"><div class="ColCaption">Insert</div></td>
						<td class="tdHeader"><div class="ColCaption">View</div></td>
						<td class="tdHeader"><div class="ColCaption">Edit</div></td>
						<td class="tdHeader"><div class="ColCaption">Delete</div></td>
						</tr>
				<!-- clients table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Clients</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(clients_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="clients_insert" value="1" <?php echo ($clients_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("clients_view", $arrPermVal, $arrPermText, $clients_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("clients_edit", $arrPermVal, $arrPermText, $clients_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("clients_delete", $arrPermVal, $arrPermText, $clients_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- companies table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Companies</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(companies_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="companies_insert" value="1" <?php echo ($companies_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("companies_view", $arrPermVal, $arrPermText, $companies_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("companies_edit", $arrPermVal, $arrPermText, $companies_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("companies_delete", $arrPermVal, $arrPermText, $companies_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- sic table -->
					<tr>
						<td class="tdCaptionCell" valign="top">SIC</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(sic_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="sic_insert" value="1" <?php echo ($sic_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("sic_view", $arrPermVal, $arrPermText, $sic_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("sic_edit", $arrPermVal, $arrPermText, $sic_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("sic_delete", $arrPermVal, $arrPermText, $sic_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- reports table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Reports</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(reports_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="reports_insert" value="1" <?php echo ($reports_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("reports_view", $arrPermVal, $arrPermText, $reports_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("reports_edit", $arrPermVal, $arrPermText, $reports_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("reports_delete", $arrPermVal, $arrPermText, $reports_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- entries table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Entries</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(entries_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="entries_insert" value="1" <?php echo ($entries_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("entries_view", $arrPermVal, $arrPermText, $entries_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("entries_edit", $arrPermVal, $arrPermText, $entries_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("entries_delete", $arrPermVal, $arrPermText, $entries_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- outcome_areas table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Outcome areas</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(outcome_areas_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="outcome_areas_insert" value="1" <?php echo ($outcome_areas_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("outcome_areas_view", $arrPermVal, $arrPermText, $outcome_areas_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("outcome_areas_edit", $arrPermVal, $arrPermText, $outcome_areas_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("outcome_areas_delete", $arrPermVal, $arrPermText, $outcome_areas_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- outcomes table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Outcomes</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(outcomes_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="outcomes_insert" value="1" <?php echo ($outcomes_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("outcomes_view", $arrPermVal, $arrPermText, $outcomes_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("outcomes_edit", $arrPermVal, $arrPermText, $outcomes_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("outcomes_delete", $arrPermVal, $arrPermText, $outcomes_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- beneficiary_groups table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Beneficiary groups</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(beneficiary_groups_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="beneficiary_groups_insert" value="1" <?php echo ($beneficiary_groups_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("beneficiary_groups_view", $arrPermVal, $arrPermText, $beneficiary_groups_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("beneficiary_groups_edit", $arrPermVal, $arrPermText, $beneficiary_groups_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("beneficiary_groups_delete", $arrPermVal, $arrPermText, $beneficiary_groups_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- indicators table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Indicators</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(indicators_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="indicators_insert" value="1" <?php echo ($indicators_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("indicators_view", $arrPermVal, $arrPermText, $indicators_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("indicators_edit", $arrPermVal, $arrPermText, $indicators_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("indicators_delete", $arrPermVal, $arrPermText, $indicators_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tax_entry table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Tax</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tax_entry_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tax_entry_insert" value="1" <?php echo ($tax_entry_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tax_entry_view", $arrPermVal, $arrPermText, $tax_entry_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tax_entry_edit", $arrPermVal, $arrPermText, $tax_entry_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tax_entry_delete", $arrPermVal, $arrPermText, $tax_entry_delete, "highlight");
							?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		<tr>
			<td colspan="2" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="Save changes">
				</td>
			</tr>
		</table></div>
</form>


<?php
	include("$currDir/incFooter.php");
?>