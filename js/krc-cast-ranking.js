
$jq = jQuery.noConflict();

$jq(document).ready(function () {
	
	
	$jq("#ranking_cast_out").sortable({
		'connectWith': '#ranking_cast_in',
	});
	$jq("#ranking_cast_in").sortable({
		'connectWith': '#ranking_cast_out',
	});
	
	$jq("#ranking_cast_out, #ranking_cast_in").sortable({
		'tolerance': 'pointer',
		'cursor':'move',
		'items':'dl',
		'placeholder':'placeholder',
		stop: function (evt, ui) {
			if (ui.item.parent().attr("id") == 'ranking_cast_in' ) {
				
				
				
				if (ui.item.find("input").length == 0) {
					$jq('<input>').attr({
						type: 'hidden',
						name: 'h_krc_cast_rankings[]',
						class: 'krc_ranking_img_prev_hidden',
						value: ui.item.attr("id"),
					}).appendTo(ui.item);
				}
			} else {
				ui.item.find("input").remove();
				
			}
		}
	});
	
	
	
	
	
});



