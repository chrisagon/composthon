<?php if(!isset($this)) die("You can't call this file directly."); ?>

<link href="../plugins-resources/dropzone/dropzone.min.css" rel="stylesheet">


<div class="page-header">
	<h1><img src="<?php echo html_attr($this->logo); ?>" style="height: 1em;"> <?php echo $this->title; ?></h1>
</div>

<?php echo $pre_upload; ?>
<div class="clearfix"></div>

<div> 
	<div id="response"></div>
	<form method="post" id="my-awesome-dropzone" class="dropzone" autocomplete="off" enctype="multipart/form-data">
		<div class="dz-default dz-message">
			<h1>
				<i class="glyphicon glyphicon-upload text-primary" style="font-size: 300%;"></i><br>
				Drag your AppGini project file (*.axp) here to open it.<br>
				<small>Or click to open the upload dialog.</small>			
			</h1>
		</div>
	</form>
</div>

<?php echo $post_upload; ?>
<div class="clearfix"></div>

<?php 
	if ($projectsNum){
		ob_start();
		?><div class="row"><?php
		foreach ( $currentProjects as $projName ){
			?>
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
				<div class="thumbnail">
					<div class="caption">
						<a href="<?php echo $redirect_to; ?>?axp=<?php echo md5($projName); ?>">
							<img src="../plugins-resources/images/bigprof-logo-only.png" class="img-responsive" alt="AppGiniLogo">
							<?php echo $projName; ?>
						</a>
					</div>
				</div>
			</div>
			<?php
		}
		?></div><?php
		// prepare the project selection code for use as a JS string
		$list_projects = json_encode(ob_get_contents());
		ob_end_clean();
		?>
		
		<h4 class="pull-right">
			<a id="open-projects" href="#">
				<i class="glyphicon glyphicon-folder-open text-warning"></i>&nbsp;
				Or open a project you uploaded before (<?php echo ($projectsNum == 1 ? 'one project' : "{$projectsNum} projects"); ?> found)
			</a>
		</h4>
		<div class="clearfix"></div>

		<script>
			$j(function(){
				$j('#open-projects').click(function(e){
					e.preventDefault();
					modal_window({
						message: <?php echo $list_projects; ?>, 
						title: "Click on a project to load it" 
					});
				})
			});
		</script>

		<?php 
	}
?>

<style>
	.dz-image , .dz-preview{
		width: 100% !important;
		margin: auto !important;
	}	
	.dropzone {
	    border: 3px dashed darkblue;
		min-height: 100px;
		-webkit-border-radius: 30px;
		border-radius: 30px;
		background: rgba(50, 50, 50, 0.06);
		padding: 23px;
	}
	
	.dz-default > img{
		max-width:100%;
		max-height:100%;
	}
	
	.row .thumbnail {
		height: 160px;
		overflow: hidden;
	}	
</style>


<script src="../plugins-resources/dropzone/dropzone.min.js"></script>
<script>
	Dropzone.options.myAwesomeDropzone = {
	  paramName: "uploadedFile", // The name that will be used to transfer the file
	  url: "../plugins-resources/upload-ajax.php",
	  acceptedFiles: ".axp,.AXP",
	  uploadMultiple: false,
	  maxFiles: 1,
	  accept: function(file, done) {
		done();
	  },
	  init: function() {
            this.on("success", function(file, response) {
				$j(".dropzone").css( "border" ,"3px dotted blue");
				response = JSON.parse(response);
				if ( response["response-type"] =="success"){
					var successDiv = $j("<div>", {class: "alert alert-success" , style: "display: none; padding-top: 6px; padding-bottom: 6px;"});
					var successMsg = "File uploaded successfully."+(response.isRenamed?"<br>The project name already exists, the file was renamed to "+response.fileName+".":"") + "<br>Please wait ...";
					successDiv.html( successMsg );
					$j("#response").html(successDiv);
					dismissible_msg( successDiv , "<?php echo $redirect_to; ?>?axp="+response.md5FileName );
				}
            });
			this.on("error", function(file, response){
				if($j.type(response) === "string"){
					response = "You must upload a (.axp) file"; //dropzone sends it's own error messages in string
				}else{
					response = response['error'];
				}
					
				$j("#response").html("<div class='alert alert-danger'>"+response+"</div>");
				$j(".dropzone").css( "border" ,"3px dotted red");
				
				setTimeout( deleteFile, 5000 , file , this);
			});
      }
	}
  	function deleteFile (file , elm){
			elm.removeFile(file);
	}
</script>
