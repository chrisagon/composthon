<?php
	define("PREPEND_PATH", "../");
	$hooks_dir = dirname(__FILE__);
	include("{$hooks_dir}/../defaultLang.php");
	include("{$hooks_dir}/../language.php");
	include("{$hooks_dir}/language-summary-reports.php");
	include("{$hooks_dir}/../lib.php");

	$x = new StdClass;
	$x->TableTitle = "Rapports graphiques";
	include_once("{$hooks_dir}/../header.php");
	$user_data = getMemberInfo();
	$user_group = strtolower($user_data["group"]);
?>

<div class="page-header"><h1>Rapports graphiques</h1></div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="text-center text-bold" style="font-size: 1.5em; line-height: 2em;">Compostage</div>
			</div>
			<div class="panel-body">
				<div class="panel-body-description">
					<div class="row">
						<div class ="col-xs-12 col-md-4 col-lg-4">
							<a href ="summary-reports-Compostage-0.php" class="btn btn-success all-groups btn-block btn-lg vspacer-lg summary-reports" style="padding-top: 1em; padding-bottom: 1em;">
								<i class ="glyphicon glyphicon-th"></i> Poids compostés							</a>
						</div>
							<div class ="col-xs-12 col-md-4 col-lg-4">
							<a href ="summary-reports-Compostage-1.php" class="btn btn-success all-groups btn-block btn-lg vspacer-lg summary-reports" style="padding-top: 1em; padding-bottom: 1em;">
								<i class ="glyphicon glyphicon-th"></i> Répartition des matières compostées							</a>
						</div>
							
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
		 
	<script>

		var user_group= <?php echo json_encode($user_group) ?>  ;

		$j(function(){ 
			$j( ".panel a" ).not('.'+user_group).not('.all-groups').parent().remove();
			$j('.panel').each(function(){
				if($j(this).find('a').length == 0){
					$j(this).remove();
				}
 
			}) 
		})

	</script>
	

<?php include_once("$hooks_dir/../footer.php"); ?>