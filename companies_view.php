<?php
// This script and data application were generated by AppGini 5.23
// Download AppGini for free from http://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/companies.php");
	include("$currDir/companies_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('companies');
	if(!$perm[0]){
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "companies";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV=array(   
		"`companies`.`company_id`" => "company_id",
		"`companies`.`name`" => "name",
		"IF(    CHAR_LENGTH(`clients1`.`name`), CONCAT_WS('',   `clients1`.`name`), '') /* Client */" => "client",
		"`companies`.`industry`" => "industry",
		"`companies`.`company_number`" => "company_number",
		"`companies`.`country_hq`" => "country_hq",
		"`companies`.`country_operations`" => "country_operations",
		"`companies`.`num_employees`" => "num_employees",
		"`companies`.`company_type`" => "company_type",
		"IF(    CHAR_LENGTH(`sic1`.`code`) || CHAR_LENGTH(`sic1`.`activity`), CONCAT_WS('',   `sic1`.`code`, ' - ', `sic1`.`activity`), '') /* SIC code */" => "sic_code",
		"if(`companies`.`created`,date_format(`companies`.`created`,'%d/%m/%Y'),'')" => "created",
		"`companies`.`created_by`" => "created_by"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`companies`.`company_id`',
		2 => 2,
		3 => 3,
		4 => 4,
		5 => '`companies`.`company_number`',
		6 => 6,
		7 => 7,
		8 => '`companies`.`num_employees`',
		9 => 9,
		10 => 10,
		11 => '`companies`.`created`',
		12 => 12
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV=array(   
		"`companies`.`company_id`" => "company_id",
		"`companies`.`name`" => "name",
		"IF(    CHAR_LENGTH(`clients1`.`name`), CONCAT_WS('',   `clients1`.`name`), '') /* Client */" => "client",
		"`companies`.`industry`" => "industry",
		"`companies`.`company_number`" => "company_number",
		"`companies`.`country_hq`" => "country_hq",
		"`companies`.`country_operations`" => "country_operations",
		"`companies`.`num_employees`" => "num_employees",
		"`companies`.`company_type`" => "company_type",
		"IF(    CHAR_LENGTH(`sic1`.`code`) || CHAR_LENGTH(`sic1`.`activity`), CONCAT_WS('',   `sic1`.`code`, ' - ', `sic1`.`activity`), '') /* SIC code */" => "sic_code",
		"if(`companies`.`created`,date_format(`companies`.`created`,'%d/%m/%Y'),'')" => "created",
		"`companies`.`created_by`" => "created_by"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters=array(   
		"`companies`.`company_id`" => "ID",
		"`companies`.`name`" => "Name",
		"IF(    CHAR_LENGTH(`clients1`.`name`), CONCAT_WS('',   `clients1`.`name`), '') /* Client */" => "Client",
		"`companies`.`industry`" => "Industry",
		"`companies`.`company_number`" => "Company number",
		"`companies`.`country_hq`" => "Country based",
		"`companies`.`country_operations`" => "Country of operations",
		"`companies`.`num_employees`" => "Number of employees",
		"`companies`.`company_type`" => "Company type",
		"IF(    CHAR_LENGTH(`sic1`.`code`) || CHAR_LENGTH(`sic1`.`activity`), CONCAT_WS('',   `sic1`.`code`, ' - ', `sic1`.`activity`), '') /* SIC code */" => "SIC code",
		"`companies`.`created`" => "Date created",
		"`companies`.`created_by`" => "Created by"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS=array(   
		"`companies`.`company_id`" => "company_id",
		"`companies`.`name`" => "name",
		"IF(    CHAR_LENGTH(`clients1`.`name`), CONCAT_WS('',   `clients1`.`name`), '') /* Client */" => "client",
		"`companies`.`industry`" => "industry",
		"`companies`.`company_number`" => "company_number",
		"`companies`.`country_hq`" => "country_hq",
		"`companies`.`country_operations`" => "country_operations",
		"`companies`.`num_employees`" => "num_employees",
		"`companies`.`company_type`" => "company_type",
		"IF(    CHAR_LENGTH(`sic1`.`code`) || CHAR_LENGTH(`sic1`.`activity`), CONCAT_WS('',   `sic1`.`code`, ' - ', `sic1`.`activity`), '') /* SIC code */" => "sic_code",
		"if(`companies`.`created`,date_format(`companies`.`created`,'%d/%m/%Y'),'')" => "created",
		"`companies`.`created_by`" => "created_by"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array(  'client' => 'Client', 'sic_code' => 'SIC code');

	$x->QueryFrom="`companies` LEFT JOIN `clients` as clients1 ON `clients1`.`client_id`=`companies`.`client` LEFT JOIN `sic` as sic1 ON `sic1`.`sic_id`=`companies`.`sic_code` ";
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
	$x->RecordsPerPage = 50;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "companies_view.php";
	$x->RedirectAfterInsert = "companies_view.php?SelectedID=#ID#";
	$x->TableTitle = "Companies";
	$x->TableIcon = "resources/table_icons/chair.png";
	$x->PrimaryKey = "`companies`.`company_id`";

	$x->ColWidth   = array(  150, 150, 20, 30);
	$x->ColCaption = array("Name", "Client", "Date created", "Created by");
	$x->ColFieldName = array('name', 'client', 'created', 'created_by');
	$x->ColNumber  = array(2, 3, 11, 12);

	$x->Template = 'templates/companies_templateTV.html';
	$x->SelectedTemplate = 'templates/companies_templateTVS.html';
	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `companies`.`company_id`=membership_userrecords.pkValue and membership_userrecords.tableName='companies' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `companies`.`company_id`=membership_userrecords.pkValue and membership_userrecords.tableName='companies' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`companies`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: companies_init
	$render=TRUE;
	if(function_exists('companies_init')){
		$args=array();
		$render=companies_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: companies_header
	$headerCode='';
	if(function_exists('companies_header')){
		$args=array();
		$headerCode=companies_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: companies_footer
	$footerCode='';
	if(function_exists('companies_footer')){
		$args=array();
		$footerCode=companies_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>