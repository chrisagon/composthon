<?php
	include(dirname(__FILE__).'/summary_reports.php');

	$summary_reports = new summary_reports(
		array(
		  'title' => 'Summary Reports',
		  'name' => 'summary_reports', 
		  'logo' => 'summary_reports-logo-lg.png' 
		)
	);
	
	/* grant access to the groups 'Admins' only */
	if (!$summary_reports->is_admin() ){
		echo  'Access denied';
		exit;
	}

	$axp_md5 = makeSafe( $_REQUEST['axp'] );
	$table_name = makeSafe( $_REQUEST['table_name'] );
	$node_index = intval( makeSafe( $_REQUEST['node_index'] ) );
	$table_index = intval( makeSafe($_REQUEST['table_index']) );
	$projectFile = '';
	$path=dirname(dirname(dirname(__FILE__)));

	$xmlFile = $summary_reports->get_xml_file( $axp_md5 , $projectFile );
	$tables = $xmlFile->table;
	
	foreach($tables as $table) {
		
		if($table->name == $table_name ){
			$table_reports_string = $table->plugins->summary_reports->report_details;
		}
	}
	 
	$table_reports_array = json_decode( $table_reports_string );
	unset( $table_reports_array[$node_index] );
	$newnode = [];
	 
	 foreach( $table_reports_array as $index => $value ){
		 array_push( $newnode,$value );
	 }
	 $table_reports_string = json_encode( $newnode ); 

	/* update the node */
	$nodeData=array(
		'projectName' => $projectFile,
		'tableIndex' => $table_index,
		'nodeName' => 'report_details',
		'pluginName' => 'summary_reports',
		'data' => $table_reports_string
	) ;
	 
	$summary_reports->update_project_plugin_node($nodeData);
	 
	$file_name = $path."\hooks\summary-reports-".$table_name.'-'.$table_index.'.php';
	 
	/* delete the file */
	if( file_exists( $file_name ) ){
		unlink( $file_name );	
	} 
	 
	echo json_encode( $newnode );
	 