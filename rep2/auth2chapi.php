<?php
/**
 * rep2 - 2ch API ���O�C���Ǘ�
 */

require_once __DIR__ . '/../init.php';

$_login->authorize(); // ���[�U�F��

//================================================================
// �ϐ�
//================================================================
global $_conf;
$AppKey = $_conf['2chapi_appkey'];
$AppName = $_conf['2chapi_appname'];
$HMKey  = $_conf['2chapi_hmkey'];

//==============================================================
// 2ch���O�C������
//==============================================================
if (isset($_GET['login2chapi'])) {
    if ($_GET['login2chapi'] == "in") {
        require_once P2_LIB_DIR . '/auth2chapi.inc.php';
        authenticate_2chapi();
    } elseif ($_GET['login2chapi'] == "out") {
        if (file_exists($_conf['sid2chapi_php'])) {
            unlink($_conf['sid2chapi_php']);
        }
    }
}

//================================================================
// �w�b�_
//================================================================
if ($_conf['ktai']) {
    $login_st = "۸޲�";
    $logout_st = "۸ޱ��";
    $password_st = "�߽ܰ��";
} else {
    $login_st = "���O�C��";
    $logout_st = "���O�A�E�g";
    $password_st = "�p�X���[�h";
}

if (file_exists($_conf['sid2chapi_php'])) { // 2ch����������
    $ptitle = "��2ch API �F�؊Ǘ�";
} else {
    $ptitle = "2ch API �F�؊Ǘ�";
}

P2Util::header_nocache();
echo $_conf['doctype'];
echo <<<EOP
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    {$_conf['extra_headers_ht']}
    <title>{$ptitle}</title>\n
EOP;

if (!$_conf['ktai']) {
    echo <<<EOP
    <link rel="stylesheet" type="text/css" href="css.php?css=style&amp;skin={$skin_en}">
    <link rel="stylesheet" type="text/css" href="css.php?css=login2ch&amp;skin={$skin_en}">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <script type="text/javascript" src="js/basic.js?{$_conf['p2_version_id']}"></script>\n
EOP;
}

$body_at = ($_conf['ktai']) ? $_conf['k_colors'] : ' onload="setWinTitle();"';

if (!$_conf['ktai']) {
    echo <<<EOP
<p id="pan_menu"><a href="setting.php">���O�C���Ǘ�</a> &gt; {$ptitle}</p>
EOP;
}

P2Util::printInfoHtml();

//================================================================
// 2ch API ���O�C���t�H�[��
//================================================================

// ���O�C�����Ȃ�
if (file_exists($_conf['sid2chapi_php'])) {
    $idsub_str = "�ĔF�؂���";
    $form_now_log = <<<EOFORM
    <form id="form_logout" method="GET" action="{$_SERVER['SCRIPT_NAME']}" target="_self">
        ���݁A2�����˂� API �F�ؒ��ł�
        {$_conf['k_input_ht']}
        <input type="hidden" name="login2chapi" value="out">
        <input type="submit" name="submit" value="�F�؉�������">
    </form>\n
EOFORM;

} else {
    $idsub_str = "�F�؂���";
    $form_now_log = "2�����˂� API �F�؂��Ă��܂���</p>";
}

if ($autoLogin2ch) {
    $autoLogin2ch_checked = ' checked="checked"';
} else {
    $autoLogin2ch_checked = '';
}

$tora3_url = "http://2ch.tora3.net/";
$tora3_url_r = P2Util::throughIme($tora3_url);

if (!$_conf['ktai']) {
    $id_input_size_at = " size=\"30\"";
    $pass_input_size_at = " size=\"24\"";
}

// �v�����g =================================
echo "<div id=\"login_status\">";
echo $form_now_log;
echo "</div>";

if ($_conf['ktai']) {
    echo "<hr>";
}

echo <<<EOFORM
<form id="login_with_id" method="GET" action="{$_SERVER['SCRIPT_NAME']}" target="_self">
    {$_conf['k_input_ht']}
    AppKey: "{$AppKey}"<br>
    HMKey: "{$HMKey}"<br>
    AppName: "{$AppName}"<br>
    �F�؏���<a href="edit_conf_user.php">���[�U�ݒ�ҏW</a>�ŕύX�ł��܂��B<br>
    <input type="hidden" name="login2chapi" value="in">
    <input type="submit" name="submit" value="{$idsub_str}">
</form>\n
EOFORM;

if ($_conf['ktai']) {
    echo "<hr>";
}

//================================================================
// �t�b�^HTML�\��
//================================================================

if ($_conf['ktai']) {
    echo "<hr><div class=\"center\">{$_conf['k_to_index_ht']}</div>";
}

echo '</body></html>';

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
