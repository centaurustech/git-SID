<?php
// This script and data application were generated by AppGini 5.23
// Download AppGini for free from http://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/tax_entry.php");
	include("$currDir/tax_entry_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('tax_entry');
	if(!$perm[0]){
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "tax_entry";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV=array(   
		"`tax_entry`.`tax_entry_id`" => "tax_entry_id",
		"if(`tax_entry`.`created`,date_format(`tax_entry`.`created`,'%d/%m/%Y'),'')" => "created",
		"`tax_entry`.`created_by`" => "created_by",
		"IF(    CHAR_LENGTH(`companies1`.`name`) || CHAR_LENGTH(`clients1`.`name`) || CHAR_LENGTH(`reports1`.`start_date`), CONCAT_WS('',   `companies1`.`name`, ' - ', `clients1`.`name`, ' - ', `reports1`.`start_date`), '') /* Report */" => "report",
		"`tax_entry`.`turnover`" => "turnover",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`payroll_tax`, 2))" => "payroll_tax",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`ni_contribution`, 2))" => "ni_contribution",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`business_rates`, 2))" => "business_rates",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`corporation_tax`, 2))" => "corporation_tax",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`other_tax`, 2))" => "other_tax",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`sum`, 2))" => "sum",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`ratio`, 2))" => "ratio"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`tax_entry`.`tax_entry_id`',
		2 => '`tax_entry`.`created`',
		3 => 3,
		4 => 4,
		5 => 5,
		6 => 6,
		7 => 7,
		8 => 8,
		9 => 9,
		10 => 10,
		11 => 11,
		12 => 12
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV=array(   
		"`tax_entry`.`tax_entry_id`" => "tax_entry_id",
		"if(`tax_entry`.`created`,date_format(`tax_entry`.`created`,'%d/%m/%Y'),'')" => "created",
		"`tax_entry`.`created_by`" => "created_by",
		"IF(    CHAR_LENGTH(`companies1`.`name`) || CHAR_LENGTH(`clients1`.`name`) || CHAR_LENGTH(`reports1`.`start_date`), CONCAT_WS('',   `companies1`.`name`, ' - ', `clients1`.`name`, ' - ', `reports1`.`start_date`), '') /* Report */" => "report",
		"`tax_entry`.`turnover`" => "turnover",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`payroll_tax`, 2))" => "payroll_tax",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`ni_contribution`, 2))" => "ni_contribution",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`business_rates`, 2))" => "business_rates",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`corporation_tax`, 2))" => "corporation_tax",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`other_tax`, 2))" => "other_tax",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`sum`, 2))" => "sum",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`ratio`, 2))" => "ratio"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters=array(   
		"`tax_entry`.`tax_entry_id`" => "Tax id",
		"`tax_entry`.`created`" => "Created",
		"`tax_entry`.`created_by`" => "Created by",
		"IF(    CHAR_LENGTH(`companies1`.`name`) || CHAR_LENGTH(`clients1`.`name`) || CHAR_LENGTH(`reports1`.`start_date`), CONCAT_WS('',   `companies1`.`name`, ' - ', `clients1`.`name`, ' - ', `reports1`.`start_date`), '') /* Report */" => "Report",
		"`tax_entry`.`turnover`" => "Turnover",
		"`tax_entry`.`payroll_tax`" => "Payroll tax (&#163;m)",
		"`tax_entry`.`ni_contribution`" => "NI contribution (&#163;m)",
		"`tax_entry`.`business_rates`" => "Business rates (&#163;m)",
		"`tax_entry`.`corporation_tax`" => "Corporation tax (&#163;m)",
		"`tax_entry`.`other_tax`" => "Other tax (&#163;m)",
		"`tax_entry`.`sum`" => "Sum of all tax",
		"`tax_entry`.`ratio`" => "Ratio"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS=array(   
		"`tax_entry`.`tax_entry_id`" => "tax_entry_id",
		"if(`tax_entry`.`created`,date_format(`tax_entry`.`created`,'%d/%m/%Y'),'')" => "created",
		"`tax_entry`.`created_by`" => "created_by",
		"IF(    CHAR_LENGTH(`companies1`.`name`) || CHAR_LENGTH(`clients1`.`name`) || CHAR_LENGTH(`reports1`.`start_date`), CONCAT_WS('',   `companies1`.`name`, ' - ', `clients1`.`name`, ' - ', `reports1`.`start_date`), '') /* Report */" => "report",
		"`tax_entry`.`turnover`" => "turnover",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`payroll_tax`, 2))" => "payroll_tax",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`ni_contribution`, 2))" => "ni_contribution",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`business_rates`, 2))" => "business_rates",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`corporation_tax`, 2))" => "corporation_tax",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`other_tax`, 2))" => "other_tax",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`sum`, 2))" => "sum",
		"CONCAT('&pound;', FORMAT(`tax_entry`.`ratio`, 2))" => "ratio"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array(  'report' => 'Report');

	$x->QueryFrom="`tax_entry` LEFT JOIN `reports` as reports1 ON `reports1`.`report_id`=`tax_entry`.`report` LEFT JOIN `companies` as companies1 ON `companies1`.`company_id`=`reports1`.`company` LEFT JOIN `clients` as clients1 ON `clients1`.`client_id`=`companies1`.`client` ";
	$x->QueryWhere='';
	$x->QueryOrder='';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm[2]==0 ? 1 : 0);
	$x->AllowDelete = $perm[4];
	$x->AllowInsert = $perm[1];
	$x->AllowUpdate = $perm[3];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 0;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowPrintingMultiSelection = 0;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "tax_entry_view.php";
	$x->RedirectAfterInsert = "tax_entry_view.php?SelectedID=#ID#";
	$x->TableTitle = "Tax";
	$x->TableIcon = "table.gif";
	$x->PrimaryKey = "`tax_entry`.`tax_entry_id`";

	$x->ColWidth   = array(  20, 20, 30, 150);
	$x->ColCaption = array("Tax id", "Created", "Created by", "Report");
	$x->ColFieldName = array('tax_entry_id', 'created', 'created_by', 'report');
	$x->ColNumber  = array(1, 2, 3, 4);

	$x->Template = 'templates/tax_entry_templateTV.html';
	$x->SelectedTemplate = 'templates/tax_entry_templateTVS.html';
	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `tax_entry`.`tax_entry_id`=membership_userrecords.pkValue and membership_userrecords.tableName='tax_entry' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `tax_entry`.`tax_entry_id`=membership_userrecords.pkValue and membership_userrecords.tableName='tax_entry' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`tax_entry`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: tax_entry_init
	$render=TRUE;
	if(function_exists('tax_entry_init')){
		$args=array();
		$render=tax_entry_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: tax_entry_header
	$headerCode='';
	if(function_exists('tax_entry_header')){
		$args=array();
		$headerCode=tax_entry_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: tax_entry_footer
	$footerCode='';
	if(function_exists('tax_entry_footer')){
		$args=array();
		$footerCode=tax_entry_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>