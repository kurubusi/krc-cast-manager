
$jq = jQuery.noConflict();

$jq(document).ready(function () {
	
	$jq("#sortable").sortable({
		'tolerance':'intersect',
		'cursor':'pointer',
		'items':'li',
		'placeholder':'placeholder',
		'nested': 'ul'
	});
	
	$jq("#sortable").disableSelection();
	$jq("#save-order").bind( "click", function() {
		$jq("html, body").animate({ scrollTop: 0 }, "fast");
		$jq.post( ajaxurl, { action:'update-custom-type-order', order: $jq("#sortable").sortable("serialize") }, function() {
			$jq("#ajax-response").html('<div class="message updated fade"><p>表示順を変更致しました。</p></div>');
			$jq("#ajax-response div").delay(3000).hide("slow");
		});
	});
	
	//order: "item[]=233&item[]=255&item[]=237&item[]=256&item[]=242&item[]=243&item[]=254"}
	
	
	
	
	
	
});



