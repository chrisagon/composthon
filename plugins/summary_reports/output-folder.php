<?php include(dirname(__FILE__) . '/header.php'); ?>

<?php
	$axp_md5 = $_REQUEST['axp'];
	$projectFile = '';
	$xmlFile = $summary_reports->get_xml_file($axp_md5 , $projectFile);
?>

<div class="page-header row">
	<h1><img src="summary_reports-logo-lg.png" style="height: 1em;"> Summary Reports</h1>
	<h1>
		<a href="index.php">Projects</a> &gt; 
		<a href="project.php?axp=<?php echo urlencode($axp_md5); ?>"><?php echo substr($projectFile, 0, -4); ?></a> &gt;
		Output folder
	</h1>
</div>

<?php
	echo $summary_reports->show_select_output_folder(array(
		'next_page' => 'generate.php?axp=' . urlencode($_REQUEST['axp']),
		'extra_options' => array(
			/* 'option1' => 'Option 1 label', */
			/* 'option2' => 'Option 2 label' */
		)
	));
?>

<?php include(dirname(__FILE__) . '/footer.php'); ?>
