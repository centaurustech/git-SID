<?php
	define('maxSortBy', 4);
	define('empty_lookup_value', '{empty_value}');

	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	if(function_exists('set_magic_quotes_runtime')) @set_magic_quotes_runtime(0);
	ob_start();

	$currDir=dirname(__FILE__);
	$setupStyle="border: solid 1px red; background-color: #FFFFE0; color: red; font-size: 16px; font-family: arial; font-weight: bold; padding: 10px; width:400px; text-align: left;";

	include("$currDir/admin/incFunctions.php");
	// include global hook functions
	@include("$currDir/hooks/__global.php");

	// check sessions config
	$noPathCheck=True;
	$arrPath=explode(';', ini_get('session.save_path'));
	$save_path=$arrPath[count($arrPath)-1];
	if(!$noPathCheck && !is_dir($save_path)){
		?>
		<center>
		<div style="<?php echo $setupStyle ?>">
			Your site is not configured to support sessions correctly. Please edit your php.ini file and change the value of <i>session.save_path</i> to a valid path.
			</div>
			</center>
		<?php
		exit;
	}
	if(session_id()){ session_write_close(); }
	$configured_save_handler = @ini_get('session.save_handler');
	if($configured_save_handler != 'memcache' && $configured_save_handler != 'memcached')
		@ini_set('session.save_handler', 'files');
	@ini_set('session.serialize_handler', 'php');
	@ini_set('session.use_cookies', '1');
	@ini_set('session.use_only_cookies', '1');
	@session_cache_limiter('private, must-revalidate');
	@session_name('SID');
	session_start();
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-Type: text/html; charset=UTF-8');

	// check if membership system exists
	setupMembership();

	// silently apply db changes, if any
	@include_once("$currDir/updateDB.php");

	// do we have a login request?
	logInMember();

	// convert expanded sorting variables, if provided, to SortField and SortDirection
	$postedOrderBy = array();
	for($i = 0; $i < maxSortBy; $i++){
		if(isset($_POST["OrderByField$i"])){
			$sd = ($_POST["OrderDir$i"] == 'desc' ? 'desc' : 'asc');
			if($sfi = intval($_POST["OrderByField$i"])){
				$postedOrderBy[] = array($sfi => $sd);
			}
		}
	}
	if(count($postedOrderBy)){
		$_POST['SortField'] = '';
		$_POST['SortDirection'] = '';
		foreach($postedOrderBy as $obi){
			$sfi = ''; $sd = '';
			foreach($obi as $sfi => $sd);
			$_POST['SortField'] .= "$sfi $sd,";
		}
		$_POST['SortField'] = substr($_POST['SortField'], 0, -2 - strlen($sd));
		$_POST['SortDirection'] = $sd;
	}elseif($_POST['apply_sorting']){
		/* no sorting and came from filters page .. so clear sorting */
		$_POST['SortField'] = $_POST['SortDirection'] = '';
	}

	#########################################################
	/*
	~~~~~~ LIST OF FUNCTIONS ~~~~~~
		getTableList() -- returns an associative array (tableName=>tableData, tableData is array(tableCaption, tableDescription, tableIcon)) of tables accessible by current user
		getLoggedMemberID() -- returns memberID of logged member. If no login, returns anonymous memberID
		getLoggedGroupID() -- returns groupID of logged member, or anonymous groupID
		logOutMember() -- destroys session and logs member out.
		logInMember() -- checks POST login. If not valid, redirects to index.php, else returns TRUE
		getTablePermissions($tn) -- returns an array of permissions allowed for logged member to given table (allowAccess, allowInsert, allowView, allowEdit, allowDelete) -- allowAccess is set to true if any access level is allowed
		htmlUserBar() -- returns html code for displaying user login status to be used on top of pages.
		showNotifications($msg, $class) -- returns html code for displaying a notification. If no parameters provided, processes the GET request for possible notifications.
		parseMySQLDate(a, b) -- returns a if valid mysql date, or b if valid mysql date, or today if b is true, or empty if b is false.
		parseCode(code) -- calculates and returns special values to be inserted in automatic fields.
		addFilter(i, filterAnd, filterField, filterOperator, filterValue) -- enforce a filter over data
		clearFilters() -- clear all filters
		getMemberInfo() -- returns an array containing the currently signed-in member's info
		loadView($view, $data) -- passes $data to templates/{$view}.php and returns the output
		loadTable($table, $data) -- loads table template, passing $data to it
		filterDropdownBy($filterable, $filterers, $parentFilterers, $parentPKField, $parentCaption, $parentTable, &$filterableCombo) -- applies cascading drop-downs for a lookup field, returns js code to be inserted into the page
		br2nl($text) -- replaces all variations of HTML <br> tags with a new line character
		htmlspecialchars_decode($text) -- inverse of htmlspecialchars()
		entitiesToUTF8($text) -- convert unicode entities (e.g. &#1234;) to actual UTF8 characters, requires multibyte string PHP extension
		func_get_args_byref() -- returns an array of arguments passed to a function, by reference
		html_attr($str) -- prepare $str to be placed inside an HTML attribute
		permissions_sql($table, $level) -- returns an array containing the FROM and WHERE additions for applying permissions to an SQL query
		error_message($msg[, $back_url]) -- returns html code for a styled error message .. pass explicit false in second param to suppress back button
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	*/
	#########################################################
	function getTableList(){
		$arrTables=array(   
			'clients'=>array('Clients', '', 'resources/table_icons/account_balances.png'),
			'companies'=>array('Companies', '', 'resources/table_icons/chair.png'),
			'sic'=>array('SIC', '', 'resources/table_icons/barcode.png'),
			'reports'=>array('Reports', '', 'resources/table_icons/application_from_storage.png'),
			'entries'=>array('Entries', '', 'resources/table_icons/attributes_display.png'),
			'outcome_areas'=>array('Outcome areas', '', 'resources/table_icons/bricks.png'),
			'outcomes'=>array('Outcomes', '', 'resources/table_icons/brick.png'),
			'beneficiary_groups'=>array('Beneficiary groups', '', 'resources/table_icons/heart.png'),
			'indicators'=>array('Indicators', '', 'resources/table_icons/book_keeping.png'),
			'tax_entry'=>array('Tax', '', 'table.gif')
			);
		if(is_array($arrTables)){
			foreach($arrTables as $tn=>$tc){
				$arrPerm=getTablePermissions($tn);
				if($arrPerm[0]){
					$arrAccessTables[$tn]=$tc;
				}
			}
		}

		return $arrAccessTables;
	}
	#########################################################
	function getTablePermissions($tn){
		$groupID=getLoggedGroupID();
		$memberID=makeSafe(getLoggedMemberID());
		if(sqlValue("select count(1) from membership_userpermissions where lcase(memberID)='$memberID' and tableName='$tn'")){
			$res=sql("select allowInsert, allowView, allowEdit, allowDelete from membership_userpermissions where lcase(memberID)='$memberID' and tableName='$tn'", $eo);
		}else{
			$res=sql("select allowInsert, allowView, allowEdit, allowDelete from membership_grouppermissions where groupID='$groupID' and tableName='$tn'", $eo);
		}

		if($row=mysql_fetch_row($res)){
			if($row[0] || $row[1] || $row[2] || $row[3]){
				$arrRet[0]=TRUE; /* allowAccess */
			}else{
				$arrRet[0]=FALSE;
			}
			$arrRet[1]=$row[0]; /* allowInsert */
			$arrRet[2]=$row[1]; /* allowView */
			$arrRet[3]=$row[2]; /* allowEdit */
			$arrRet[4]=$row[3]; /* allowDelete */

			return $arrRet;
		}

		return array(FALSE, 0, 0, 0, 0);
	}
	#########################################################
	function getLoggedGroupID(){
		if($_SESSION['memberGroupID']!=''){
			return $_SESSION['memberGroupID'];
		}else{
			setAnonymousAccess();
			return getLoggedGroupID();
		}
	}
	#########################################################
	function getLoggedMemberID(){
		if($_SESSION['memberID']!=''){
			return strtolower($_SESSION['memberID']);
		}else{
			setAnonymousAccess();
			return getLoggedMemberID();
		}
	}
	#########################################################
	function setAnonymousAccess(){
		global $adminConfig;

		$anonGroupID=sqlValue("select groupID from membership_groups where name='".$adminConfig['anonymousGroup']."'");
		$_SESSION['memberGroupID']=($anonGroupID ? $anonGroupID : 0);

		$anonMemberID=sqlValue("select lcase(memberID) from membership_users where lcase(memberID)='".strtolower($adminConfig['anonymousMember'])."' and groupID='$anonGroupID'");
		$_SESSION['memberID']=($anonMemberID ? $anonMemberID : 0);
	}
	#########################################################
	function logInMember(){
		$redir='index.php';
		if($_POST['signIn']!=''){
			if($_POST['username']!='' && $_POST['password']!=''){
				$username=makeSafe(strtolower(trim($_POST['username'])));
				$password=md5(trim($_POST['password']));

				if(sqlValue("select count(1) from membership_users where lcase(memberID)='$username' and passMD5='$password' and isApproved=1 and isBanned=0")==1){
					$_SESSION['memberID']=$username;
					$_SESSION['memberGroupID']=sqlValue("select groupID from membership_users where lcase(memberID)='$username'");
					if($_POST['rememberMe']==1){
						@setcookie('SID_rememberMe', md5($username.$password), time()+86400*30);
					}else{
						@setcookie('SID_rememberMe', '', time()-86400*30);
					}

					// hook: login_ok
					if(function_exists('login_ok')){
						$args=array();
						if(!$redir=login_ok(getMemberInfo(), $args)){
							$redir='index.php';
						}
					}

					redirect($redir);
					exit;
				}
			}

			// hook: login_failed
			if(function_exists('login_failed')){
				$args=array();
				login_failed(array(
					'username' => $_POST['username'],
					'password' => $_POST['password'],
					'IP' => $_SERVER['REMOTE_ADDR']
					), $args);
			}

			redirect("index.php?loginFailed=1");
			exit;
		}elseif((!$_SESSION['memberID'] || $_SESSION['memberID']==$adminConfig['anonymousMember']) && $_COOKIE['SID_rememberMe']!=''){
			$chk=makeSafe($_COOKIE['SID_rememberMe']);
			if($username=sqlValue("select memberID from membership_users where convert(md5(concat(memberID, passMD5)), char)='$chk' and isBanned=0")){
				$_SESSION['memberID']=$username;
				$_SESSION['memberGroupID']=sqlValue("select groupID from membership_users where lcase(memberID)='$username'");
			}
		}
	}
	#########################################################
	function logOutMember(){
		logOutUser();
		redirect("index.php?signIn=1");
	}
	#########################################################
	function htmlUserBar(){
		global $adminConfig, $Translation;

		ob_start();
		$home_page = (basename($_SERVER['PHP_SELF'])=='index.php' ? true : false);

		?>
		<nav class="navbar navbar-default navbar-fixed-top hidden-print" role="navigation">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<!-- ---- application title is obtained from the name besides the yellow database icon in AppGini, use underscores for spaces ---- -->
				<a class="navbar-brand" href="index.php"><i class="glyphicon glyphicon-home"></i> SID</a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<?php if(!$home_page){ ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $Translation['select a table']; ?> <b class="caret"></b></a>
							<ul class="dropdown-menu" role="menu">
								<?php echo NavMenus(); ?>
							</ul>
						</li>
					<?php } ?>
				</ul>

				<?php if(getLoggedAdmin()){ ?>
					<ul class="nav navbar-nav">
						<a href="admin/pageHome.php" class="btn btn-danger navbar-btn visible-sm visible-md visible-lg"><i class="glyphicon glyphicon-wrench"></i> <?php echo $Translation['admin area']; ?></a>
						<a href="admin/pageHome.php" class="visible-xs btn btn-danger navbar-btn btn-lg"><i class="glyphicon glyphicon-wrench"></i> <?php echo $Translation['admin area']; ?></a>
					</ul>
				<?php } ?>

				<?php if(!$_GET['signIn'] && !$_GET['loginFailed']){ ?>
					<?php if(getLoggedMemberID() == $adminConfig['anonymousMember']){ ?>
						<a href="index.php?signIn=1" class="btn btn-success navbar-btn navbar-right"><?php echo $Translation['sign in']; ?></a>
						<p class="navbar-text navbar-right">
							<?php echo $Translation['not signed in']; ?>
						</p>
					<?php }else{ ?>
						<a class="btn navbar-btn btn-default navbar-right" href="index.php?signOut=1"><i class="glyphicon glyphicon-log-out"></i> <?php echo $Translation['sign out']; ?></a>
						<p class="navbar-text navbar-right">
							<?php echo $Translation['signed as']; ?> <strong><a href="membership_profile.php" class="navbar-link"><?php echo getLoggedMemberID(); ?></a></strong>
						</p>
					<?php } ?>
				<?php } ?>
			</div>
		</nav>
		<?php

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
	#########################################################
	function showNotifications($msg = '', $class = '', $fadeout = true){
		global $Translation;

		$notify_template_no_fadeout = '<div id="%%ID%%" class="alert alert-dismissable %%CLASS%%" style="display: none;">' .
					'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' .
					'%%MSG%%</div>' .
					'<script> jQuery(function(){ jQuery("#%%ID%%").show("slow"); }); </script>'."\n";
		$notify_template = '<div id="%%ID%%" class="alert %%CLASS%%" style="display: none;">%%MSG%%</div>' .
					'<script>' .
						'jQuery(function(){' .
							'jQuery("#%%ID%%").show("slow", function(){' .
								'setTimeout(function(){ jQuery("#%%ID%%").hide("slow"); }, 4000);' .
							'});' .
						'});' .
					'</script>'."\n";

		if(!$msg){ // if no msg, use url to detect message to display
			if($_REQUEST['record-added-ok'] != ''){
				$msg = $Translation['new record saved'];
				$class = 'alert-success';
			}elseif($_REQUEST['record-added-error'] != ''){
				$msg = $Translation['Couldn\'t save the new record'];
				$class = 'alert-danger';
				$fadeout = false;
			}elseif($_REQUEST['record-updated-ok'] != ''){
				$msg = $Translation['record updated'];
				$class = 'alert-success';
			}elseif($_REQUEST['record-updated-error'] != ''){
				$msg = $Translation['Couldn\'t save changes to the record'];
				$class = 'alert-danger';
				$fadeout = false;
			}elseif($_REQUEST['record-deleted-ok'] != ''){
				$msg = $Translation['The record has been deleted successfully'];
				$class = 'alert-success';
				$fadeout = false;
			}elseif($_REQUEST['record-deleted-error'] != ''){
				$msg = $Translation['Couldn\'t delete this record'];
				$class = 'alert-danger';
				$fadeout = false;
			}else{
				return '';
			}
		}
		$id = 'notification-' . rand();

		$out = ($fadeout ? $notify_template : $notify_template_no_fadeout);
		$out = str_replace('%%ID%%', $id, $out);
		$out = str_replace('%%MSG%%', $msg, $out);
		$out = str_replace('%%CLASS%%', $class, $out);

		return $out;
	}
	#########################################################
	function parseMySQLDate($date, $altDate){
		// is $date valid?
		if(preg_match("/^\d{4}-\d{1,2}-\d{1,2}$/", trim($date))){
			return trim($date);
		}

		if($date != '--' && preg_match("/^\d{4}-\d{1,2}-\d{1,2}$/", trim($altDate))){
			return trim($altDate);
		}

		if($date != '--' && $altDate && intval($altDate)==$altDate){
			return @date('Y-m-d', @time() + ($altDate >= 1 ? $altDate - 1 : $altDate) * 86400);
		}

		return '';
	}
	#########################################################
	function parseCode($code, $isInsert=true, $rawData=false){
		if($isInsert){
			$arrCodes=array(
				'<%%creatorusername%%>' => $_SESSION['memberID'],
				'<%%creatorgroupid%%>' => $_SESSION['memberGroupID'],
				'<%%creatorip%%>' => $_SERVER['REMOTE_ADDR'],
				'<%%creatorgroup%%>' => sqlValue("select name from membership_groups where groupID='{$_SESSION['memberGroupID']}'"),

				'<%%creationdate%%>' => ($rawData ? @date('Y-m-d') : @date('j/n/Y')),
				'<%%creationtime%%>' => ($rawData ? @date('H:i:s') : @date('h:i:s a')),
				'<%%creationdatetime%%>' => ($rawData ? @date('Y-m-d H:i:s') : @date('j/n/Y h:i:s a')),
				'<%%creationtimestamp%%>' => ($rawData ? @date('Y-m-d H:i:s') : @time())
			);
		}else{
			$arrCodes=array(
				'<%%editorusername%%>' => $_SESSION['memberID'],
				'<%%editorgroupid%%>' => $_SESSION['memberGroupID'],
				'<%%editorip%%>' => $_SERVER['REMOTE_ADDR'],
				'<%%editorgroup%%>' => sqlValue("select name from membership_groups where groupID='{$_SESSION['memberGroupID']}'"),

				'<%%editingdate%%>' => ($rawData ? @date('Y-m-d') : @date('j/n/Y')),
				'<%%editingtime%%>' => ($rawData ? @date('H:i:s') : @date('h:i:s a')),
				'<%%editingdatetime%%>' => ($rawData ? @date('Y-m-d H:i:s') : @date('j/n/Y h:i:s a')),
				'<%%editingtimestamp%%>' => ($rawData ? @date('Y-m-d H:i:s') : @time())
			);
		}

		$pc=str_ireplace(array_keys($arrCodes), array_values($arrCodes), $code);

		return $pc;
	}
	#########################################################
	function addFilter($index, $filterAnd, $filterField, $filterOperator, $filterValue){
		// validate input
		if($index<1 || $index>80 || !is_int($index))   return false;
		if($filterAnd!='or')   $filterAnd='and';
		$filterField=intval($filterField);
		if(!in_array($filterOperator, array('<=>', '!=', '>', '>=', '<', '<=', 'like', 'not like', 'isEmpty', 'isNotEmpty')))
			$filterOperator='like';

		if(!$filterField){
			$filterOperator='';
			$filterValue='';
		}

		if($_SERVER['REQUEST_METHOD']=='POST'){
			$_POST['FilterAnd'][$index]=$filterAnd;
			$_POST['FilterField'][$index]=$filterField;
			$_POST['FilterOperator'][$index]=$filterOperator;
			$_POST['FilterValue'][$index]=$filterValue;
		}else{
			$_GET['FilterAnd'][$index]=$filterAnd;
			$_GET['FilterField'][$index]=$filterField;
			$_GET['FilterOperator'][$index]=$filterOperator;
			$_GET['FilterValue'][$index]=$filterValue;
		}

		return true;
	}
	#########################################################
	function clearFilters(){
		for($i=1; $i<=80; $i++){
			addFilter($i, '', 0, '', '');
		}
	}
	#########################################################
	function getMemberInfo($memberID=''){
		global $adminConfig;
		$mi=array();

		if(!$memberID){
			$memberID=getLoggedMemberID();
		}

		if($memberID){
			$res=sql("select * from membership_users where memberID='".addslashes($memberID)."'", $eo);
			if($row=mysql_fetch_assoc($res)){
				$mi['username']=$memberID;
				$mi['groupID']=$row['groupID'];
				$mi['group']=sqlValue("select name from membership_groups where groupID='".$row['groupID']."'");
				$mi['admin']=($adminConfig['adminUsername']==$memberID ? TRUE : FALSE);
				$mi['email']=$row['email'];
				$mi['custom'][0]=$row['custom1'];
				$mi['custom'][1]=$row['custom2'];
				$mi['custom'][2]=$row['custom3'];
				$mi['custom'][3]=$row['custom4'];
				$mi['banned']=($row['isBanned'] ? TRUE : FALSE);
				$mi['approved']=($row['isApproved'] ? TRUE : FALSE);
				$mi['signupDate']=@date('j/n/Y', @strtotime($row['signupDate']));
				$mi['comments']=$row['comments'];
				$mi['IP']=$_SERVER['REMOTE_ADDR'];
			}
		}

		return $mi;
	}
	#########################################################
	if(!function_exists('str_ireplace')){
		function str_ireplace($search, $replace, $subject){
			$ret=$subject;
			if(is_array($search)){
				for($i=0; $i<count($search); $i++){
					$ret=str_ireplace($search[$i], $replace[$i], $ret);
				}
			}else{
				$ret=preg_replace('/'.preg_quote($search, '/').'/i', $replace, $ret);
			}

			return $ret;
		} 
	} 

	#########################################################
	/**
	* Loads a given view from the templates folder, passing the given data to it
	* @param $view the name of a php file (without extension) to be loaded from the 'templates' folder
	* @param $the_data_to_pass_to_the_view (optional) associative array containing the data to pass to the view
	* @return the output of the parsed view as a string
	*/
	function loadView($view, $the_data_to_pass_to_the_view=false){
		global $Translation;

		$view = dirname(__FILE__)."/templates/$view.php";
		if(!is_file($view)) return false;

		if(is_array($the_data_to_pass_to_the_view)){
			foreach($the_data_to_pass_to_the_view as $k => $v)
				$$k = $v;
		}
		unset($the_data_to_pass_to_the_view, $k, $v);

		ob_start();
		@include($view);
		$out=ob_get_contents();
		ob_end_clean();

		return $out;
	}

	#########################################################
	/**
	* Loads a table template from the templates folder, passing the given data to it
	* @param $table_name the name of the table whose template is to be loaded from the 'templates' folder
	* @param $the_data_to_pass_to_the_table associative array containing the data to pass to the table template
	* @return the output of the parsed table template as a string
	*/
	function loadTable($table_name, $the_data_to_pass_to_the_table = array()){
		$dont_load_header = $the_data_to_pass_to_the_table['dont_load_header'];
		$dont_load_footer = $the_data_to_pass_to_the_table['dont_load_footer'];
		
		$header = $table = $footer = '';
		
		if(!$dont_load_header){
			// try to load tablename-header
			if(!($header = loadView("{$table_name}-header", $the_data_to_pass_to_the_table))){
				$header = loadView('table-common-header', $the_data_to_pass_to_the_table);
			}
		}
		
		$table = loadView($table_name, $the_data_to_pass_to_the_table);
		
		if(!$dont_load_footer){
			// try to load tablename-footer
			if(!($footer = loadView("{$table_name}-footer", $the_data_to_pass_to_the_table))){
				$footer = loadView('table-common-footer', $the_data_to_pass_to_the_table);
			}
		}
		
		return "{$header}{$table}{$footer}";
	}

	#########################################################
	function filterDropdownBy($filterable, $filterers, $parentFilterers, $parentPKField, $parentCaption, $parentTable, &$filterableCombo){
		$filterersArray = explode(',', $filterers);
		$parentFilterersArray = explode(',', $parentFilterers);
		$parentFiltererList = '`' . implode('`, `', $parentFilterersArray) . '`';
		$res=sql("SELECT `$parentPKField`, $parentCaption, $parentFiltererList FROM `$parentTable` ORDER BY 2", $eo);
		$filterableData = array();
		while($row=mysql_fetch_row($res)){
			$filterableData[$row[0]] = $row[1];
			$filtererIndex = 0;
			foreach($filterersArray as $filterer){
				$filterableDataByFilterer[$filterer][$row[$filtererIndex + 2]][$row[0]] = $row[1];
				$filtererIndex++;
			}
			$row[0] = addslashes($row[0]);
			$row[1] = addslashes($row[1]);
			$jsonFilterableData .= "\"{$row[0]}\":\"{$row[1]}\",";
		}
		$jsonFilterableData .= '}';
		$jsonFilterableData = '{'.str_replace(',}', '}', $jsonFilterableData);     
		$filterJS = "\nvar {$filterable}_data = $jsonFilterableData;";

		foreach($filterersArray as $filterer){
			if(is_array($filterableDataByFilterer[$filterer])) foreach($filterableDataByFilterer[$filterer] as $filtererItem => $filterableItem){
				$jsonFilterableDataByFilterer[$filterer] .= '"'.addslashes($filtererItem).'":{';
				foreach($filterableItem as $filterableItemID => $filterableItemData){
					$jsonFilterableDataByFilterer[$filterer] .= '"'.addslashes($filterableItemID).'":"'.addslashes($filterableItemData).'",';
				}
				$jsonFilterableDataByFilterer[$filterer] .= '},';
			}
			$jsonFilterableDataByFilterer[$filterer] .= '}';
			$jsonFilterableDataByFilterer[$filterer] = '{'.str_replace(',}', '}', $jsonFilterableDataByFilterer[$filterer]);

			$filterJS.="\n\n// code for filtering {$filterable} by {$filterer}\n";
			$filterJS.="\nvar {$filterable}_data_by_{$filterer} = {$jsonFilterableDataByFilterer[$filterer]}; ";
			$filterJS.="\nvar selected_{$filterable} = \$F('{$filterable}');";
			$filterJS.="\nvar {$filterable}_change_by_{$filterer} = function(){";
			$filterJS.="\n\t$('{$filterable}').options.length=0;";
			$filterJS.="\n\t$('{$filterable}').options[0] = new Option();";
			$filterJS.="\n\tif(\$F('{$filterer}')){";
			$filterJS.="\n\t\tfor({$filterable}_item in {$filterable}_data_by_{$filterer}[\$F('{$filterer}')]){";
			$filterJS.="\n\t\t\t$('{$filterable}').options[$('{$filterable}').options.length] = new Option(";
			$filterJS.="\n\t\t\t\t{$filterable}_data_by_{$filterer}[\$F('{$filterer}')][{$filterable}_item],";
			$filterJS.="\n\t\t\t\t{$filterable}_item,";
			$filterJS.="\n\t\t\t\t({$filterable}_item == selected_{$filterable} ? true : false),";
			$filterJS.="\n\t\t\t\t({$filterable}_item == selected_{$filterable} ? true : false)";
			$filterJS.="\n\t\t\t);";
			$filterJS.="\n\t\t}";
			$filterJS.="\n\t}else{";
			$filterJS.="\n\t\tfor({$filterable}_item in {$filterable}_data){";
			$filterJS.="\n\t\t\t$('{$filterable}').options[$('{$filterable}').options.length] = new Option(";
			$filterJS.="\n\t\t\t\t{$filterable}_data[{$filterable}_item],";
			$filterJS.="\n\t\t\t\t{$filterable}_item,";
			$filterJS.="\n\t\t\t\t({$filterable}_item == selected_{$filterable} ? true : false),";
			$filterJS.="\n\t\t\t\t({$filterable}_item == selected_{$filterable} ? true : false)";
			$filterJS.="\n\t\t\t);";
			$filterJS.="\n\t\t}";
			$filterJS.="\n\t\tif(selected_{$filterable} && selected_{$filterable} == \$F('{$filterable}')){";
			$filterJS.="\n\t\t\tfor({$filterer}_item in {$filterable}_data_by_{$filterer}){";
			$filterJS.="\n\t\t\t\tfor({$filterable}_item in {$filterable}_data_by_{$filterer}[{$filterer}_item]){";
			$filterJS.="\n\t\t\t\t\tif({$filterable}_item == selected_{$filterable}){";
			$filterJS.="\n\t\t\t\t\t\t$('{$filterer}').value = {$filterer}_item;";
			$filterJS.="\n\t\t\t\t\t\tbreak;";
			$filterJS.="\n\t\t\t\t\t}";
			$filterJS.="\n\t\t\t\t}";
			$filterJS.="\n\t\t\t\tif({$filterable}_item == selected_{$filterable}) break;";
			$filterJS.="\n\t\t\t}";
			$filterJS.="\n\t\t}";
			$filterJS.="\n\t}";
			$filterJS.="\n\t$('{$filterable}').highlight();";
			$filterJS.="\n};";
			$filterJS.="\n$('{$filterer}').observe('change', function(){ window.setTimeout({$filterable}_change_by_{$filterer}, 25); });";
			$filterJS.="\n";
		}

		$filterableCombo = new Combo;
		$filterableCombo->ListType = 0;
		$filterableCombo->ListItem = array_slice(array_values($filterableData), 0, 10);
		$filterableCombo->ListData = array_slice(array_keys($filterableData), 0, 10);
		$filterableCombo->SelectName = $filterable;
		$filterableCombo->AllowNull = true;

		return $filterJS;
	}
	#########################################################
	function br2nl($text){
		return  preg_replace('/\<br(\s*)?\/?\>/i', "\n", $text);
	}

	#########################################################
	if(!function_exists('htmlspecialchars_decode')){
		function htmlspecialchars_decode($string, $quote_style = ENT_COMPAT){
			return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
		}
	}

	#########################################################
	function entitiesToUTF8($input){
		return preg_replace_callback('/(&#[0-9]+;)/', '_toUTF8', $input);
	}

	function _toUTF8($m){
		if(function_exists('mb_convert_encoding')){
			return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
		}else{
			return $m[1];
		}
	}

	#########################################################
	function func_get_args_byref() {
		if(!function_exists('debug_backtrace')) return false;
		
		$trace = debug_backtrace();
		return $trace[1]['args'];
	}

	#########################################################
	function html_attr($str) {
		return htmlspecialchars($str, ENT_QUOTES);
	}
	#########################################################

	function permissions_sql($table, $level = 'all'){
		if(!in_array($level, array('user', 'group'))){ $level = 'all'; }
		$perm = getTablePermissions($table);
		$from = '';
		$where = '';
		$pk = getPKFieldName($table);
		
		if($perm[2] == 1 || ($perm[2] > 1 && $level == 'user')){ // view owner only
			$from = 'membership_userrecords';
			$where = "(`$table`.`$pk`=membership_userrecords.pkValue and membership_userrecords.tableName='$table' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."')";
		}elseif($perm[2] == 2 || ($perm[2] > 2 && $level == 'group')){ // view group only
			$from = 'membership_userrecords';
			$where = "(`$table`.`$pk`=membership_userrecords.pkValue and membership_userrecords.tableName='$table' and membership_userrecords.groupID='".getLoggedGroupID()."')";
		}elseif($perm[2] == 3){ // view all
			// no further action
		}elseif($perm[2] == 0){ // view none
			return false;
		}
		
		return array('where' => $where, 'from' => $from, 0 => $where, 1 => $from);
	}
	
	#########################################################
	function error_message($msg, $back_url = ''){
		$curr_dir = dirname(__FILE__);
		global $Translation;

		ob_start();

		include_once($curr_dir . '/header.php');
		echo '<div class="panel panel-danger">';
			echo '<div class="panel-heading"><h3 class="panel-title">' . $Translation['error:'] . '</h3></div>';
			echo '<div class="panel-body"><p class="text-danger">' . $msg . '</p>';
			if($back_url !== false){ // explicitly passing false suppresses the back link completely
				echo '<div class="text-center">';
				if($back_url){
					echo '<a href="' . $back_url . '" class="btn btn-danger btn-lg vspacer-lg"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['< back'] . '</a>';
				}else{
					echo '<a href="#" class="btn btn-danger btn-lg vspacer-lg" onclick="history.go(-1); return false;"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['< back'] . '</a>';
				}
				echo '</div>';
			}
			echo '</div>';
		echo '</div>';
		include_once($curr_dir . '/footer.php');

		$out = ob_get_contents();
		ob_end_clean();

		return $out;
	}
	#########################################################

