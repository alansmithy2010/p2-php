<?php
// rep2 -  インデックスページ

include_once './conf/conf.inc.php';
require_once P2_LIB_DIR . '/filectl.class.php';

$_login->authorize(); //ユーザ認証

//=============================================================
// 前処理
//=============================================================
// アクセス拒否用の.htaccessをデータディレクトリに作成する
makeDenyHtaccess($_conf['pref_dir']);
makeDenyHtaccess($_conf['dat_dir']);
makeDenyHtaccess($_conf['idx_dir']);
if ($_conf['expack.ic2.enabled']) {
    include 'conf/conf_ic2.inc.php';
    makeImageCacheDenyHtaccess($_conf['expack.ic2.general.cachedir']);
}

//=============================================================

$me_url = P2Util::getMyUrl();
$me_dir_url = dirname($me_url);

if ($_conf['ktai']) {

    //=========================================================
    // 携帯用 インデックス
    //=========================================================
    // url指定があれば、そのままスレッド読みへ飛ばす
    if (!empty($_GET['url']) || !empty($_GET['nama_url'])) {
        header('Location: '.$me_dir_url.'/read.php?'.$_SERVER['QUERY_STRING']);
        exit;
    }
    if ($_conf['iphone'] && file_exists('./iui/iui.js')) {
        include './menu_i.php';
        exit;
    }
    include_once P2_LIB_DIR . '/index_print_k.inc.php';
    index_print_k();

} else {
    //=========================================
    // PC用 変数
    //=========================================
    $title_page = "title.php";

    if (!empty($_GET['url']) || !empty($_GET['nama_url'])) {
        $htm['read_page'] = "read.php?".$_SERVER['QUERY_STRING'];
    } else {
        if (!empty($_conf['first_page'])) {
            $htm['read_page'] = $_conf['first_page'];
        } else {
            $htm['read_page'] = 'first_cont.php';
        }
    }

    $sidebar = !empty($_GET['sidebar']);

    $ptitle = "rep2";
    //======================================================
    // PC用 HTMLプリント
    //======================================================
    P2Util::header_nocache();
     if ($_conf['doctype']) { 
        echo str_replace(
            array('Transitional', 'loose.dtd'),
            array('Frameset', 'frameset.dtd'),
            $_conf['doctype']);
    }
    echo <<<EOHEADER
<html lang="ja">
<head>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <title>{$ptitle}</title>
    <link href="favicon.ico" type="image/x-icon" rel="shortcut icon">
</head>
EOHEADER;

    if (!$sidebar) {
        echo <<<EOMENUFRAME
<frameset cols="{$_conf['frame_menu_width']},*" frameborder="1" border="1">
    <frame src="menu.php" name="menu" scrolling="auto">
EOMENUFRAME;
    }

    echo <<<EOMAINFRAME
    <frameset rows="{$_conf['frame_subject_width']},{$_conf['frame_read_width']}" frameborder="1" border="2">
        <frame src="{$title_page}" name="subject" scrolling="auto">
        <frame src="{$htm['read_page']}" name="read" scrolling="auto">
    </frameset>
EOMAINFRAME;

    if (!$sidebar) {
        echo '</frameset>'."\n";
    }

    echo '</html>';

}

//============================================================================
// 関数
//============================================================================
/**
 * ディレクトリに（アクセス拒否のための） .htaccess がなければ、自動で生成する
 */
function makeDenyHtaccess($dir)
{
    $hta = $dir . '/.htaccess';
    if (!file_exists($hta)) {
        $data = 'Order allow,deny'."\n".'Deny from all'."\n";
        FileCtl::file_write_contents($hta, $data);
    }
}
/**
 * ディレクトリに（画像以外アクセス拒否のための） .htaccess がなければ、自動で生成する
 */
function makeImageCacheDenyHtaccess($dir)
{
    $hta = $dir . '/.htaccess';
    $allow_pattern = '\.(gif|jpe?g|png)$';
    /*if (is_dir($dir) && !file_exists($hta)) {
        $data = <<<HTACCESS
Order allow,deny
<FilesMatch "{$allow_pattern}">
    Allow from all
</FilesMatch>
Deny from all\n
HTACCESS;
        FileCtl::file_write_contents($hta, $data);
    }*/
    // 書き込み権限の無い（≒ユーザが自分で作成した）.htaccess は消去しない
    if (is_dir($dir) && file_exists($hta) && is_writable($hta)) {
        unlink($hta);
    }
}
