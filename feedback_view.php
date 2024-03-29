<?php
// This script and data application were generated by AppGini 5.75
// Download AppGini for free from https://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/feedback.php");
	include("$currDir/feedback_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('feedback');
	if(!$perm[0]){
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "feedback";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = array(   
		"`feedback`.`id`" => "id",
		"`feedback`.`titre`" => "titre",
		"`feedback`.`fonctionnalite`" => "fonctionnalite",
		"`feedback`.`description`" => "description",
		"`feedback`.`plus`" => "plus",
		"`feedback`.`moins`" => "moins",
		"`feedback`.`kanban`" => "kanban",
		"`feedback`.`commentaires`" => "commentaires"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`feedback`.`id`',
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5,
		6 => 6,
		7 => 7,
		8 => 8
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = array(   
		"`feedback`.`id`" => "id",
		"`feedback`.`titre`" => "titre",
		"`feedback`.`fonctionnalite`" => "fonctionnalite",
		"`feedback`.`description`" => "description",
		"`feedback`.`plus`" => "plus",
		"`feedback`.`moins`" => "moins",
		"`feedback`.`kanban`" => "kanban",
		"`feedback`.`commentaires`" => "commentaires"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters = array(   
		"`feedback`.`id`" => "ID",
		"`feedback`.`titre`" => "Titre",
		"`feedback`.`fonctionnalite`" => "Fonctionnalite",
		"`feedback`.`description`" => "Description",
		"`feedback`.`plus`" => "Que faut-il Ajouter",
		"`feedback`.`moins`" => "Que faut-il retirer ?",
		"`feedback`.`kanban`" => "Kanban",
		"`feedback`.`commentaires`" => "Commentaires"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS = array(   
		"`feedback`.`id`" => "id",
		"`feedback`.`titre`" => "titre",
		"`feedback`.`fonctionnalite`" => "fonctionnalite",
		"`feedback`.`description`" => "description",
		"`feedback`.`plus`" => "plus",
		"`feedback`.`moins`" => "moins",
		"`feedback`.`kanban`" => "kanban",
		"`feedback`.`commentaires`" => "commentaires"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array();

	$x->QueryFrom = "`feedback` ";
	$x->QueryWhere = '';
	$x->QueryOrder = '';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm[2]==0 ? 1 : 0);
	$x->AllowDelete = $perm[4];
	$x->AllowMassDelete = false;
	$x->AllowInsert = $perm[1];
	$x->AllowUpdate = $perm[3];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 0;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowPrintingDV = 1;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "feedback_view.php";
	$x->RedirectAfterInsert = "feedback_view.php?SelectedID=#ID#";
	$x->TableTitle = "Feedback Dev";
	$x->TableIcon = "resources/table_icons/bulb.png";
	$x->PrimaryKey = "`feedback`.`id`";

	$x->ColWidth   = array(  150, 150, 150, 150, 150, 150, 150);
	$x->ColCaption = array("Titre", "Fonctionnalite", "Description", "Que faut-il Ajouter", "Que faut-il retirer ?", "Kanban", "Commentaires");
	$x->ColFieldName = array('titre', 'fonctionnalite', 'description', 'plus', 'moins', 'kanban', 'commentaires');
	$x->ColNumber  = array(2, 3, 4, 5, 6, 7, 8);

	// template paths below are based on the app main directory
	$x->Template = 'templates/feedback_templateTV.html';
	$x->SelectedTemplate = 'templates/feedback_templateTVS.html';
	$x->TemplateDV = 'templates/feedback_templateDV.html';
	$x->TemplateDVP = 'templates/feedback_templateDVP.html';

	$x->ShowTableHeader = 0;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `feedback`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='feedback' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `feedback`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='feedback' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`feedback`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: feedback_init
	$render=TRUE;
	if(function_exists('feedback_init')){
		$args=array();
		$render=feedback_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: feedback_header
	$headerCode='';
	if(function_exists('feedback_header')){
		$args=array();
		$headerCode=feedback_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: feedback_footer
	$footerCode='';
	if(function_exists('feedback_footer')){
		$args=array();
		$footerCode=feedback_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>