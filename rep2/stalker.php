<?php
/*
IDストーカーにURLを渡す。

引数:
host:$hostを渡す (任意)
bbs:$bbsを渡す
id:IDを渡す
img:何か含まれていれば画像を表示
*/

require_once __DIR__ . '/../init.php';

$_login->authorize(); //ユーザ認証

require_once P2_PLUGIN_DIR . '/stalker/Stalker.php';

$stalker = new Stalker();
$stalker->host = $_GET['host'];
$stalker->bbs  = $_GET['bbs'];
// 画像を表示する場合
if ($_GET['img']) {
    if ($stalker->isEnabled()) {
        header("Content-Type: image/png");
        readfile(P2_PLUGIN_DIR . '/stalker/stalker.png');
    } else {
        header("Content-Type: image/gif");
        readfile('./img/spacer.gif');
    }
    exit;
} else {
    if ($stalker->isEnabled()) {
        $id = null;
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
        } elseif (!empty($_GET['key']) && !empty($_GET['resnum'])) {
            $aThread = new ThreadRead();
            $aThread->setThreadPathInfo($_GET['host'], $_GET['bbs'], $_GET['key']);
            $aThread->readDat();
            $resnum = $_GET['resnum'];
            if (isset($aThread->datlines[$resnum - 1])) {
                $ares = $aThread->datlines[$resnum - 1];
                $resar = $aThread->explodeDatLine($ares);
                $m = array();
                if (preg_match('<(ID: ?| )([0-9A-Za-z/.+]{8,11})(?=[^0-9A-Za-z/.+]|$)>', $resar[2], $m)) {
                    $id = $m[2];
                }
            }
        }
        if ($id) {
            $stalker->id = $id;
        } else {
            P2Util::printSimpleHtml('何かが足りないようです。');
            exit();
        }
        $_ime = new P2Ime();
        header('Location: ' . $_ime->through($stalker->getIDURL(), null, false));
    } else {
        P2Util::printSimpleHtml('この板は対応していません。');
    }
}
