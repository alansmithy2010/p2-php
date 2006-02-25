<?php
/* vim: set fileencoding=cp932 ai et ts=4 sw=4 sts=4 fdm=marker: */
/* mi: charset=Shift_JIS */
/**
 * tGrep お気にリストメニュー
 */

if ($_conf['ktai']) {
    tgrep_print_quick_list_k();
} else {
    tgrep_print_quick_list();
}

/**
 * お気にリストを読み込む
 */
function tgrep_read_quick_list()
{
    global $_conf;

    if (file_exists($_conf['expack.tgrep.quick_file'])) {
        return array_filter(array_map('trim', (array) @file($_conf['expack.tgrep.quick_file'])), 'strlen');
    }
    return array();
}

/**
 * PC用表示
 */
function tgrep_print_quick_list()
{
    global $_conf;

    $tgrep_quick_list = tgrep_read_quick_list();

    if (!defined('TGREP_SMARTLIST_PRINT_ONLY_LINKS')) {
        echo '<div class="menu_cate">' . "\n";
        echo '<b><a class="menu_cate" href="#" onclick="return showHide(\'c_tgrep_quick\');" target="_self">スレ一発検索</a></b>' . "\n";
        echo '[<a href="#" onclick="return tGrepAppendListInput(\'quick\',\'c_tgrep_quick\');" target="_self">＋</a>]' . "\n";
        echo '[<a href="#" onclick="return tGrepClearList(\'quick\',\'c_tgrep_quick\');" target="_self">空</a>]' . "\n";
        echo '<div class="itas" id="c_tgrep_quick">' . "\n";
    }
    if ($tgrep_quick_list) {
        foreach ($tgrep_quick_list as $tgrep_quick_query) {
            $tgrep_quick_query_en = rawurlencode($tgrep_quick_query);
            $tgrep_quick_query_ht = htmlspecialchars($tgrep_quick_query, ENT_QUOTES);
            echo '<a class="fav" href="#" onclick="return tGrepRemoveListItem(\'quick\',\'c_tgrep_quick\',\'' . $tgrep_quick_query_en . '\');" target="_self">★</a>' . "\n";
            echo '<a href="tgrepc.php?Q=' . $tgrep_quick_query_en . '">' . $tgrep_quick_query_ht . '</a><br>' . "\n";
        }
    } else {
        echo "（なし）\n";
    }
    if (!defined('TGREP_SMARTLIST_PRINT_ONLY_LINKS')) {
        echo "</div>\n</div>\n";
    }
}

/**
 * 携帯用表示
 */
function tgrep_print_quick_list_k()
{
    global $_conf;

    $tgrep_quick_list = tgrep_read_quick_list();

    echo '<h4>一発検索</h4>' . "\n";
    if ($tgrep_quick_list) {
        echo '<ul>' . "\n";
        foreach ($tgrep_quick_list as $tgrep_quick_query) {
            $tgrep_quick_query_en = rawurlencode($tgrep_quick_query);
            $tgrep_quick_query_ht = htmlspecialchars($tgrep_quick_query, ENT_QUOTES);
            echo '<li><a href="tgrepc.php?Q=' . $tgrep_quick_query_en . '">' . $tgrep_quick_query_ht . '</a>' . "\n";
            echo '<small>[<a href="tgrepctl.php?file=quick&amp;query=' . $tgrep_quick_query_en . '&amp;purge=true">削</a>]</small></li>' . "\n";
        }
        echo '</ul>' . "\n";
    } else {
        echo '<p>（なし）</p>' . "\n";
    }
    echo '<form method="get" action="tgrepctl.php">';
    echo '<input type="hidden" name="file" value="quick">';
    echo '<input type="text" name="query" value="">';
    echo '<input type="submit" value="追加">';
    echo '</form>' . "\n";
    if ($tgrep_quick_list) {
        echo '<p><a href="tgrepctl.php?file=quick&amp;clear=all">一発検索をｸﾘｱ</a></p>' . "\n";
    }
}

?>
