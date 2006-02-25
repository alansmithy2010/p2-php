<?php
// p2 - レス書き込みフォームの機能読み込み

$fake_time = -10; // time を10分前に偽装
$time = time() - 9*60*60;
$time = $time + $fake_time * 60;

$csrfid = P2Util::getCsrfId();

$htm['disable_js'] = <<<EOP
<script type="text/javascript">
<!--
// Thanks naoya <http://d.hatena.ne.jp/naoya/20050804/1123152230>

function isNetFront() {
  var ua = navigator.userAgent;
  if (ua.indexOf("NetFront") != -1 || ua.indexOf("AVEFront/") != -1 || ua.indexOf("AVE-Front/") != -1) {
    return true;
  } else {
    return false;
  }
}

function disableSubmit(form) {

  // 2006/02/15 NetFrontとは相性が悪く固まるらしいので抜ける
  if (isNetFront()) {
    return;
  }
  
  var elements = form.elements;
  for (var i = 0; i < elements.length; i++) {
    if (elements[i].type == 'submit') {
      elements[i].disabled = true;
    }
  }
}

function setHiddenValue(button) {
  
  // 2006/02/15 NetFrontとは相性が悪く固まるらしいので抜ける
  if (isNetFront()) {
    return;
  }
  
  if (button.name) {
    var q = document.createElement('input');
    q.type = 'hidden';
    q.name = button.name;
    q.value = button.value;
    button.form.appendChild(q);
  }
}

//-->
</script>\n
EOP;

// {{{ key.idxから名前とメールを読込み

if ($lines = @file($key_idx)) {
    $line = explode('<>', rtrim($lines[0]));
    $hd['FROM'] = htmlspecialchars($line[7], ENT_QUOTES);
    $hd['mail'] = htmlspecialchars($line[8], ENT_QUOTES);
}

// }}}

// 前回のPOST失敗があれば呼び出し
$failed_post_file = P2Util::getFailedPostFilePath($host, $bbs, $key);
if ($cont_srd = DataPhp::getDataPhpCont($failed_post_file)) {
    $last_posted = unserialize($cont_srd);
    
    // まとめてサニタイズ
    $last_posted = array_map(create_function('$n', 'return htmlspecialchars($n, ENT_QUOTES);'), $last_posted);
    //$addslashesS = create_function('$str', 'return str_replace("\'", "\\\'", $str);');
    //$last_posted = array_map($addslashesS, $last_posted);

    $hd['FROM'] = $last_posted['FROM'];
    $hd['mail'] = $last_posted['mail'];
    $hd['MESSAGE'] = $last_posted['MESSAGE'];
    $hd['subject'] = $last_posted['subject'];
}

// 空白はユーザ設定値に変換
$hd['FROM'] = ($hd['FROM'] == '') ? htmlspecialchars($_conf['my_FROM'], ENT_QUOTES) : $hd['FROM'];
$hd['mail'] = ($hd['mail'] == '') ? htmlspecialchars($_conf['my_mail'], ENT_QUOTES) : $hd['mail'];

// P2NULLは空白に変換
$hd['FROM'] = ($hd['FROM'] == 'P2NULL') ? '' : $hd['FROM'];
$hd['mail'] = ($hd['mail'] == 'P2NULL') ? '' : $hd['mail'];


// 参考 クラシック COLS='60' ROWS='8'
$mobile = &Net_UserAgent_Mobile::singleton();
// PC
if (empty($_conf['ktai'])) {
    $name_size_at = ' size="19"';
    $mail_size_at = ' size="19"';
    $msg_cols_at = ' cols="' . $STYLE['post_msg_cols'] . '"';
    $wrap = 'off';
// willcom
} elseif($mobile->isAirHPhone()) {
    $msg_cols_at = ' cols="' . $STYLE['post_msg_cols'] . '"';
    $wrap = 'soft';
// 携帯
} else {
    $STYLE['post_msg_rows'] = 5;
    $msg_cols_at = '';
    $wrap = 'soft';
}

// Be.2ch
if (P2Util::isHost2chs($host) and $_conf['be_2ch_code'] && $_conf['be_2ch_mail']) {
    $htm['be2ch'] = '<input type="submit" name="submit_beres" value="BEで書き込む" onClick="setHiddenValue(this);">';
}

// PC用 sage checkbox
if (!$_conf['ktai']) {
    $on_check_sage = 'onChange="checkSage();"';
    $sage_cb_ht = <<<EOP
<input id="sage" type="checkbox" onClick="mailSage();"><label for="sage">sage</label><br>
EOP;
}

// {{{ 2ch●書き込み

$htm['maru_post'] = '';
if (P2Util::isHost2chs($host) and file_exists($_conf['sid2ch_php'])) {
    $htm['maru_post'] = <<<EOP
<span title="2ch●IDの使用"><input id="maru" name="maru" type="checkbox" value="1"><label for="maru">●</label></span>
EOP;
}

// }}}
// {{{ソースコード補正用チェックボックス

$src_fix_ht = '';
if (!$_conf['ktai']) {
    if ($_conf['editor_srcfix'] == 1 ||
        ($_conf['editor_srcfix'] == 2 && preg_match('/pc\d\.2ch\.net/', $host))
    ) {
        $htm['src_fix'] = '<input type="checkbox" id="fix_source" name="fix_source" value="1"><label for="fix_source">ソースコード補正</label>';
    }
}

