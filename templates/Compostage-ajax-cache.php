<?php
	$rdata = array_map('to_utf8', array_map('nl2br', array_map('html_attr_tags_ok', $rdata)));
	$jdata = array_map('to_utf8', array_map('nl2br', array_map('html_attr_tags_ok', $jdata)));
?>
<script>
	$j(function(){
		var tn = 'Compostage';

		/* data for selected record, or defaults if none is selected */
		var data = {
			lieu_compostage: <?php echo json_encode(array('id' => $rdata['lieu_compostage'], 'value' => $rdata['lieu_compostage'], 'text' => $jdata['lieu_compostage'])); ?>,
			matiere: <?php echo json_encode(array('id' => $rdata['matiere'], 'value' => $rdata['matiere'], 'text' => $jdata['matiere'])); ?>
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for lieu_compostage */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'lieu_compostage' && d.id == data.lieu_compostage.id)
				return { results: [ data.lieu_compostage ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for matiere */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'matiere' && d.id == data.matiere.id)
				return { results: [ data.matiere ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

