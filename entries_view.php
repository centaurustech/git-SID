<?php
// This script and data application were generated by AppGini 5.23
// Download AppGini for free from http://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/entries.php");
	include("$currDir/entries_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('entries');
	if(!$perm[0]){
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "entries";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV=array(   
		"`entries`.`entry_id`" => "entry_id",
		"IF(    CHAR_LENGTH(`companies1`.`name`) || CHAR_LENGTH(`clients1`.`name`) || CHAR_LENGTH(`reports1`.`date`), CONCAT_WS('',   `companies1`.`name`, ' - ', `clients1`.`name`, ' - ', `reports1`.`date`), '') /* Report */" => "report",
		"IF(    CHAR_LENGTH(`outcome_areas1`.`name`), CONCAT_WS('',   `outcome_areas1`.`name`), '') /* Outcome area */" => "outcome_area",
		"IF(    CHAR_LENGTH(`outcomes1`.`description`), CONCAT_WS('',   `outcomes1`.`description`), '') /* Outcome */" => "outcome",
		"IF(    CHAR_LENGTH(`beneficiary_groups1`.`name`), CONCAT_WS('',   `beneficiary_groups1`.`name`), '') /* Relevant beneficiary group */" => "beneficiary_group",
		"`entries`.`beneficiary_group_relevance`" => "beneficiary_group_relevance",
		"if(`entries`.`created`,date_format(`entries`.`created`,'%d/%m/%Y'),'')" => "created",
		"`entries`.`created_by`" => "created_by",
		"`entries`.`comment`" => "comment",
		"`entries`.`reference`" => "reference",
		"`entries`.`reliability`" => "reliability",
		"`entries`.`intentionality`" => "intentionality",
		"`entries`.`equivalence`" => "equivalence",
		"`entries`.`score`" => "score"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`entries`.`entry_id`',
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5,
		6 => 6,
		7 => '`entries`.`created`',
		8 => 8,
		9 => 9,
		10 => 10,
		11 => 11,
		12 => 12,
		13 => 13,
		14 => '`entries`.`score`'
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV=array(   
		"`entries`.`entry_id`" => "entry_id",
		"IF(    CHAR_LENGTH(`companies1`.`name`) || CHAR_LENGTH(`clients1`.`name`) || CHAR_LENGTH(`reports1`.`date`), CONCAT_WS('',   `companies1`.`name`, ' - ', `clients1`.`name`, ' - ', `reports1`.`date`), '') /* Report */" => "report",
		"IF(    CHAR_LENGTH(`outcome_areas1`.`name`), CONCAT_WS('',   `outcome_areas1`.`name`), '') /* Outcome area */" => "outcome_area",
		"IF(    CHAR_LENGTH(`outcomes1`.`description`), CONCAT_WS('',   `outcomes1`.`description`), '') /* Outcome */" => "outcome",
		"IF(    CHAR_LENGTH(`beneficiary_groups1`.`name`), CONCAT_WS('',   `beneficiary_groups1`.`name`), '') /* Relevant beneficiary group */" => "beneficiary_group",
		"`entries`.`beneficiary_group_relevance`" => "beneficiary_group_relevance",
		"if(`entries`.`created`,date_format(`entries`.`created`,'%d/%m/%Y'),'')" => "created",
		"`entries`.`created_by`" => "created_by",
		"`entries`.`comment`" => "comment",
		"`entries`.`reference`" => "reference",
		"`entries`.`reliability`" => "reliability",
		"`entries`.`intentionality`" => "intentionality",
		"`entries`.`equivalence`" => "equivalence",
		"`entries`.`score`" => "score"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters=array(   
		"`entries`.`entry_id`" => "ID",
		"IF(    CHAR_LENGTH(`companies1`.`name`) || CHAR_LENGTH(`clients1`.`name`) || CHAR_LENGTH(`reports1`.`date`), CONCAT_WS('',   `companies1`.`name`, ' - ', `clients1`.`name`, ' - ', `reports1`.`date`), '') /* Report */" => "Report",
		"IF(    CHAR_LENGTH(`outcome_areas1`.`name`), CONCAT_WS('',   `outcome_areas1`.`name`), '') /* Outcome area */" => "Outcome area",
		"IF(    CHAR_LENGTH(`outcomes1`.`description`), CONCAT_WS('',   `outcomes1`.`description`), '') /* Outcome */" => "Outcome",
		"IF(    CHAR_LENGTH(`beneficiary_groups1`.`name`), CONCAT_WS('',   `beneficiary_groups1`.`name`), '') /* Relevant beneficiary group */" => "Relevant beneficiary group",
		"`entries`.`beneficiary_group_relevance`" => "Beneficiary group relevance",
		"`entries`.`created`" => "Created",
		"`entries`.`created_by`" => "Created by",
		"`entries`.`comment`" => "Comment",
		"`entries`.`reference`" => "Reference",
		"`entries`.`reliability`" => "Reliability",
		"`entries`.`intentionality`" => "Intentionality",
		"`entries`.`equivalence`" => "Equivalence",
		"`entries`.`score`" => "Score"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS=array(   
		"`entries`.`entry_id`" => "entry_id",
		"IF(    CHAR_LENGTH(`companies1`.`name`) || CHAR_LENGTH(`clients1`.`name`) || CHAR_LENGTH(`reports1`.`date`), CONCAT_WS('',   `companies1`.`name`, ' - ', `clients1`.`name`, ' - ', `reports1`.`date`), '') /* Report */" => "report",
		"IF(    CHAR_LENGTH(`outcome_areas1`.`name`), CONCAT_WS('',   `outcome_areas1`.`name`), '') /* Outcome area */" => "outcome_area",
		"IF(    CHAR_LENGTH(`outcomes1`.`description`), CONCAT_WS('',   `outcomes1`.`description`), '') /* Outcome */" => "outcome",
		"IF(    CHAR_LENGTH(`beneficiary_groups1`.`name`), CONCAT_WS('',   `beneficiary_groups1`.`name`), '') /* Relevant beneficiary group */" => "beneficiary_group",
		"`entries`.`beneficiary_group_relevance`" => "beneficiary_group_relevance",
		"if(`entries`.`created`,date_format(`entries`.`created`,'%d/%m/%Y'),'')" => "created",
		"`entries`.`created_by`" => "created_by",
		"`entries`.`comment`" => "comment",
		"`entries`.`reference`" => "reference",
		"`entries`.`reliability`" => "reliability",
		"`entries`.`intentionality`" => "intentionality",
		"`entries`.`equivalence`" => "equivalence",
		"`entries`.`score`" => "score"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array(  'report' => 'Report', 'outcome' => 'Outcome', 'beneficiary_group' => 'Relevant beneficiary group');

	$x->QueryFrom="`entries` LEFT JOIN `reports` as reports1 ON `reports1`.`report_id`=`entries`.`report` LEFT JOIN `companies` as companies1 ON `companies1`.`company_id`=`reports1`.`company` LEFT JOIN `clients` as clients1 ON `clients1`.`client_id`=`companies1`.`client` LEFT JOIN `outcomes` as outcomes1 ON `outcomes1`.`outcome_id`=`entries`.`outcome` LEFT JOIN `beneficiary_groups` as beneficiary_groups1 ON `beneficiary_groups1`.`beneficiary_group_id`=`entries`.`beneficiary_group` LEFT JOIN `outcome_areas` as outcome_areas1 ON `outcome_areas1`.`outcome_area_id`=`outcomes1`.`outcome_area` ";
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
	$x->ScriptFileName = "entries_view.php";
	$x->RedirectAfterInsert = "entries_view.php?SelectedID=#ID#";
	$x->TableTitle = "Entries";
	$x->TableIcon = "resources/table_icons/attributes_display.png";
	$x->PrimaryKey = "`entries`.`entry_id`";

	$x->ColWidth   = array(  20, 150, 150, 80, 20, 30, 150);
	$x->ColCaption = array("ID", "Report", "Outcome area", "Outcome", "Created", "Created by", "Score");
	$x->ColFieldName = array('entry_id', 'report', 'outcome_area', 'outcome', 'created', 'created_by', 'score');
	$x->ColNumber  = array(1, 2, 3, 4, 7, 8, 14);

	$x->Template = 'templates/entries_templateTV.html';
	$x->SelectedTemplate = 'templates/entries_templateTVS.html';
	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `entries`.`entry_id`=membership_userrecords.pkValue and membership_userrecords.tableName='entries' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `entries`.`entry_id`=membership_userrecords.pkValue and membership_userrecords.tableName='entries' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`entries`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: entries_init
	$render=TRUE;
	if(function_exists('entries_init')){
		$args=array();
		$render=entries_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: entries_header
	$headerCode='';
	if(function_exists('entries_header')){
		$args=array();
		$headerCode=entries_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: entries_footer
	$footerCode='';
	if(function_exists('entries_footer')){
		$args=array();
		$footerCode=entries_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>