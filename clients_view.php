<?php
// This script and data application were generated by AppGini 5.23
// Download AppGini for free from http://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/clients.php");
	include("$currDir/clients_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('clients');
	if(!$perm[0]){
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "clients";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV=array(   
		"`clients`.`client_id`" => "client_id",
		"`clients`.`name`" => "name",
		"if(`clients`.`created`,date_format(`clients`.`created`,'%d/%m/%Y'),'')" => "created",
		"`clients`.`created_by`" => "created_by"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`clients`.`client_id`',
		2 => 2,
		3 => '`clients`.`created`',
		4 => 4
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV=array(   
		"`clients`.`client_id`" => "client_id",
		"`clients`.`name`" => "name",
		"if(`clients`.`created`,date_format(`clients`.`created`,'%d/%m/%Y'),'')" => "created",
		"`clients`.`created_by`" => "created_by"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters=array(   
		"`clients`.`client_id`" => "ID",
		"`clients`.`name`" => "Name",
		"`clients`.`created`" => "Created",
		"`clients`.`created_by`" => "Created by"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS=array(   
		"`clients`.`client_id`" => "client_id",
		"`clients`.`name`" => "name",
		"if(`clients`.`created`,date_format(`clients`.`created`,'%d/%m/%Y'),'')" => "created",
		"`clients`.`created_by`" => "created_by"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array();

	$x->QueryFrom="`clients` ";
	$x->QueryWhere='';
	$x->QueryOrder='';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm[2]==0 ? 1 : 0);
	$x->AllowDelete = $perm[4];
	$x->AllowInsert = $perm[1];
	$x->AllowUpdate = $perm[3];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 0;
	$x->AllowSavingFilters = 0;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 0;
	$x->AllowPrintingMultiSelection = 0;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "clients_view.php";
	$x->RedirectAfterInsert = "clients_view.php";
	$x->TableTitle = "Clients";
	$x->TableIcon = "resources/table_icons/account_balances.png";
	$x->PrimaryKey = "`clients`.`client_id`";
	$x->DefaultSortField = '1';
	$x->DefaultSortDirection = 'asc';

	$x->ColWidth   = array(  150, 20, 30);
	$x->ColCaption = array("Name", "Created", "Created by");
	$x->ColFieldName = array('name', 'created', 'created_by');
	$x->ColNumber  = array(2, 3, 4);

	$x->Template = 'templates/clients_templateTV.html';
	$x->SelectedTemplate = 'templates/clients_templateTVS.html';
	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `clients`.`client_id`=membership_userrecords.pkValue and membership_userrecords.tableName='clients' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `clients`.`client_id`=membership_userrecords.pkValue and membership_userrecords.tableName='clients' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`clients`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: clients_init
	$render=TRUE;
	if(function_exists('clients_init')){
		$args=array();
		$render=clients_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: clients_header
	$headerCode='';
	if(function_exists('clients_header')){
		$args=array();
		$headerCode=clients_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: clients_footer
	$footerCode='';
	if(function_exists('clients_footer')){
		$args=array();
		$footerCode=clients_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>