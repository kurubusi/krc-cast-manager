<div class="wrap">
	<div class="icon32" id="icon-edit"><br></div>
	<h2>キャスト表示順変更</h2>
	<div id="ajax-response"></div>
	<noscript>
		<div class="error message">
			<p><?php _e('This plugin can\'t work without javascript, because it\'s use drag and drop and AJAX.', 'cpt') ?></p>
		</div>
	</noscript>

	
	<div id="order-post-type" class="postbox " >
		<h3 class='hndle'><span>キャスト表示順変更BOX</span></h3>
		<div class="inside">
			
			<ul id="sortable">
				<?php echo $data['list_pege']; ?>
			</ul>
			<p class="krc_memo">ドラッグ&ドロップでキャストの表示位置を入れ替え、表示順の決定後「決定」ボタンをクリックして下さい。</p>
		</div>
	</div>
	
	
	
	<p class="submit">
		<a href="javascript: void(0)" id="save-order" class="button-primary"><?php _e('決定', 'krc' ) ?></a>
	</p>
</div>