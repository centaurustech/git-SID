<?php
	// For help on using hooks, please refer to http://bigprof.com/appgini/help/working-with-generated-web-database-application/hooks

	function tax_entry_init(&$options, $memberInfo, &$args){

		return TRUE;
	}

	function tax_entry_header($contentType, $memberInfo, &$args){
		$header='';

		switch($contentType){
			case 'tableview':
				$header='';
				break;

			case 'detailview':
				$header='';
				break;

			case 'tableview+detailview':
				$header='';
				break;

			case 'print-tableview':
				$header='';
				break;

			case 'print-detailview':
				$header='';
				break;

			case 'filters':
				$header='';
				break;
		}

		return $header;
	}

	function tax_entry_footer($contentType, $memberInfo, &$args){
		$footer='';

		switch($contentType){
			case 'tableview':
				$footer='';
				break;

			case 'detailview':
				$footer='';
				break;

			case 'tableview+detailview':
				$footer='';
				break;

			case 'print-tableview':
				$footer='';
				break;

			case 'print-detailview':
				$footer='';
				break;

			case 'filters':
				$footer='';
				break;
		}

		return $footer;
	}

	function tax_entry_before_insert(&$data, $memberInfo, &$args){

		$data['sum'] = $data['payroll_tax'] + $data['ni_contribution'] + $data['$business_rates'] + $data['corporation_tax'] + $data['other_tax'];
		$data['ratio'] = $data['sum'] / $data['turnover'];

		return TRUE;
	}

	function tax_entry_after_insert($data, $memberInfo, &$args){

		return TRUE;
	}

	function tax_entry_before_update(&$data, $memberInfo, &$args){

		$data['sum'] = $data['payroll_tax'] + $data['ni_contribution'] + $data['$business_rates'] + $data['corporation_tax'] + $data['other_tax'];
		$data['ratio'] = $data['sum'] / $data['turnover'];		

		return TRUE;
	}

	function tax_entry_after_update($data, $memberInfo, &$args){

		return TRUE;
	}

	function tax_entry_before_delete($selectedID, &$skipChecks, $memberInfo, &$args){

		return TRUE;
	}

	function tax_entry_after_delete($selectedID, $memberInfo, &$args){

	}

	function tax_entry_dv($selectedID, $memberInfo, &$html, &$args){

	}

	function tax_entry_csv($query, $memberInfo, $args){

		return $query;
	}