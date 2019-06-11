<?php
	include(dirname(__FILE__).'/summary_reports.php');
	
	/*
		$_REQUEST includes the following:
		axp: md5 hash of project
		report-title
		table_name: source table (the table containing the report) 
		table-index: index of source table
		group-table
		previous-reports: json-encoded list of other reports for this table
		label: field name used as label field
		label-field-index: index of label field
		first-caption: label of group-by column
		second-caption: label of value column
		how-to-summarize: grouping function
		group-array: names of groups allowed to access report, one per line
		look-up-table: in csae label field is a lookup field, this is its parent table
		look-up-value: parentCaption1 fieldname of look-up-table
		date-field
		date-field-index
		report-header-url
		report-footer-url
		report-id: index of current report
	*/
	
	$summary_reports = new summary_reports(
	array(
		  'title' => 'Summary Reports',
		  'name' => 'summary_reports', 
		  'logo' => 'summary_reports-logo-lg.png' 
	));
	
	/* grant access to the groups 'Admins' only */
	if (!$summary_reports->is_admin() ){
		echo  'Access denied';
		exit;
	}
	
	$axp_md5 = $_REQUEST['axp'];
	$projectFile = '';
	$xmlFile = $summary_reports->get_xml_file($axp_md5, $projectFile);
	
	$node = new stdClass();
	$node->report_hash = $_REQUEST['report-hash'];
	if(!$node->report_hash) $node->report_hash = $summary_reports->random_hash();
	$node->title = strip_tags($_REQUEST['report-title']);
	$node->table = $_REQUEST['table_name'];
	$node->table_index = intval($_REQUEST['table-index']);
	$table_index = intval($_REQUEST['table-index']);
	
	$group_table = $_REQUEST['group-table'];
	if(in_array($group_table, $summary_reports->get_table_names($xmlFile))) {
		$node->parent_table = $group_table;	
	}

	if(isset($_REQUEST['previous-reports'])) {
		$previous_reports = $_REQUEST['previous-reports'];	
	}
	$all_reports = json_decode($previous_reports);
	if($all_reports === null) $all_reports = array();
	
	$table_fields = $summary_reports->get_table_fields($node->table);
	if($node->parent_table) {
		$table_fields = $summary_reports->get_table_fields($node->parent_table);
	}
	$node->label = $table_fields[0]; 
	if(in_array($_REQUEST['label'] , $table_fields)) $node->label = $_REQUEST['label']; 

	$node->caption1 = $_REQUEST['first-caption'];
	$node->caption2 = $_REQUEST['second-caption'];
	$node->group_function = $_REQUEST['how-to-summarize'];
	$node->group_function_field = $_REQUEST['summarized-value'];

	//get it from users
	$node->group_array = array();
	if(isset($_REQUEST['group-array'])) {
		$group_str = $_REQUEST['group-array'];
		$group_str = str_replace(array("\r", "\n"), '%GS%', $group_str);
		$group_array = explode('%GS%', $group_str);
		for($i = 0; $i < count($group_array); $i++) {
			if(strlen($group_array[$i])) $node->group_array[] = $group_array[$i];
		}
	}
	if(isset($_REQUEST['look-up-table'])) {
		$node->look_up_table = $_REQUEST['look-up-table'];
	}
	 
	if(isset($_REQUEST['look-up-value'])) {
		$node->look_up_value = $_REQUEST['look-up-value'];
	}

	if(isset($_REQUEST['label-field-index'])) {
		$node->label_field_index = $_REQUEST['label-field-index'];
	}

	if(isset($_REQUEST['date-field']) && $_REQUEST['date-field'] != ''){
		$node->date_field = $_REQUEST['date-field'];
		if(isset($_REQUEST['date-field-index'])) {	
			$node->date_field_index = $_REQUEST['date-field-index'];
		} 
	}
	
	if(isset($_REQUEST['report-header-url'])) {
		$node->report_header_url = $_REQUEST['report-header-url'];
	}
	
	if(isset($_REQUEST['report-footer-url'])) {
		$node->report_footer_url = $_REQUEST['report-footer-url'];
	}
	
	$node->data_table_section = isset($_REQUEST['data_table_section']) ? 1 : 0;
	$node->barchart_section = isset($_REQUEST['barchart_section']) ? 1 : 0;
	$node->piechart_section = isset($_REQUEST['piechart_section']) ? 1 : 0;
	
	/* some URL Parameters*/
	
	$node->join_statment = '';
	$all_lookup_fields = '';
	$date_separators = array(
		'1' => '-',
		'2' => ' ',
		'3' => '.',
		'4' => '/',
		'5' => ','
	);	
	 
	$date_separator_index = (string)$xmlFile->dateSeparator;
	$node->date_separator = $date_separators[ $date_separator_index ];
	
	$path = array ();
	if(isset($node->parent_table ) ){
		$path = $summary_reports->find_path( $node->table, $node->parent_table );
	}
 
	for($i=0; $i < count($path) - 1; $i++) { 
		$node->join_statment .= ' JOIN ' . $summary_reports->get_join_statment($path[$i], $path[$i + 1]);
	}
 
	$node->join_statment = $path[0] . $node->join_statment;

	if(!empty($_REQUEST['report-id']) || $_REQUEST['report-id'] === '0') {
		// update given report_id with node changes
		$report_id = intval($_REQUEST['report-id']);
		$all_reports[$report_id] = $node;
	} else {
		// save node as a new report
		$all_reports[] = $node;
	}

	$json_nodes = json_encode($all_reports);
	 
	/* update node */
	$summary_reports->update_project_plugin_node(array(
		'projectName' => $projectFile,
		'tableIndex' => $table_index,
		'nodeName' => 'report_details',
		'pluginName' => 'summary_reports',
		'data' => $json_nodes
	));

	echo $json_nodes;
