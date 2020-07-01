<?php
/**
 * ImageCache2 - �摜�̃_�E�����[�h�E�T���l�C���쐬
 */

// {{{ p2��{�ݒ�ǂݍ���&�F��

require_once __DIR__ . '/../init.php';
require_once P2EX_LIB_DIR . '/ImageCache2/bootstrap.php';

$_login->authorize();

if (!$_conf['expack.ic2.enabled']) {
    p2die('ImageCache2�͖����ł��B', 'conf/conf_admin_ex.inc.php �̐ݒ��ς��Ă��������B');
}

// }}}
// {{{ �摜�����E�o�͗p�ϐ��ݒ�

$url = $_GET['url'];
$info_key_type = 'url';
$info_key_value = $url;

$icdb = new ImageCache2_DataObject_Images();
$thumbnailer = new ImageCache2_Thumbnailer(ImageCache2_Thumbnailer::SIZE_DEFAULT);
if (preg_match('/^' . preg_quote($thumbnailer->sourceuri, '/') . '/', $url) && file_exists($url)) {
    $info = getimagesize($url);
    $x = $info[0];
    $y = $info[1];
    $info_key_type = 'md5';
    $info_key_value = preg_replace('/^\\d+_([0-9a-f]+)\\..*/', '\\1', basename($url));
} elseif (preg_match('{(?:[\w.]*/)?ic2\.php\?(?:.*&)?ur[il]=([^&]+)(?:&|$)}', $url, $m)) {
    $url = rawurldecode($m[1]);
    if ($icdb->get($url)) {
        $url = $thumbnailer->srcUrl($icdb->size, $icdb->md5, $icdb->mime);
        $x = (int)$icdb->width;
        $y = (int)$icdb->height;
    } else {
        $x = 0;
        $y = 0;
    }
    $info_key_type = 'id';
    $info_key_value = $icdb->id;
} else {
    // �O�����ăL���b�V�����ꂽ�摜��\������̂ŁA�����ɂ͗��Ȃ��͂�
    $x = 0;
    $y = 0;
}

$info_key_value = p2h($info_key_value);

$alt = p2h(basename($url));

$autofit = '';
if ($x && $y) {
    if ($_conf['expack.ic2.fitimage'] == 1) {
        $autofit = 'contract';
    } elseif ($_conf['expack.ic2.fitimage'] == 2) {
        $autofit = 'width';
    } elseif ($_conf['expack.ic2.fitimage'] == 3) {
        $autofit = 'height';
    }
}

// }}}
// {{{ HTML�o��

P2Util::header_nocache();
echo $_conf['doctype'];
echo <<<EOF
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    {$_conf['extra_headers_ht']}
    <title>fitImage - {$alt}</title>
    <link rel="stylesheet" type="text/css" href="css.php?css=fitimage&amp;skin={$skin_en}">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <script type="text/javascript" src="js/basic.js?{$_conf['p2_version_id']}"></script>
    <script type="text/javascript" src="js/json2.js?{$_conf['p2_version_id']}"></script>
    <script type="text/javascript" src="js/ic2_getinfo.js?{$_conf['p2_version_id']}"></script>
    <script type="text/javascript" src="js/fitimage.js?{$_conf['p2_version_id']}"></script>
</head>
<body onload="fiSetup({$x},{$y},'{$autofit}','{$info_key_type}','{$info_key_value}')">
<div id="btn">
    <input type="hidden" id="fi_id" value="">
    <!-- <input type="text" id="fi_memo" size="50" value=""><br> -->
    <!-- <img id="fi_fit_xy" src="img/fi_full.gif" width="15" height="15" alt="XY"> -->
    <img id="fi_fit_y" src="img/fi_height.gif" width="15" height="15" alt="Y">
    <img id="fi_fit_x" src="img/fi_width.gif" width="15" height="15" alt="X">
    &nbsp;
    <span id="fi_stars"><img src="img/sn0.png" width="16" height="16" alt="-1"><img src="img/sz1.png" width="10" height="16" alt="0"><img src="img/s0.png" width="16" height="16" alt="1"><img src="img/s0.png" width="16" height="16" alt="2"><img src="img/s0.png" width="16" height="16" alt="3"><img src="img/s0.png" width="16" height="16" alt="4"><img src="img/s0.png" width="16" height="16" alt="5"></span>
</div>
<div id="pct"><img id="picture" src="{$url}" width="{$x}" height="{$y}" alt="{$alt}"></div>
</body>
</html>
EOF;

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: cp932
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: nil
 * End:
 */
// vim: set syn=php fenc=cp932 ai et ts=4 sw=4 sts=4 fdm=marker:
