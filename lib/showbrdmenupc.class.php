<?php
/**
 * p2 - ボードメニューを表示する クラス
 */
class ShowBrdMenuPc{

    var $cate_id; // カテゴリーID

    function __construct()
    {
        $this->cate_id = 1;
    }

    /**
     * 板メニューをプリントする
     */
    function printBrdMenu($categories)
    {
        global $_conf, $_info_msg_ht;

        $menu_php_ht = htmlspecialchars((isset($GLOBALS['menu_php_self'])) ? $GLOBALS['menu_php_self'] : $_SERVER['SCRIPT_NAME']);

        if ($categories) {
            foreach ($categories as $cate) {
                if ($cate->num > 0) {
                    echo "<div class=\"menu_cate\">\n";
                    echo "  <b><a class=\"menu_cate\" href=\"javascript:void(0);\" onClick=\"showHide('c{$this->cate_id}');\" target=\"_self\">{$cate->name}</a></b>\n";
                    if ($cate->is_open or $cate->ita_match_num) {
                        echo "  <div class=\"itas\" id=\"c{$this->cate_id}\">\n";
                    } else {
                        echo "  <div class=\"itas_hide\" id=\"c{$this->cate_id}\">\n";
                    }
                    foreach ($cate->menuitas as $mita) {
                        echo "    <a href=\"{$menu_php_ht}?host={$mita->host}&amp;bbs={$mita->bbs}&amp;itaj_en={$mita->itaj_en}&amp;setfavita=1\" target=\"_self\" class=\"fav\">+</a> <a href=\"{$_conf['subject_php']}?host={$mita->host}&amp;bbs={$mita->bbs}&amp;itaj_en={$mita->itaj_en}\">{$mita->itaj_ht}</a><br>\n";
                    }
                    echo "  </div>\n";
                    echo "</div>\n";
                }
                $this->cate_id++;
            }
        }

    }

    /**
     * お気に板をプリントする
     */
    function print_favIta()
    {
        global $_conf, $matome_i, $STYLE;

        $menu_php_ht = htmlspecialchars((isset($GLOBALS['menu_php_self'])) ? $GLOBALS['menu_php_self'] : $_SERVER['SCRIPT_NAME']);

        echo <<<EOP
<div class="menu_cate">
  <b><a class="menu_cate" href="javascript:void(0);" onClick="showHide('c_favita');" target="_self">お気に板</a></b> [<a href="editfavita.php" target="subject">編集</a>]
EOP;
        // お気に板切り替え
        if ($_conf['expack.misc.multi_favs']) {
            echo "<br>\n";
            echo FavSetManager::makeFavSetSwitchElem('m_favita_set', 'お気に板', TRUE, "replaceMenuItem('c_favita', 'm_favita_set', this.options[this.selectedIndex].value);");
        }

        if ($_conf['expack.misc.multi_favs']) {
            $favset_title = FavSetManager::getFavSetPageTitleHt('m_favita_set', 'お気に板');
        } else {
            $favset_title = 'お気に板';
        }

        echo "  <div class=\"itas\" id=\"c_favita\">\n";

        if ($_conf['merge_favita']) {
            echo <<<EOP
    　 <a href="{$_conf['subject_php']}?spmode=merge_favita{$_conf['m_favita_set_at_a']}">{$favset_title} (まとめ)</a><br>\n
EOP;
        }

        $lines= @file($_conf['favita_path']); // favita読み込み

        if($lines){
            foreach ($lines as $l) {
                $l = rtrim($l);
                if (preg_match("/^\t?(.+)\t(.+)\t(.+)\$/", $l, $matches)) {
                    $itaj = rtrim($matches[3]);
                    $itaj_view = htmlspecialchars($itaj, ENT_QUOTES);
                    $itaj_en = rawurlencode(base64_encode($itaj));
                    $itaj_js = addslashes($itaj_view);

                    $p_htm['star'] = <<<EOP
<a href="{$menu_php_ht}?host={$matches[1]}&amp;bbs={$matches[2]}&amp;setfavita=0{$_conf['m_favita_set_at_a']}" target="_self" class="fav" title="「{$itaj_view}」をお気に板から外す" onclick="return window.confirm('「{$itaj_js}」をお気に板から外してよろしいですか？');">★</a>
EOP;
                    //  onClick="return confirmSetFavIta('{$itaj_ht}');"
                    // 新着数を表示する場合
                    if ($_conf['enable_menu_new'] && !empty($_GET['new'])) {
                        $matome_i++;
                        $host = $matches[1];
                        $bbs = $matches[2];
                        $spmode = "";
                        $shinchaku_num = 0;
                        $_newthre_num = 0;
                        $newthre_ht = "";
                        include("./subject_new.php");    // $shinchaku_num, $_newthre_num をセット
                        if ($shinchaku_num > 0) {
                            $class_newres_num = " class=\"newres_num\"";
                        } else {
                            $class_newres_num = " class=\"newres_num_zero\"";
                        }
                        if ($_newthre_num) {
                            $newthre_ht = "{$_newthre_num}";
                        }
                        echo <<<EOP
    {$p_htm['star']} <a href="{$_conf['subject_php']}?host={$matches[1]}&amp;bbs={$matches[2]}&amp;itaj_en={$itaj_en}" onClick="chMenuColor({$matome_i});">{$itaj_view}</a> <span id="newthre{$matome_i}" class="newthre_num">{$newthre_ht}</span> (<a href="{$_conf['read_new_php']}?host={$matches[1]}&amp;bbs={$matches[2]}" target="read" id="un{$matome_i}" onClick="chUnColor({$matome_i});"{$class_newres_num}>{$shinchaku_num}</a>)<br>\n
EOP;

                    // 新着数を表示しない場合
                    } else {
                        echo <<<EOP
    {$p_htm['star']} <a href="{$_conf['subject_php']}?host={$matches[1]}&amp;bbs={$matches[2]}&amp;itaj_en={$itaj_en}">{$itaj_view}</a><br>\n
EOP;

                    }

                }

                flush();

            } // foreach

        // 空っぽなら
        } else {
            echo '　（空っぽ）';
        }

       echo "  </div>\n</div>\n";
    }

}
