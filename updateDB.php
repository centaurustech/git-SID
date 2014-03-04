<?php
	// check this file's MD5 to make sure it wasn't called before
	$prevMD5=@implode('', @file(dirname(__FILE__).'/setup.md5'));
	$thisMD5=md5(@implode('', @file("./updateDB.php")));
	if($thisMD5==$prevMD5){
		$setupAlreadyRun=true;
	}else{
		// set up tables
		if(!isset($silent)){
			$silent=true;
		}

		// set up tables
		setupTable('clients', "create table if not exists `clients` (   `client_id` TINYINT not null auto_increment , primary key (`client_id`), `name` VARCHAR(60) not null , unique(`name`), `created` DATE , `created_by` VARCHAR(40) ) CHARSET utf8", $silent);
		setupTable('companies', "create table if not exists `companies` (   `company_id` TINYINT not null auto_increment , primary key (`company_id`), `name` VARCHAR(60) , `client` TINYINT , `website` VARCHAR(40) , `industry` VARCHAR(40) , `company_number` INT , `country_hq` VARCHAR(40) default 'United Kingdom' , `country_operations` VARCHAR(40) default 'United Kingdom' , `num_employees` INT , `company_type` VARCHAR(40) , `sic_code` INT , `created` DATE , `created_by` VARCHAR(40) ) CHARSET utf8", $silent);
		setupIndexes('companies', array('client','sic_code'));
		setupTable('sic', "create table if not exists `sic` (   `sic_id` INT not null auto_increment , primary key (`sic_id`), `code` INT , `activity` VARCHAR(255) ) CHARSET utf8", $silent);
		setupTable('reports', "create table if not exists `reports` (   `report_id` TINYINT not null auto_increment , primary key (`report_id`), `date` DATE , `company` TINYINT , `created` DATE , `created_by` VARCHAR(40) , `average_score` INT ) CHARSET utf8", $silent);
		setupIndexes('reports', array('company'));
		setupTable('entries', "create table if not exists `entries` (   `entry_id` INT not null auto_increment , primary key (`entry_id`), `created` DATE , `created_by` VARCHAR(40) , `report` TINYINT , `outcome_area` INT , `outcome` INT , `indicator` INT , `score` TINYINT , `beneficiary_group` TINYINT , `beneficiary_group_relevance` VARCHAR(40) , `comment` MEDIUMTEXT , `reference` VARCHAR(255) , `reliability` VARCHAR(40) , `intentionality` VARCHAR(40) , `equivalence` VARCHAR(40) ) CHARSET utf8", $silent);
		setupIndexes('entries', array('report','outcome','indicator','beneficiary_group'));
		setupTable('outcome_areas', "create table if not exists `outcome_areas` (   `outcome_area_id` TINYINT not null auto_increment , primary key (`outcome_area_id`), `name` VARCHAR(40) , `description` MEDIUMTEXT ) CHARSET utf8", $silent);
		setupTable('outcomes', "create table if not exists `outcomes` (   `outcome_id` INT not null auto_increment , primary key (`outcome_id`), `outcome_area` TINYINT , `description` MEDIUMTEXT , `strata` VARCHAR(40) ) CHARSET utf8", $silent);
		setupIndexes('outcomes', array('outcome_area'));
		setupTable('beneficiary_groups', "create table if not exists `beneficiary_groups` (   `beneficiary_group_id` TINYINT not null auto_increment , primary key (`beneficiary_group_id`), `name` VARCHAR(100) , `description` MEDIUMTEXT ) CHARSET utf8", $silent);
		setupTable('indicators', "create table if not exists `indicators` (   `indicator_id` INT not null auto_increment , primary key (`indicator_id`), `outcome` INT , `description` LONGTEXT ) CHARSET utf8", $silent);
		setupIndexes('indicators', array('outcome'));


		// save MD5
		if($fp=@fopen(dirname(__FILE__).'/setup.md5', 'w')){
			fwrite($fp, $thisMD5);
			fclose($fp);
		}
	}


	function setupIndexes($tableName, $arrFields){
		if(!is_array($arrFields)){
			return false;
		}

		foreach($arrFields as $fieldName){
			if(!$res=@mysql_query("SHOW COLUMNS FROM `$tableName` like '$fieldName'")){
				continue;
			}
			if(!$row=@mysql_fetch_assoc($res)){
				continue;
			}
			if($row['Key']==''){
				@mysql_query("ALTER TABLE `$tableName` ADD INDEX `$fieldName` (`$fieldName`)");
			}
		}
	}


	function setupTable($tableName, $createSQL='', $silent=true, $arrAlter=''){
		global $Translation;
		ob_start();

		echo '<div style="padding: 5px; border-bottom:solid 1px silver; font-family: verdana, arial; font-size: 10px;">';

		// is there a table rename query?
		if(is_array($arrAlter)){
			$matches=array();
			if(preg_match("/ALTER TABLE `(.*)` RENAME `$tableName`/", $arrAlter[0], $matches)){
				$oldTableName=$matches[1];
			}
		}

		if($res=@mysql_query("select count(1) from `$tableName`")){ // table already exists
			if($row=@mysql_fetch_array($res)){
				echo str_replace("<TableName>", $tableName, str_replace("<NumRecords>", $row[0],$Translation["table exists"]));
				if(is_array($arrAlter)){
					echo '<br />';
					foreach($arrAlter as $alter){
						if($alter!=''){
							echo "$alter ... ";
							if(!@mysql_query($alter)){
								echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
								echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . mysql_error() . '</div>';
							}else{
								echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
							}
						}
					}
				}else{
					echo $Translation["table uptodate"];
				}
			}else{
				echo str_replace("<TableName>", $tableName, $Translation["couldnt count"]);
			}
		}else{ // given tableName doesn't exist

			if($oldTableName!=''){ // if we have a table rename query
				if($ro=@mysql_query("select count(1) from `$oldTableName`")){ // if old table exists, rename it.
					$renameQuery=array_shift($arrAlter); // get and remove rename query

					echo "$renameQuery ... ";
					if(!@mysql_query($renameQuery)){
						echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
						echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . mysql_error() . '</div>';
					}else{
						echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
					}

					if(is_array($arrAlter)) setupTable($tableName, $createSQL, false, $arrAlter); // execute Alter queries on renamed table ...
				}else{ // if old tableName doesn't exist (nor the new one since we're here), then just create the table.
					setupTable($tableName, $createSQL, false); // no Alter queries passed ...
				}
			}else{ // tableName doesn't exist and no rename, so just create the table
				echo str_replace("<TableName>", $tableName, $Translation["creating table"]);
				if(!@mysql_query($createSQL)){
					echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
					echo '<div class="text-danger">' . $Translation['mysql said'] . mysql_error() . '</div>';
				}else{
					echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
				}
			}
		}

		echo "</div>";

		$out=ob_get_contents();
		ob_end_clean();
		if(!$silent){
			echo $out;
		}
	}
?>