// }}}
// {{{ 定型文・アクティブモナー
/*
$htm['options'] = '';
$htm['options_k'] = '';

if (!$_conf['ktai']) {
    if ($_exconf['editor']['constant'] || $_exconf['editor']['with_aMona']) {
        @include (P2EX_LIBRARY_DIR . '/post_options.inc.php');
    }
} else {
    if ($_exconf['editor']['constant']) {
        @include (P2EX_LIBRARY_DIR . '/post_options_k.inc.php');
    }
}
*/
// }}}
// {{{ 書き込みプレビュー
/*
$htm['dpreview_onoff'] = '';
$htm['dpreview']  = '';
$htm['dpreview2'] = '';
$js['dp_setname'] = '';
$js['dp_setmail'] = '';
$js['dp_setmailsage'] = '';
$js['dp_setmsg'] = '';
$dp_name_at = '';
$dp_mail_at = '';
$dp_msg_at  = '';

if (!$_conf['ktai']) {
    if ($_exconf['editor']['dpreview']) {
        $dpreview_pos = ($_exconf['editor']['dpreview'] == 2) ? 'dpreview2' : 'dpreview';
        $htm[$dpreview_pos] = <<<EOP
<fieldset id="dpreview" style="display:none;">
<legend>Preview:</legend>
    <div>
        <span class="prvw_resnum">?</span>
        ：<span class="prvw_name"><b id="dp_name"></b><span id="dp_trip"></span></span>
        ：<span id="dp_mail" class="prvw_mail"></span>
        ：<span class="prvw_dateid"><span id="dp_date"></span> ID:<span id="dp_id">???</span></span>
    </div>
    <div id="dp_msg" class="prvw_msg"></div>
</fieldset>
EOP;
        $htm['dpreview_onoff'] = "<input type=\"button\" value=\"プレビュー\" onclick=\"DPInit();showHide('dpreview');\">";
        $js['dp_setname'] = 'DPSetName(this.value);';
        $js['dp_setmail'] = 'DPSetMail(this.value);';
        $js['dp_setmailsage'] = "DPSetMail(document.getElementById('mail').value);";
        $js['dp_setmsg']  = 'DPSetMsg(this.value);';

        $htm['sage_cb'] = <<<EOP
<input id="sage" type="checkbox" onclick="mailSage();{$js['dp_setmail']}"><label for="sage">sage</label>
EOP;

        $on_check_sage = '';
        $dp_name_at = " onkeyup=\"{$js['dp_setname']}\" onchange=\"{$js['dp_setname']}\"";
        $dp_mail_at = " onkeyup=\"{$js['dp_setmail']}\" onchange=\"checkSage();{$js['dp_setmail']}\"";
        $dp_msg_at  = " onkeyup=\"{$js['dp_setmsg']}\" onchange=\"{$js['dp_setmsg']}\"";
    }
}
*/
// }}}
// {{{ ここにレス

$htm['orig_msg'] = '';
if ((basename($_SERVER['SCRIPT_NAME']) == 'post_form.php' || !empty($_GET['inyou'])) && !empty($_GET['resnum'])) {
    $q_resnum = $_GET['resnum'];
    $hd['MESSAGE'] = "&gt;&gt;" . $q_resnum . "\r\n";
    if (!empty($_GET['inyou'])) {
        require_once (P2_LIBRARY_DIR . '/thread.class.php');
        require_once (P2_LIBRARY_DIR . '/threadread.class.php');
        $aThread = &new ThreadRead;
        $aThread->setThreadPathInfo($host, $bbs, $key);
        $aThread->readDat($aThread->keydat);
        $q_resar = $aThread->explodeDatLine($aThread->datlines[$q_resnum-1]);
        $q_resar = array_map('trim', $q_resar);
        $q_resar[3] = strip_tags($q_resar[3], '<br>');
        if ($_GET['inyou'] == 1 || $_GET['inyou'] == 3) {
            $hd['MESSAGE'] .= "&gt;";
            $hd['MESSAGE'] .= preg_replace("/ *<br> ?/","\r\n&gt;", $q_resar[3]);
            $hd['MESSAGE'] .= "\r\n";
        }
        if ($_GET['inyou'] == 2 || $_GET['inyou'] == 3) {
            $htm['orig_msg'] = <<<EOM
<fieldset id="original_msg">
<legend>Original Message:</legend>
    <div>
        <span class="prvw_resnum">{$q_resnum}</span>
        ：<b class="prvw_name">{$q_resar[0]}</b>
        ：<span class="prvw_mail">{$q_resar[1]}</span>
        ：<span class="prvw_dateid">{$q_resar[2]}</span>
    </div>
    <div class="prvw_msg">{$q_resar[3]}</div>
</fieldset>
EOM;
        }
    }
}

// }}}
// {{{ 本文が空のときやsageてないときに送信しようとすると注意する
/*
$onsubmit_ht = '';

if (!$_conf['ktai']) {
    if ($_conf['expack.editor.check_message'] || $_conf['expack.editor.check_sage']) {
        $_check_message = (int) $_conf['expack.editor.check_message'];
        $_check_sage = (int) $_conf['expack.editor.check_sage'];
        $onsubmit_ht = " onsubmit=\"return validateAll({$_check_message},{$_check_sage})\"";
    }
}
*/
// }}}

?>
