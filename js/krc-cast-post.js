
$jq = jQuery.noConflict();

$jq(document).ready(function () {
	//バリデート　ターム自動入力
	aut_check_len(new Array(18, 21, 24, 27, 30, 33, 36, 39, 42 ), $jq('#krc_age'), $jq('#krc_agechecklist li'));
	aut_check_len(new Array(150, 151, 156, 161, 166, 171, 176 ), $jq('#krc_tall'), $jq('#krc_tallchecklist li'));
	aut_check_alpha(new Array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J' ), $jq('#krc_cups'), $jq('#krc_cupschecklist li'));
	
	function aut_check_len(checkarr, toone, theone) {
		toone.change(function () {
			var price = $jq(this).val();
			if(!price.match(/^[0-9]+$/)){
				//エラー処理
			} else {
				price = Number(price);
				var prev = checkarr[0];
				$jq.each(checkarr, function(i, value) {
					if (price != 0) {
						if (price < checkarr[0]) {
							theone.eq(0).find('input').attr("checked", true );
						} else if (price > checkarr[checkarr.length - 1]) {
							theone.eq(checkarr.length - 1).find('input').attr("checked", true );
						}
						if (price >= prev && price < value) {
							theone.eq(i - 1).find('input').attr("checked", true );
						} else {
							theone.eq(i - 1).find('input').attr("checked", false );
						}
					} else {
						theone.eq(i - 1).find('input').attr("checked", false );
					}
					prev = value;
				});
			}
		});
	}
	function aut_check_alpha(checkarr, toone, theone) {
		toone.change(function () {
			var price = $jq(this).val();
			if(!price.match(/^[a-zA-Z0]+$/)){
				//エラー処理
			} else {
				var price_len = Number(price);
				$jq.each(checkarr, function(i, value) {
					if (price_len != 0) {
						if (value == price) {
							theone.eq(i).find('input').attr("checked", true );
						} else {
							theone.eq(i).find('input').attr("checked", false );
						}
						if (checkarr[checkarr.length - 1] < price) {
							theone.eq(checkarr.length - 1).find('input').attr("checked", true );
						}
					} else {
						theone.eq(i).find('input').attr("checked", false );
					}
				});
			}
			
		});
	}
	
	
	
	
	
	
	
	
});



