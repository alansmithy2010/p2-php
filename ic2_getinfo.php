<?php
/* vim: set fileencoding=cp932 ai et ts=4 sw=4 sts=4 fdm=marker: */
/* mi: charset=Shift_JIS */

/* ImageCache2 - 画像のID or URLから情報を取得する */

// {{{ p2基本設定読み込み&認証

require_once 'conf/conf.inc.php';

$_login->authorize();

if (!$_conf['expack.ic2.enabled']) {
    exit('<html><body><p>ImageCache2は無効です。<br>conf/conf_admin_ex.inc.php の設定を変えてください。</p></body></html>');
}

// }}}
// {{{ HTTPヘッダとXML宣言

P2Util::header_nocache();
header('Content-Type: text/html; charset=UTF-8');

// }}}
// {{{ 初期化

// パラメータを検証
if (!isset($_GET['id']) && !isset($_GET['url']) && !isset($_GET['md5'])) {
    echo '-1';
    exit;
}

// ライブラリ読み込み
require_once 'PEAR.php';
require_once 'DB.php';
require_once 'DB/DataObject.php';
require_once P2EX_LIB_DIR . '/ic2/loadconfig.inc.php';
require_once P2EX_LIB_DIR . '/ic2/database.class.php';
require_once P2EX_LIB_DIR . '/ic2/db_images.class.php';

// }}}
// {{{ execute

$finder = new IC2DB_Images;
if (isset($_GET['id'])) {
    $finder->whereAdd(sprintf('id=%d', (int)$_GET['id']));
} elseif (isset($_GET['url'])) {
    $finder->whereAddQuoted('uri', '=', (string)$_GET['url']);
} else {
    $finder->whereAddQuoted('md5', '=', (string)$_GET['md5']);
}
if ($finder->find(1)) {
    printf('%d,%d,%d,%d,%d,%s',
           $finder->id, $finder->width, $finder->height,
           $finder->size, $finder->rank, $finder->memo
           );
} else {
    echo '-1';
}
exit;

// }}}
