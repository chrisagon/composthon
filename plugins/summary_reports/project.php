<?php
	include(dirname(__FILE__) . '/header.php');

	// validate project name
	if (!isset($_REQUEST['axp']) || !preg_match('/^[a-f0-9]{32}$/i', $_REQUEST['axp'])) {
		echo '<br>' . $summary_reports->error_message('Project file not found.');
		exit;
	}
	
	$axp_md5 = $_REQUEST['axp'];
	$projectFile = '';
	$xmlFile = $summary_reports->get_xml_file($axp_md5, $projectFile);
//-----------------------------------------------------------------------------------------
?>

<script>
	if(window.AppGini === undefined) window.AppGini = {};
	
	AppGini.project = <?php echo json_encode($xmlFile); ?>;
	AppGini.axp_md5 = <?php echo json_encode($axp_md5); ?>;
	AppGini.prependPath = <?php echo json_encode(PREPEND_PATH); ?>;
</script>
<script src="project.js"></script>

<div class="page-header row">
	<h1><img src="summary_reports-logo-lg.png" style="height: 1em;"> Summary Reports for AppGini</h1>
	<h1>
		<a href="./index.php">Projects</a> &gt; <?php echo substr($projectFile, 0, -4); ?>
		<a href="output-folder.php?axp=<?php echo $axp_md5; ?>" class="pull-right btn btn-success btn-lg col-md-3 col-xs-12">Specify output folder <span class="glyphicon glyphicon-chevron-right"></span></a>
		<div class="clearfix"></div>
	</h1>
</div>

<div class="row">
	<div class="col-md-4"> 

	<?php 
		echo $summary_reports->show_tables(array(
			'axp' => $xmlFile,
			'click_handler' => 'AppGini.listTableReports',
			'select_first_table' => true
		))	;
		$tables = $xmlFile->table;
	?>
	</div>

	<div class="col-md-8">
		<button type="button" class="btn btn-success" id="add-report" data-toggle="modal" data-target="#report-modal"><i class="glyphicon glyphicon-plus"></i> Add Report</button>

		<div class="btn-group pull-right">
			<button type="button" class="btn btn-default" id="compact-reports-list"><i class="glyphicon glyphicon-list"></i></button>
			<button type="button" class="btn btn-default" id="detailed-reports-list"><i class="glyphicon glyphicon-th-list"></i></button>
		</div>
		<div class="clearfix"></div>
		
		<div id="table-reports" class="table-reponsive vspacer-lg"></div>
	</div>
</div>

<!-- Modal -->
<?php
	$modal_label_classes = 'col-xs-offset-1 col-xs-10 col-sm-3 col-sm-offset-1';
	$modal_input_classes = 'col-xs-offset-1 col-xs-10 col-sm-7 col-sm-offset-0';
	$separator = '<div class="row"><div class="col-xs-offset-1 col-xs-10"><hr></div></div>';
