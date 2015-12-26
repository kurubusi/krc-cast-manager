
$jq = jQuery.noConflict();

$jq(document).ready(function () {
	
	var hiduke = new Date(), 
			year = hiduke.getFullYear(),
			month = hiduke.getMonth() + 1,
			day = hiduke.getDate(),
			today = year  + '/' + month + '/' + day;
	
	var time_popup = '<dd class="time_input"><div class="time_popup"><dl><dt><label for="遅早表記">遅早表記</label></dt><dd><select name="fastslow" class="fastslow"><option value="0">指定無</option><option value="早番">早番</option><option value="中番">中番</option><option value="遅番">遅番</option></select></dd></dl><dl><dt><label for="時間表記">時間表記</label></dt><dd><select name="starttime" class="starttime"><option value="0">指定無</option>';
	time_popup += '<option value="OPEN">OPEN</option>';
	for (var i = 10; i <= 26; i++) {
		time_popup += '<option value="' + i + '時">' + i + '時</option>';
	}
	time_popup += '</select><br>から</dd><dd><select name="endtime" class="endtime"><option value="0">指定無</option>';
	for (var i = 10; i <= 26; i++) {
		time_popup += '<option value="' + i + '時">' + i + '時</option>';
	}
	time_popup += '<option value="LAST">LAST</option>';
	time_popup += '</select></dd></dl></div></dd>';
	
	
	
	
	$jq("#datepicker").datepicker({
		'altField': '#schedule_target_day',
		'dateFormat': 'yy/mm/dd',
		'closeText': true,
		'onSelect': function (chosen, inst) {
			//PHPに初期データ取りに行く
			
			$jq.post( ajaxurl, { action: 'krc_schedule_target_day', 'order': chosen }, function(data, status) {
				var cast_arr = $jq.parseJSON(data),
						post_in_sort = [];
				
				$jq('#schedule_cast_out').empty();
				$jq('#schedule_cast_in').empty();
				$jq('#krc_schedule_rest').remove();
				
				if (cast_arr['rest'] == 'rest') {
					//console.log($jq('#schedule_cast_in:before'));
					$jq('#schedule_cast_in').before('<div id="krc_schedule_rest">■定休日に設定しています</div>');
				} else {
					
					$jq.each(cast_arr['post_in'], function (i, val) {
						post_in_sort[val['s_order']] = i;
					});
					
					
					$jq.each(post_in_sort, function (i, val) {
						$jq('#schedule_cast_in').append('<dl class="schedule_cast ui-sortable-handle" id="item_' + val + '"><dt>' + cast_arr['post_in'][val]['krc_name'] + '</dt><dd><img src="' + cast_arr['post_in'][val]['krc_cast_screens'] + '" width="100" class="cast_photo" /></dd>' + time_popup + '</dl>');
						$jq('#item_' + val).find(".fastslow").val(cast_arr['post_in'][val]['fastslow']);
						$jq('#item_' + val).find(".starttime").val(cast_arr['post_in'][val]['starttime']);
						$jq('#item_' + val).find(".endtime").val(cast_arr['post_in'][val]['endtime']);
					});
					/*
					$jq.each(cast_arr['post_in'], function (i, val) {
						$jq('#schedule_cast_in').append('<dl class="schedule_cast ui-sortable-handle" id="item_' + i + '"><dt>' + val['krc_name'] + '</dt><dd><img src="' + val['krc_cast_screens'] + '" width="100" class="cast_photo" /></dd>' + time_popup + '</dl>');
						$jq('#item_' + i).find(".fastslow").val(cast_arr['post_in'][i]['fastslow']);
						$jq('#item_' + i).find(".starttime").val(cast_arr['post_in'][i]['starttime']);
						$jq('#item_' + i).find(".endtime").val(cast_arr['post_in'][i]['endtime']);
					});
					*/
				}
				$jq.each(cast_arr['post_not_in'], function (i, val) {
					$jq('#schedule_cast_out').append('<dl class="schedule_cast ui-sortable-handle" id="item_' + i + '"><dt>' + val['krc_name'] + '</dt><dd><img src="' + val['krc_cast_screens'] + '" width="100" class="cast_photo" /></dd></dl>');
				});
				
				//console.log(data);
				
			});
			
		},
	});
	
	
	
	
	$jq("#schedule_cast_out").sortable({
		'connectWith': '#schedule_cast_in',
	});
	$jq("#schedule_cast_in").sortable({
		'connectWith': '#schedule_cast_out',
	});
	
	$jq("#schedule_cast_out, #schedule_cast_in").sortable({
		'tolerance': 'pointer',
		'cursor':'move',
		'stop': function (evt, ui) {
			if (ui.item.parent().attr("id") == 'schedule_cast_in' ) {
				if (ui.item.find(".time_input").length == 0) {
					ui.item.append(time_popup);
				}
			} else {
				ui.item.find(".time_input").remove();
			}
		}
	});

	
	$jq("#save-schedule").bind( "click", function() {
		$jq("html, body").animate({ scrollTop: 0 }, "fast");
		//出勤時間データとか何とかしなきゃ
		//この配列回してカスタムしたデータをpostしてポップアップの内容も合わせて送る?
		var post_arr = {};
		$jq.each($jq("#schedule_cast_in").sortable("toArray"), function(i, val) {
			id = val.replace(/item_/g,'');
			//console.log(i);
			post_arr[id] = {
				'fastslow': $jq('#' + val + ' .fastslow').val(),
				'starttime': $jq('#' + val + ' .starttime').val(),
				'endtime': $jq('#' + val + ' .endtime').val(),
				's_order': i
			};
		});
		
		$jq('#krc_schedule_rest').remove();
		$jq.post( ajaxurl, { action:'krc_schedule_update', order: post_arr, day: $jq("#schedule_target_day").val() }, function(data, status) {
			$jq("#ajax-response").html('<div class="message updated fade"><p>スケジュールを変更致しました。</p></div>');
			$jq("#ajax-response div").delay(3000).hide("slow");
		});
	});
	
	$jq("#rest-schedule").bind( "click", function() {
		$jq("html, body").animate({ scrollTop: 0 }, "fast");
		$jq.post( ajaxurl, { action:'krc_schedule_update', order: 'rest', day: $jq("#schedule_target_day").val()}, function(data, status) {
			$jq("#ajax-response").html('<div class="message updated fade"><p>定休日に設定致しました。</p></div>');
			$jq("#ajax-response div").delay(3000).hide("slow");
			console.log(data);
		});
	});
	
	
	
	
	
	
	
	
	
	
	
	
	
	
});

/*! jQuery UI - v1.8.20 - 2012-04-30
* https://github.com/jquery/jquery-ui
* Includes: jquery.ui.datepicker-ja.js
* Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
$jq(function(a){a.datepicker.regional.ja={closeText:"閉じる",prevText:"&#x3c;前",nextText:"次&#x3e;",currentText:"今日",monthNames:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"],monthNamesShort:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"],dayNames:["日曜日","月曜日","火曜日","水曜日","木曜日","金曜日","土曜日"],dayNamesShort:["日","月","火","水","木","金","土"],dayNamesMin:["日","月","火","水","木","金","土"],weekHeader:"週",dateFormat:"yy/mm/dd",firstDay:0,isRTL:!1,showMonthAfterYear:!0,yearSuffix:"年"},a.datepicker.setDefaults(a.datepicker.regional.ja)});


