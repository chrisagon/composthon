<?php
	$rdata = array_map('to_utf8', array_map('nl2br', array_map('html_attr_tags_ok', $rdata)));
	$jdata = array_map('to_utf8', array_map('nl2br', array_map('html_attr_tags_ok', $jdata)));
?>
<script>
	$j(function(){
		var tn = 'Demande';

		/* data for selected record, or defaults if none is selected */
		var data = {
			site_compostage: <?php echo json_encode(array('id' => $rdata['site_compostage'], 'value' => $rdata['site_compostage'], 'text' => $jdata['site_compostage'])); ?>,
			site_dechet: <?php echo json_encode(array('id' => $rdata['site_dechet'], 'value' => $rdata['site_dechet'], 'text' => $jdata['site_dechet'])); ?>
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for site_compostage */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'site_compostage' && d.id == data.site_compostage.id)
				return { results: [ data.site_compostage ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for site_dechet */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'site_dechet' && d.id == data.site_dechet.id)
				return { results: [ data.site_dechet ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

