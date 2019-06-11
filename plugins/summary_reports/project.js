$j(function() {
	/* variable definitions/initialization */
	var project = AppGini.project;
	if(project.table.length === undefined) project.table = $j.makeArray(project.table);
	var axp_md5 = AppGini.axp_md5;
	var summaryFunctions = {
		avg: 'Average',
		count: 'Count',
		max: 'Maximum',
		min: 'Minimum',
		sum: 'Sum'
	};
	AppGini.getTableAncestors = {};

	/* TODO: (Debugging) set to false on production */
	AppGini.srDebug = false;

	/* TODO: (Refactoring) for all SR-related AppGini vars, store under AppGiniPlugin instead of AppGini? */

	AppGini.listTableReports = function(table_index) {
		debug('AppGini.listTableReports', table_index);

		var table = project.table[table_index];
		AppGini.table_name = table.name;
		AppGini.table_index = table_index;

		// caching table ancestors, if needed, to be ready when report editor is open
		getTableAncestors(AppGini.table_name);
		
		$j("#previous-reports").val("");
		
		if(table.plugins === undefined) return alertTableHasNoReports();
		
		var table_plugins = project.table[table_index].plugins;
		if( table_plugins.summary_reports === undefined )
			return alertTableHasNoReports();
		
		if( table_plugins.summary_reports.report_details === undefined )
			return alertTableHasNoReports();

		if( table_plugins.summary_reports.report_details.length <= 17 )
			return alertTableHasNoReports();
	 
		var reportsJson = table_plugins.summary_reports.report_details;
		var report = JSON.parse(reportsJson);
		$j( "#previous-reports" ).val(reportsJson);
		
		var table_html = '';

		/* loop over reports */
		for(var i = 0; i < report.length; i++) {
			/* Get the values and push them in array*/
			var report_config_values = {};	
			var title = report[i].title;
			var ctIndex = table_index;
			/* group table caption, label field caption */
			var gtc = '', lfc = '';
			/* grouping function */
			var gf = summaryFunctions[report[i].group_function];
			/* selected table caption */
			var stc = project.table[ctIndex].caption;
			
			gtc = project.table[ctIndex].caption;
			lfc = getCaption(ctIndex, report[i].label);

			if(report[i].parent_table !== undefined) {
				gtc = getCaption(report[i].parent_table);
				ctIndex = getTableIndex(report[i].parent_table);
				lfc = getCaption(ctIndex, report[i].label);
			}
			
			
			report_config_values["How / what to summarize?"] = (
				gf != summaryFunctions.count ?
					gf + ' of <code>' + stc + '</code> . <code>' + 
					getCaption(table_index, report[i].group_function_field) +
					'</code>'
				:
					'Count of records of <code>' + stc + '</code> table'
			);
			
			report_config_values["Grouped by"] = '<code>' + gtc + '</code> . <code>' + lfc + '</code>';

			report_config_values["Date field used to filter the report"] = 'No filtering by dates';
			if(report[i].date_field !== undefined) {
				report_config_values["Date field used to filter the report"] = '' + 
					'<code>' + stc + '</code> . <code>' + getCaption(table_index, report[i].date_field) + '</code>';
			}

			report_config_values["Which groups can access this report?"] = '<span class="text-danger text-bold">All groups</span>';
			if(report[i].group_array !== undefined) {
				if(report[i].group_array.length)
					report_config_values["Which groups can access this report?"] = '<code>' + report[i].group_array.join('</code> <code>') + '</code>';
			}
 			
			report_config_values['Report sections'] = '<div class="report-sections"><div class="paper-mocker">' +
				(report[i].report_header_url ? '<img src="' + encodeURI(report[i].report_header_url) + '" class="img-responsive" alt="Loading report header image ...">' : '') +
				(report[i].data_table_section ? '<img src="images/summary-reports-data-table-section.png" class="img-responsive">' : '') +
				(report[i].barchart_section ? '<img src="images/summary-reports-bar-chart-section.png" class="img-responsive">' : '') +
				(report[i].piechart_section ? '<img src="images/summary-reports-pie-chart-section.png" class="img-responsive">' : '') +
				(report[i].report_footer_url ? '<img src="' + encodeURI(report[i].report_footer_url) + '" class="img-responsive" alt="Loading report footer image ...">' : '') +
				'</div></div>';
				
			/* loop over table config and draw it */	
			var table_conf_values = '';
			for (var key in report_config_values) {
				if(key == 'Report sections') continue;
				table_conf_values += '' +
					'<tr>' +
						'<th class="text-right">' + key + '</th>' +
						'<td>' + report_config_values[key] + '</td>' +
					'</tr>';	 
			}

			var hiddenPanelBody = $j('#table-reports').hasClass('hidden-panel-body') ? 'hidden' : '';

			table_html +=	'<div class="panel panel-success">' +
							'<div class="panel-heading">' +
								'<h3 class="panel-title">' +
									'<i class="glyphicon glyphicon-list-alt"></i> ' + title + 
									'<div class="btn-group pull-right">' +
										'<button title="Edit report" type="button" class="btn btn-default btn-sm edit-report" id="edit-table' + i + '" data-toggle="modal" data-target="#report-modal" data-id="' + i + '"><span class="glyphicon glyphicon-pencil text-primary"></span> <span class="text-primary">Edit</span></button>' +
										'<button title="Delete report" type="button" class="btn btn-default btn-sm delete-report" id="delete-table' + i + '" data-id="' + i + '"><span class="glyphicon glyphicon-trash text-danger"></span> <span class="text-danger">Delete</span></button>' +
									'</div>' +
									'<div class="clearfix"></div>' +
								'</h3>' +
							'</div>' +
							'<div class="panel-body ' + hiddenPanelBody + '">' +
								'<table class="table report-listing" style="margin-bottom: 0;">' +
									'<tr><td rowspan="5">' + report_config_values['Report sections'] + '</td><td></td><td></td></tr>' +
									table_conf_values +
								'</table>' +
							'</div>' +
						'</div>' +							
						'';
		}
		
		$j('#table-reports div').remove();
		$j(table_html).appendTo("#table-reports");

	}
	
	var debug = function(fn, p1, p2, p3, p4, p5) {
		if(!AppGini.srDebug) return;
		var msg = fn + '(';
		if(p1 != undefined) msg += (  "'" + p1 + "'");
		if(p2 != undefined) msg += (", '" + p2 + "'");
		if(p3 != undefined) msg += (", '" + p3 + "'");
		if(p4 != undefined) msg += (", '" + p4 + "'");
		if(p5 != undefined) msg += (", '" + p5 + "'");
		msg += ')';

		console.log(msg);
	}

	/* displayFormElement('abc') to show #abc field */
	/* displayFormElement('abc', false) to hide #abc field */
	var displayFormElement = function(selector, display) {
		debug('displayFormElement', selector, display);

		if(display === undefined) display = true;

		$j(selector)
			.prop('disabled', !display)
			.parents('.form-group')
			.addClass(display ? '' : 'hidden')
			.removeClass(display ? 'hidden' : '');
	}

	var randomHash = function(length) {
		length = length || 20;
		var hash = '', pool = 'abcdefghijklmnopqrstuvwxyz0123456789';
		for(var i = 0; i < length; i++) {
			hash += pool.charAt(Math.random() * pool.length);
		}
		return hash;
	}
	
	var resetReportForm = function() {
		debug('resetReportForm');

		/* empty hidden inputs of report form */
		/* 
			TODO: (Refactoring) This should be replaced with some sort of state ..
			ideally: AppGini.project.table[i].plugins.summary_reports.report_details
		 */
		$j(
			"#table-index, #report-id, #first-caption," + 
			"#second-caption, #look-up-table, #look-up-value, #label-field-index" +
			"#date-field-index"
		).val('');

		/* assign a new report hash */
		$j('#report-hash').val(randomHash());
		
		/* hide any validation errors */
		$j('.validation-error').addClass('hidden');
		
		/* empty report title*/
		$j('#report-title').val('').focus();
		
		displayGroupingControls();

		displaySummaryControls({ function: 'count' });
		
		/* empty 'Date field' */
		$j('#date-field').empty();
		
		/* empty 'Groups' textarea */
		$j('#group-array').val('');
		
		/* show data table section but not charts, by default */
		$j('#data-table-section').prop('checked', true);
		$j('#bar-chart-section, #pie-chart-section').prop('checked', false);

		/* empty header and footer */
		$j('#report-header-url, #report-footer-url').val('');
	}
	
	/* retrieve the caption of given table, or given field in given table */
	var getCaption = function(table, field_name) {
		debug('getCaption', table, field_name);
		
		if( field_name === undefined ){
			for ( var key in project.table ) {
				if ( project.table[key].name === table ) {
					return project.table[key].caption; 
				}
			} 
		} 
		
		var table_fields = project.table[table].field;
		
		for (var key in table_fields ) {
			if ( table_fields[key].name == field_name ) {
				return table_fields[key].caption; 
			}
		}
		
		return '';
	}
	
	
	var getFieldIndex = function(table_index, field_name ) {
		debug('getFieldIndex', table_index, field_name);

		var table_fields = project.table[table_index].field;
		
		for (var key in table_fields ) {
			if ( table_fields[key].name == field_name ) return key; 	
		}
		return false;
	}
	
	var isLookupField = function(table_index, field_name) {
		debug('isLookupField', table_index, field_name);
		
		var field_index = getFieldIndex(table_index, field_name);
		if(field_index === false) return false ;
		
		if(typeof(project.table[table_index].field[field_index].parentTable) != "string"){
			return false;
		}
		
		return true;
	}
	
	//get lookup table
	var getLookupTable = function(table_index, field_name) {
		debug('getLookupTable', table_index, field_name);

		var field_index = getFieldIndex(table_index, field_name);
		return project.table[table_index].field[field_index].parentTable;
		 
	}
	
	//get lookup value
	
	var getLookupValue = function(table_index, field_name) {
		debug('getLookupValue', table_index, field_name);
	
		var field_index = getFieldIndex(table_index, field_name);
		return project.table[table_index].field[field_index].parentCaptionField;
		 
	}
	
	var getTableIndex = function(table_name) {
		debug('getTableIndex', table_name);

		for ( var key in project.table ) {
				if ( project.table[key].name === table_name) {
					return parseInt( key ); 
				}
			}
	}
	
	/* fill #how-to-summarize drop-down if empty, and selected given value */
	var populateHowToSummarizeField = function(selectedValue, countOnly) {
		debug('populateHowToSummarizeField', selectedValue, countOnly);
		
		if(countOnly == undefined) countOnly = false;

		/* Fill How To Summrize drop-down */
		$j('#how-to-summarize').empty();
		$j.each(summaryFunctions, function(key , caption) {
			if(countOnly && key != 'count') return;
			addOptionToSelect({
				select: '#how-to-summarize',
				value: key,
				caption: caption,
				selected: selectedValue == key
			});
		});
	}
	
	var getTableAncestors = function(table) {
		debug('getTableAncestors', table);

		/* table ancestors already cached? */
		if(AppGini.getTableAncestors[table] != undefined) return;

		/* Send ajax request to get table ancestors */
		$j.ajax({
			url: 'table-ancestors-ajax.php',
			data: {
				axp: AppGini.axp_md5,
				table_name: table
			},
			success: function(data) {
				AppGini.getTableAncestors[table] = JSON.parse(data);
			},
			error: function() {
				// try again in 30 seconds
				(function(tn) {
					setTimeout(function() { getTableAncestors(tn); }, 30000);
				})(table);
			}
		});
	}
	
	var populateTableAncestors = function(table, ancestorTableName) {
		debug('populateTableAncestors', table, ancestorTableName);

		if(AppGini.getTableAncestors[table] == undefined) return;
		var ancestors = AppGini.getTableAncestors[table];

		$j('#group-table').empty();
		addOptionToSelect({ select: '#group-table' });

		if(!ancestors.length) return;

		for(var i = 0; i < ancestors.length; i++) {
			addOptionToSelect({
				select: '#group-table',
				value: ancestors[i],
				selected: ancestors[i] == ancestorTableName
			});
		}
	}

	var populateDateFields = function(table, dateField) {
		debug('populateDateFields', table, dateField);

		var dates = {};
		var table_fields = [];
		var tables = project.table;
	
		/* Detects the type of the passed parmeter and retrives it's fields */
		table_fields = (typeof(table) == 'string' ? getTableByName(table).field : tables[table].field);
		
		/* Loop over table fields to retrieve date and datetime fields */
		for(var j = 0; j < table_fields.length; j++) {	 
			var field_type = parseInt(table_fields[j].dataType);
			if(field_type >= 9 && field_type <= 10) {
				dates[table_fields[j].name] = table_fields[j].caption;
			}
		}

		/* update date select */
		addOptionToSelect({ select: '#date-field', caption: "Don't filter the report by date" });
	 	$j.each(dates, function(name, caption) {
			addOptionToSelect({
				select: '#date-field',
				value: name,
				caption: caption,
				selected: (name == dateField)
			});
		}); 	
	}

	var populateLabels = function(table, selectedLabel) { // can accept table name or table index
		debug('populateLabels', table, selectedLabel);

		$j('#label').empty();
		addOptionToSelect({ select: '#label' });

		table = table || AppGini.table_name;

		var labels = {}, table_fields = [], tables = project.table;
	
		/* Detects the type of the passed parmeter and retrives its fields */
		if(typeof table == 'string'){	
			for(var i = 0; i < tables.length; i++) {
				if(tables[i].name != table) continue;

				table_fields = tables[i].field;
				break;
			}
		} else if(tables[table] != undefined) {
			table_fields = tables[table].field;
		} else {
			// invalid table value .. return, leaving #label empty
			return;
		}
		
		/* Loop over table fields and categorize them */		
		for(var j = 0; j < table_fields.length; j++) {
			labels[table_fields[j].name] = table_fields[j].caption;
		}

		/* update labels select */
		$j.each(labels, function(name, caption) {
			addOptionToSelect({
				select: '#label',
				value: name,
				caption: caption,
				selected: name == selectedLabel
			});
		});
	}
	
	var alertTableHasNoReports = function() {
		debug('alertTableHasNoReports');

		$j("#table-reports").html(
			'<div class="alert alert-warning">' +
				'This table has no reports configured yet. ' +
				'<a href="#" onclick="$j(\'#add-report\').click(); return false;">Create one now!</a>' +
			'</div>'
		);
		
		return false;
	}
	
	var getSelectdReportDetails = function(reportId) {
		debug('getSelectdReportDetails', reportId);

		var report_details_json = project.table[AppGini.table_index].plugins.summary_reports.report_details;
		var all_reports_details = JSON.parse(report_details_json);
		return all_reports_details[reportId];
	}

	var displayGroupingControls = function(data) {
		debug('displayGroupingControls', JSON.stringify(data));

		// defaults
		data = $j.extend({}, {
			table: AppGini.table_name,
			groupTable: '',
			labelField: '',
			userTriggered: false
		}, data);

		populateTableAncestors(data.table, data.groupTable);

		var ancestors = AppGini.getTableAncestors[data.table] || [];
		if(ancestors.length) {
			displayFormElement('#single-table');
			if(!data.userTriggered) $j('#single-table').prop('checked', data.groupTable != '');

			displayFormElement('#group-table', $j('#single-table').prop('checked'));			
			$j('#group-table-no-ancestors').addClass('hidden');

			if(data.userTriggered) data.labelField = $j('#label').val();
			populateLabels(data.groupTable ? data.groupTable : data.table, data.labelField);

			return;
		}

		displayFormElement('#single-table', false);
		$j('#single-table').prop('checked', false);

		displayFormElement('#group-table', false);
		$j('#group-table-no-ancestors').removeClass('hidden');
		populateLabels(data.table, data.labelField);
	}

	var fieldCanBeSummmarized = function(f) {
		debug('fieldCanBeSummmarized', '{' + f.name + '}');

		return (parseInt(f.dataType) < 9 && f.primaryKey != 'True' && f.unique != 'True' && f.parentTable.length == undefined);
	}

	var summaryFields = function(t) {
		debug('summaryFields', '{' + t.name + '}');

		var fields = [];
		for(var i = 0; i < t.field.length; i++) {
			if(fieldCanBeSummmarized(t.field[i])) fields.push(t.field[i]);
		}
		return fields;
	}

	var getTableByName = function(tn) {
		debug('getTableByName', tn);

		var tables = AppGini.project.table;
		for(var i = 0; i < tables.length; i++) {
			if(tables[i].name == tn) return tables[i];
		}

		// otherwise, return is 'undefined'
	}

	var addOptionToSelect = function(data) {
		//debug('addOptionToSelect', JSON.stringify(data));

		data = $j.extend({}, {
			select: '',
			value: '',
			caption: null,
			selected: false
		}, data);

		if(!$j(data.select).length) return;
		if(data.caption === null) data.caption = data.value;

		$j('<option></option>')
			.attr('value', data.value)
			.prop('selected', data.selected)
			.text(data.caption)
			.appendTo(data.select);
	}

	/* populate #summarized-value drop-down for given tablename, and select given field name, and return number of summary fields */
	var populateSummaryFields = function(tn, fn) {
		debug('populateSummaryFields', tn, fn);

		var t = getTableByName(tn);
		if(t == undefined) return 0;

		var sf = summaryFields(t);
		$j('#summarized-value').empty();		

		addOptionToSelect({ select: '#summarized-value' });
		for(var i = 0; i < sf.length; i++) {
			addOptionToSelect({
				select: '#summarized-value', 
				value: sf[i].name,
				caption: sf[i].caption,
				selected: sf[i].name == fn
			});
		}

		return sf.length;
	}

	var displaySummaryControls = function(data) {
		debug('displaySummaryControls', JSON.stringify(data));

		// defaults
		data = $j.extend({}, {
			table: AppGini.table_name,
			function: 'count',
			field: '',
			userTriggered: false
		}, data);

		var numSummaryFields = populateSummaryFields(data.table, data.field);
		if(numSummaryFields) {
			populateHowToSummarizeField(data.function);
			$j('#how-to-summarize').prop('readonly', false);
			displayFormElement('#summarized-value', data.function != 'count');
			// data.userTriggered??
			$j('#summarized-value-validation').addClass('hidden');
			return;
		}

		displayFormElement('#summarized-value', false);
		$j('#how-to-summarize').prop('readonly', true);
		populateHowToSummarizeField('count', true);
		$j('#summarized-value-validation').removeClass('hidden');
	}

	/* Triggring Add and Edit Modal Events */
 	$j('#group-table').on('change', function() {
 		var tn = $j('#group-table').val() || AppGini.table_name;
		populateLabels(tn);
	}); 
	
	/* Change How to summarize event*/
	$j('#how-to-summarize').on('change', function() {
		displaySummaryControls({
			function: $j(this).val(),
			field: $j('#summarized-value').val(),
			userTriggered: true
		});
	});

	/* on user-initiated toggling of #single-table, update display of grouping controls */
	$j('#single-table').on('click', function() {
		displayGroupingControls({ userTriggered: true });
	});

	$j('#add-report').on('click', function() {
		var table_caption = project.table[AppGini.table_index].caption;
		
		resetReportForm();
		$j("#table-index").val(AppGini.table_index);

		/* Add report window title  */
		$j("#modal-title").html('Create a new <span class="text-info">' + table_caption + '</span> report');
		
		/* Fill the rest of fields */
		populateDateFields(AppGini.table_name);
	});

	$j('#table-reports').on('click', '.edit-report', function() {
		resetReportForm();		
		var report_id = $j(this).data('id');
		var report = getSelectdReportDetails(report_id);
		$j("#table-index").val(AppGini.table_index);
		
		$j("#report-id").val(report_id);
		if(report.report_hash != undefined) $j('#report-hash').val(report.report_hash);
		
		/* Set report window title  */
		$j("#modal-title").html('Edit <span class="text-info">' + report.title + '</span> report');

		/* update report title */
		$j("#report-title").val(report.title);
	

		/* Populate date fields */		
		populateDateFields(AppGini.table_index, report.date_field);
		
		displayGroupingControls({
			table: AppGini.table_name,
			groupTable: report.parent_table,
			labelField: report.label
		});

		displaySummaryControls({
			function: report.group_function,
			field: report.group_function_field
		});

		$j("#group-array").val(report.group_array);
		
		if(typeof report.group_array !== 'undefined') {
			var group_array = "";
			for (var key in report.group_array) {
				group_array += report.group_array[key] + "\n";
				$j("#group-array").val(group_array);
			}
		}

		/* report sections */
		if(report.data_table_section != undefined) {
			$j('#data-table-section').prop('checked', report.data_table_section);
		}
		if(report.piechart_section != undefined) {
			$j('#pie-chart-section').prop('checked', report.piechart_section);
		}
		if(report.barchart_section != undefined) {
			$j('#bar-chart-section').prop('checked', report.barchart_section);
		}

		/* report header and footer */
		if(report.report_header_url != undefined) {
			$j('#report-header-url').val(report.report_header_url);
		}
		if(report.report_footer_url != undefined) {
			$j('#report-footer-url').val(report.report_footer_url);
		}
	})


	$j("#table-reports").on('click',".delete-report",function(){		 
		if(!confirm('Are you sure you want to delete this report?')) return;
		
		var node_index = $j(this).data('id');
		var table_index = AppGini.table_index;
		
		$j(this).addClass("disabled");
	 	$j.ajax({
			type: "POST",
			url: 'delete_node_ajax.php',
			data: { 
				axp: axp_md5, 
				table_name: AppGini.table_name,
				node_index: node_index,
				table_index: table_index
			},
			success: function(data) {  /* */
				project.table[table_index].plugins = project.table[AppGini.table_index].plugins || {};
				project.table[table_index].plugins.summary_reports = project.table[table_index].plugins.summary_reports || {};
				project.table[table_index].plugins.summary_reports.report_details = data;
				if(table_index == AppGini.table_index){
					AppGini.listTableReports(table_index); 
				} 
			}
		});
	}); 
	
	$j('#report-title').keyup(function() {
		if($j(this).val() != '') $j('#title-validation').addClass('hidden');
	});
	
	$j('#label').change(function() {
		if($j(this).val() != '') $j('#label-validation').addClass('hidden');
	});

	$j('#how-to-summarize, #summarized-value').change(function() {
		if($j('#summarized-value').val() != '' || $j('#how-to-summarize').val() == 'count')
			$j('#what-to-summarize-required').addClass('hidden');
	});
	
	$j('#report-editor-form').submit(function(e){
		e.preventDefault();
		$j('#save-report').click();
	})

	$j('#save-report').click(function() {
		debug('$j("#save-report").click');

		/* Validating report title required */
		if($j("#report-title").val() == '') {
			$j('#title-validation').removeClass('hidden');
			$j("#report-title").focus();
			return;
		}
		
		/* #how-to-summarize is not 'Count' and no #summarized-value */
		if($j('#summarized-value').val() == '' && $j('#how-to-summarize').val() != 'count'){
			$j('#what-to-summarize-required').removeClass('hidden');
			$j('#summarized-value').focus();
			return;
		}
		
		/* Don't allow empty label field */
		if($j('#label').val() == ''){
			$j('#label-validation').removeClass('hidden');
			$j('#label').focus();
			return;
		}
		
		if($j("#group-table").val() == '') {
			var label_field = $j("#label").val();
			if(isLookupField($j("#table-index").val(), label_field) == false){
				$j("#look-up-table, #look-up-value").val(''); 
			}else{
				//get lookup table
				var lookup_table = getLookupTable($j("#table-index").val(), label_field);
				$j("#look-up-table").val(lookup_table);
				
				//get lookup value
				var lookup_value = getLookupValue($j("#table-index").val(), label_field);
				$j("#look-up-value").val(lookup_value);
			}
		}
	 
		if($j("#group-table").val()) {
			$j("#first-caption").val(
				getCaption(
					getTableIndex(
						$j("#group-table").val()
					),
					$j("#label").val()
				)
			);
		} else {
			$j("#first-caption").val(
				getCaption(
					$j("#table-index").val(), 
					$j("#label").val()
				)
			);
		}
		
		$j('#second-caption').val(
			summaryFunctions[$j("#how-to-summarize").val()] + 
			" of " +
			getCaption(AppGini.table_name)
		);

		$j('#label-field-index').val(
			parseInt(
				getFieldIndex(
					AppGini.table_index, 
					$j('#label').val()
				)
			) + 1
		);

		$j('#date-field-index').val(
			parseInt(
				getFieldIndex(
					AppGini.table_index, 
					$j('#date-field').val()
				)
			) + 1
		);

		$j('#save-report').prop('disabled', true).text('Please wait ...');

		$j.ajax({
			type: "POST",
			url: 'update_node_ajax.php?axp=' + axp_md5 + '&table_name=' + AppGini.table_name,
			data: $j("#report-editor-form").serialize(),
			success: function(data) { /* */
				debug('update_node_ajax.php: success', JSON.stringify(data));

				project.table[AppGini.table_index].plugins = project.table[AppGini.table_index].plugins || {};
				project.table[AppGini.table_index].plugins.summary_reports = project.table[AppGini.table_index].plugins.summary_reports || {};
				project.table[AppGini.table_index].plugins.summary_reports.report_details = data;
				AppGini.listTableReports(AppGini.table_index); 
			},
			complete: function() {
				debug('update_node_ajax.php: complete');

				$j('#report-modal').modal('hide');
				$j('#save-report').prop('disabled', false).text('Save');

				/* Empty new report modal */
				document.getElementById("report-editor-form").reset();
				$j("#report-id").val('');
				$j("#report-hash").val('');
			}
		});

	}); 

	$j('#report-modal').on('hidden.bs.modal', function () { /* */
		document.getElementById("report-editor-form").reset();
		$j("#report-id").val('');
		$j("#report-hash").val('');
		$j('.carousel').carousel(0);
	}).on('shown.bs.modal', function() { /* */
		$j('#report-title').focus();
		$j('.carousel').carousel({ keyboard: false, interval: false }).carousel(0);
	});

	$j('.help-launcher').click(function() {
		$j('.carousel').carousel(1);
	})

	$j('.help-closer').click(function() {
		$j('.carousel').carousel(0);
	})

	$j('.help-next, .help-prev').click(function() {
		$j('.carousel').carousel($j(this).data('goto'));
	})

	$j('#detailed-reports-list').click(function() {
		$j('#table-reports').removeClass('hidden-panel-body');
		$j('#table-reports .panel-body').removeClass('hidden');
	})

	$j('#compact-reports-list').click(function() {
		$j('#table-reports').addClass('hidden-panel-body');
		$j('#table-reports .panel-body').addClass('hidden');
	})

	/* on page load, cache table ancestors for all tables, one table at a time */
	for(var ti = 0; ti < project.table.length; ti++) {
		(function(tableName, delay) {
			setTimeout(function() { getTableAncestors(tableName); }, delay);
		})(project.table[ti].name, (ti + 1) * 1000);
	}

	/* move focus to the first table */
	setTimeout(function() { $j('#tables-list a:first').focus() }, 1000);
})