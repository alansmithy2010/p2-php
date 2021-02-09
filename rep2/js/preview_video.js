/*
 * rep2expack - ネット動画のプレビュー
 */

// {{{ preview_video_youtube()

/*
 * プレビューを表示する
 *
 */
$(function() {
	$("img.preview-video-switch").click(function (event) {
		var $container = $("<div>").addClass('preview-video preview-video-youtube');
		var $preview = $("<iframe>").attr('src', $(this).data('video_url')).attr('frameborder', '0');

		if($(this).data('video_harf') == "1") {
			$preview.attr('width', $(this).data('video_width')/2);
			$preview.attr('height', $(this).data('video_height')/2);
		} else {
			$preview.attr('width', $(this).data('video_width'));
			$preview.attr('height', $(this).data('video_height'));
		}

		if($(this).data('video_style')) {
			$preview.attr('style', $(this).data('video_style'));
		}

		var video_option = $(this).data('video_option');
		for (var key in video_option) {
			$preview.attr(key, video_option [key]);
		}

		$container.append($preview);

		if ($(this) && $(this).parent()) {
			$(this).replaceWith($container);
		} else {
			$("body").append($container);
		}
	});
});

// }}}

/*
 * Local Variables:
 * mode: javascript
 * coding: cp932
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: t
 * End:
 */
/* vim: set syn=javascript fenc=cp932 ai noet ts=4 sw=4 sts=4 fdm=marker: */
