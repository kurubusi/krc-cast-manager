
$jq = jQuery.noConflict();

$jq(document).ready(function () {
	
	/*
	$jq('.krc_multi_file').each(function () {
		var fieldId = $jq(this).attr("id");
		$jq(this).after("<div id='krc_upload_panel_"+ fieldId +"' ></div>");
		$jq("#krc_upload_panel_"+ fieldId).html("<input type='button' value='キャスト画像アップロード' class='widefat button krc_upload_btn' id='"+ fieldId +"' />");
		$jq("#krc_upload_panel_"+ fieldId).append("<div class='krc_preview_box' id='"+ fieldId +"_panel' ></div>");
		$jq(this).remove();
	});
	*/
	
	var org_media = wp.media.editor.send.attachment;
	$jq('.krc_upload_btn').click(function () {
		var uploadObject = $jq(this);
		wp.media.editor.send.attachment = function(props, attachment) {
			$jq(uploadObject).parent().find(".krc_preview_box").append("<li><img class='krc_img_prev cast_photo' style='' src='" + attachment.url + "' /><input class='krc_img_prev_hidden' type='hidden' name='h_krc_cast_screens[]' value='" + attachment.url +"' /></li>");
		}
		wp.media.editor.open();
		return false;
	});
	
	$jq("body").on("dblclick", ".krc_img_prev" , function() {
		$jq(this).next(".krc_img_prev_hidden").remove();
		$jq(this).remove();
	});
	
	$jq('.krc_preview_box' ).sortable({
		cursor : 'move',
		tolerance : 'pointer',
		opacity: 0.6
	});
	
	
	
	
	
	
	
	
	
});