?>
<div id="report-modal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button> 
				<h4 class="modal-title" id="modal-title"></h4>
				<!-- <button type="button" class="btn btn-success" id="save-report" >Save</button> -->
			</div>
			<div class="modal-body">
				<div class="carousel slide" id="report-editor-carousel">
					<div class="carousel-inner">
						<div class="item active">
							<form id="report-editor-form" class="form-horizontal">

								<input type="hidden" id="table-index" name="table-index" value="">
								<input type="hidden" id="report-id" name="report-id" value="">
								<input type="hidden" id="report-hash" name="report-hash" value="">
								<input type="hidden" id="previous-reports" name="previous-reports" value="">
								<input type="hidden" id="first-caption" name="first-caption" value="">
								<input type="hidden" id="second-caption" name="second-caption" value="">
								<input type="hidden" id="look-up-table" name="look-up-table" value="">
								<input type="hidden" id="look-up-value" name="look-up-value" value="">
								<input type="hidden" id="label-field-index" name="label-field-index" value="">
								<input type="hidden" id="date-field-index" name="date-field-index" value="">
					  
								<div class="row">
									<div class="col-xs-5" id="report-sections"> <!-- display section of report edit form -->
										<div class="h4 text-bold">Report sections</div>
										<div class="paper-mocker">
											<div class="form-group">
												<label for="report-header-url" class="control-label" title="Enter the URL of an image to use as the report header.">Report header image URL <i class="glyphicon glyphicon-question-sign"></i></label>
												<input type="text" class="form-control" id="report-header-url" name="report-header-url">
											</div>
											<div class="form-group">
												<label for="report-title" class="control-label">Report Title</label>
												<input maxlength="40" type="text" class="form-control" name="report-title" id="report-title" value="" required>
												<div class="text-danger hidden validation-error" id="title-validation">Report title required</div>
											</div>
											<div class="checkbox">
												<label>
													<input type="checkbox" value="1" id="data-table-section" name="data_table_section">
													Data table<br>
													<img src="images/summary-reports-data-table-section.png" class="img-responsive">
												</label>
											</div>
											<div class="checkbox">
												<label>
													<input type="checkbox" value="1" id="bar-chart-section" name="barchart_section">
													Bar chart<br>
													<img src="images/summary-reports-bar-chart-section.png" class="img-responsive">
												</label>
											</div>
											<div class="checkbox">
												<label>
													<input type="checkbox" value="1" id="pie-chart-section" name="piechart_section">
													Pie charts<br>
													<img src="images/summary-reports-pie-chart-section.png" class="img-responsive">
												</label>
											</div>
											<div class="form-group">
												<label for="report-foter-url" class="control-label" title="Enter the URL of an image to use as the report footer.">Report footer image URL <i class="glyphicon glyphicon-question-sign"></i></label>
												<input type="text" class="form-control" id="report-footer-url" name="report-footer-url">
												<span class="help-block text-italic">This is an experimental feature and not yet supported by browsers.</span>
											</div>
										</div>
									</div> <!-- end of display section of report edit form -->

									<div class="col-xs-7" id="report-data"> <!-- data section of report edit form -->
										<div class="h4 text-bold" style="margin-left: 10%;">
											Report data
											<button type="button" class="btn btn-info help-launcher" id=""><i class="glyphicon glyphicon-info-sign"></i> Help!</button>
										</div>

										<div class="form-group">
											<div class="<?php echo $modal_label_classes;?>"></div>
											<div class="<?php echo $modal_input_classes; ?> checkbox">
												<label>
													<input type="checkbox" value="0" id="single-table">
													Group data by a field from another table
												</label>
											</div>
										</div>
										<div class="form-group hidden">
											<label for="group-table" id="group-table-label" class="control-label <?php echo $modal_label_classes; ?>">Group Table</label>
											<div class="<?php echo $modal_input_classes; ?>">
												<select class="form-control" id="group-table" name="group-table"></select>
											</div>
										</div>
										
										<div class="form-group" >
											<label for="label" class="control-label <?php echo $modal_label_classes; ?>">
												Label Field
											</label>
											<div class="<?php echo $modal_input_classes; ?>">
												<select class="form-control" id="label" name="label"></select>
												<div class="text-danger hidden validation-error" id="label-validation">Label field required</div>
											</div>
										</div>

										<div id="group-table-no-ancestors" style="margin: 0 10%;" class="hidden">This table has no parent table. Thus, records can't be grouped by a field from another table.</div>

										<?php echo $separator; ?>

										<div class="form-group" id="how-to-summarize-group">
											<label for="how-to-summarize" id="how-to-summarize-label" class="control-label <?php echo $modal_label_classes; ?>">
												How to summarize?
											</label>
											<div class="<?php echo $modal_input_classes; ?>">
												<select class="form-control" id="how-to-summarize" name="how-to-summarize" ></select>
											</div>
										</div>
										<div class="form-group" id="summarized-value-group">
											<label for="summarized-value" id="summarized-value-label" class="control-label <?php echo $modal_label_classes; ?>">
												What field to summarize?
											</label>
											<div class="<?php echo $modal_input_classes; ?>">
												<select class="form-control" id="summarized-value" name="summarized-value" ></select>
												<div class="text-danger hidden validation-error" id="what-to-summarize-required">A field must be selected</div>
											</div>
										</div>
										<div class="hidden" id="summarized-value-validation" style="margin: 0 10%">This table has no fields to summarize. You can only use count.</div>

										<?php echo $separator; ?>
										
										<div class="form-group">
											<label for="date-field" class="control-label <?php echo $modal_label_classes; ?>">
												Date field used to filter the report
											</label>
											<div class="<?php echo $modal_input_classes; ?>">
												<select class="form-control" id="date-field" name="date-field"></select>
											</div>
										</div>
										
										<?php echo $separator; ?>

										<div class="form-group">
											<label for="group-array" class="control-label <?php echo $modal_label_classes; ?>">
												Groups that can access this report
											</label>
											<div class="<?php echo $modal_input_classes; ?>">
												 <textarea class="form-control" rows="5" id="group-array" name="group-array"></textarea>
												 <span id="helpBlock" class="help-block">Enter each group in a separate line or leave it blank for all groups</span>
											</div>
										</div>
									</div> <!-- end of data section of report edit form -->

								</div>
						
							</form>
						</div>

						<?php $num_slides = 4; ?>
						<?php for($slide = 1; $slide <= $num_slides; $slide++) { ?>
							<div class="item" style="min-height: 50rem;">
								<button type="button" class="btn btn-default help-closer"><i class="glyphicon glyphicon-remove"></i> Back to report settings</button>
		
								<div class="btn-group pull-right">
									<?php if($slide > 1) { ?>
										<button type="button" class="btn btn-default help-prev" data-goto="<?php echo $slide - 1; ?>"><i class="glyphicon glyphicon-chevron-left"></i> Previous</button>
									<?php } ?>
									<?php if($slide < $num_slides) { ?>
										<button type="button" class="btn btn-default help-next" data-goto="<?php echo $slide + 1; ?>"><i class="glyphicon glyphicon-chevron-right"></i> Next</button>
									<?php } ?>
								</div>
								<div class="clearfix" style="border-bottom: 2px solid black; margin: 1rem 0;"></div>

								<img class="img-responsive" src="images/report-editor-help-<?php echo ($slide < 10 ? "0{$slide}" : $slide); ?>.png">
							</div>
						<?php } ?>

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="save-report" >Save</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- /Modal -->

<style>
	.panel tr:first-child th, .panel tr:first-child td {
		border-top: none !important;
	}
	.panel-title { font-weight: bold; }

	.report-sections {
		width: 13.5rem;
		margin: 0 auto;
		position: relative;
		height: 18.75rem;
	}
	.report-sections > .paper-mocker {
		height: 18.75rem;
		padding: 1rem;
		box-shadow: 0 0 9px 0 silver;
	}
	.report-listing tr:nth-child(2) th, .report-listing tr:nth-child(2) td {
		border-top: none;
	}
	.report-listing th, .report-listing td {
		vertical-align: middle !important;
	}

	#report-sections { padding-left: 4rem; }
	#report-sections label { text-align: left; }
	#report-sections .form-group { margin-left: 0; margin-right: 0; }
	#report-sections .img-responsive { width: 70%; }

	.pointer {
		cursor: pointer;
	}
</style>

<?php
include(dirname(__FILE__) . '/footer.php'); ?>
