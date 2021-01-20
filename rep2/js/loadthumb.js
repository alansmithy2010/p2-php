/*
 * ImageCache2::Load Thumbnail
 */

// {{{ loadThumb()

/**
 * ��\����Ԃ̃T���l�C����ǂݍ���
 * 
 * �ǂݍ��ݔ���ɂ͒u���ΏۃI�u�W�F�N�g�̗L���𗘗p�B
 * �Ԃ�l�͉摜���ǂݍ��ݍς݂��ۂ��B
 *
 * @param String thumb_url
 * @param String thumb_id
 * @return void
 */
function loadThumb(thumb_url, thumb_id)
{
	var tmp_thumb = document.getElementById(thumb_id);
	if (!tmp_thumb) {
		return true;
	}

	var thumb = document.createElement('img');
	thumb.className = 'thumbnail';
	thumb.setAttribute('src', thumb_url);
	thumb.setAttribute('hspace', 4);
	thumb.setAttribute('vspace', 4);
	thumb.setAttribute('align', 'middle');

	tmp_thumb.parentNode.replaceChild(thumb, tmp_thumb);

	// IE�ł͓ǂݍ��݊������Ă��烊�T�C�Y���Ȃ��ƕςȋ����ɂȂ�̂�
	if (document.all) {
		thumb.onload = function() {
			autoImgSize(thumb_id);
		}
	// ���̑�
	} else {
		autoImgSize(thumb_id);
	}

	return false;
}

// }}}
// {{{ autoImgSize()

/**
 * �ǂݍ��݂����������T���l�C����{���̃T�C�Y�ŕ\������
 *
 * @param String|Image thumb
 * @return void
 */
function autoImgSize(thumb)
{
	if (typeof thumb === 'string') {
		thumb = document.getElementById(thumb);
	}
	var size = getImgNaturalSize(thumb);
	if (!size) {
		return;
	}

	thumb.style.width = size.width.toString() + 'px';
	thumb.style.height = size.height.toString() + 'px';
}

// }}}
// {{{ autoAdjustImgSize()

/**
 * �ǂݍ��݂����������T���l�C����{���̃T�C�Y�ŕ\������
 *
 * @param String|Image thumb
 * @param Number dpr device-pixel-ratio
 * @return void
 */
function autoAdjustImgSize(thumb, dpr)
{
	if (typeof thumb === 'string') {
		thumb = document.getElementById(thumb);
	}
	var size = getImgNaturalSize(thumb);
	if (!size) {
		return;
	}

	if (dpr > 1.0) {
		thumb.style.width = Math.round(size.width / dpr).toString() + 'px';
		thumb.style.height = Math.round(size.height / dpr).toString() + 'px';
	}
}

// }}}
// {{{ getImgNaturalSize()

/**
 * �摜�{���̃T�C�Y�𓾂�
 *
 * @link http://d.hatena.ne.jp/uupaa/20090602/1243933843
 *
 * @param Image img
 * @return Object { 'width': Number, 'height': Number }
 */
function getImgNaturalSize(img)
{
	var size = _getImgNaturalSize(img);
	if (size) {
		if (typeof size.width !== 'number') {
			size.width = parseInt(size.width);
		}
		if (typeof size.height !== 'number') {
			size.height = parseInt(size.height);
		}
	}
	return size;
}

function _getImgNaturalSize(img)
{
	if (typeof img.width === 'undefined') {
		return null;
	}

	// Firefox, Safari and Chrome
	if (typeof img.naturalWidth !== 'undefined') {
		if (img.naturalWidth == 0) {
			return null; // not loaded
		}
		return { 'width': img.naturalWidth, 'height': img.naturalHeight };
	}

	// IE
	if (typeof window.attachEvent !== 'undefined') {
		var w, h, run, mem;

		run = img.runtimeStyle;
		mem = { 'w': run.width, 'h': run.height }; // keep runtimeStyle

		run.width  = 'auto'; // override
		run.height = 'auto';
		w = img.width;
		h = img.height;
		run.width  = mem.w; // restore
		run.height = mem.h;

		return { 'width': w, 'height': h };
	}

	// Opera
	if (typeof window.opera !== 'undefined') {
		var w = 0, h = 0, mem, fn;

		fn = function () {
			w = img.width;
			h = img.height;
		};

		mem = { w: img.width, h: img.height };
		img.removeAttribute('width');
		img.addEventListener('DOMAttrModified', fn, false);
		img.removeAttribute('height');
		// call fn
		img.removeEventListener('DOMAttrModified', fn, false);
		img.width  = mem.w;
		img.height = mem.h;

		return { 'width': w, 'height': h };
	}

	return null;
}

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
