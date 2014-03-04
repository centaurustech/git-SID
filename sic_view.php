<?php
// This script and data application were generated by AppGini 5.23
// Download AppGini for free from http://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/sic.php");
	include("$currDir/sic_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('sic');
	if(!$perm[0]){
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "sic";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV=array(   
		"`sic`.`sic_id`" => "sic_id",
		"`sic`.`code`" => "code",
		"`sic`.`activity`" => "activity"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`sic`.`sic_id`',
		2 => '`sic`.`code`',
		3 => 3
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV=array(   
		"`sic`.`sic_id`" => "sic_id",
		"`sic`.`code`" => "code",
		"`sic`.`activity`" => "activity"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters=array(   
		"`sic`.`sic_id`" => "ID",
		"`sic`.`code`" => "Code",
		"`sic`.`activity`" => "Activity"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS=array(   
		"`sic`.`sic_id`" => "sic_id",
		"`sic`.`code`" => "code",
		"`sic`.`activity`" => "activity"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array();

	$x->QueryFrom="`sic` ";
	$x->QueryWhere='';
	$x->QueryOrder='';

	$x->AllowSelection = 0;
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
	$x->AllowCSV = 0;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "sic_view.php";
	$x->RedirectAfterInsert = "sic_view.php?SelectedID=#ID#";
	$x->TableTitle = "SIC";
	$x->TableIcon = "resources/table_icons/barcode.png";
	$x->PrimaryKey = "`sic`.`sic_id`";

	$x->ColWidth   = array(  150, 150);
	$x->ColCaption = array("Code", "Activity");
	$x->ColFieldName = array('code', 'activity');
	$x->ColNumber  = array(2, 3);

	$x->Template = 'templates/sic_templateTV.html';
	$x->SelectedTemplate = 'templates/sic_templateTVS.html';
	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `sic`.`sic_id`=membership_userrecords.pkValue and membership_userrecords.tableName='sic' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `sic`.`sic_id`=membership_userrecords.pkValue and membership_userrecords.tableName='sic' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`sic`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: sic_init
	$render=TRUE;
	if(function_exists('sic_init')){
		$args=array();
		$render=sic_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: sic_header
	$headerCode='';
	if(function_exists('sic_header')){
		$args=array();
		$headerCode=sic_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: sic_footer
	$footerCode='';
	if(function_exists('sic_footer')){
		$args=array();
		$footerCode=sic_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>