<?php
/*
replaceLinkToHTML(url, src) ���C���֐�
save(array)                 �f�[�^��ۑ�
load()                      �f�[�^��ǂݍ���ŕԂ�(�����I�Ɏ��s�����)
clear()                     �f�[�^���폜
*/

require_once __DIR__ . '/WikiPluginCtlBase.php';

class LinkPluginCtl extends WikiPluginCtlBase
{
    protected $filename = 'p2_plugin_link.txt';
    protected $data = array();

    public function clear()
    {
        global $_conf;

        $path = $_conf['pref_dir'] . '/' . $this->filename;

        return @unlink($path);
    }

    public function load()
    {
        global $_conf;

        $lines = array();
        $path = $_conf['pref_dir'] . '/' . $this->filename;
        if ($lines = @file($path)) {
            foreach ($lines as $l) {
                $lar = explode("\t", trim($l));
                if (strlen($lar[0]) == 0) {
                    continue;
                }
                $ar = array(
                    'match'   => $lar[0], // �Ώە�����
                    'replace' => $lar[1], // �u��������
                );

                $this->data[] = $ar;
            }
        }

        return $this->data;
    }

    /*
    $data[$i]['match']       Match
    $data[$i]['replace']     Replace
    $data[$i]['del']         �폜
    */
    public function save($data)
    {
        global $_conf;

        $path = $_conf['pref_dir'] . '/' . $this->filename;

        $newdata = '';

        foreach ($data as $na_info) {
            $a[0] = strtr(trim($na_info['match'], "\t\r\n"), "\t\r\n", "   ");
            $a[1] = strtr(trim($na_info['replace'], "\t\r\n"), "\t\r\n", "   ");
            if ($na_info['del'] || ($a[0] === '' || $a[1] === '')) {
                continue;
            }
            $newdata .= implode("\t", $a) . "\n";
        }

        return FileCtl::file_write_contents($path, $newdata);
    }

    public function replaceLinkToHTML($url, $str)
    {
        global $_conf;

        $this->setup();

        $src = false;
        foreach ($this->data as $v) {
            // �u�������s
            $temp = @preg_replace ('{'.$v['match'].'}', $v['replace'], $url, -1, $count);
            // �u�����ꂽ�ꍇ
            if ($count) {
                $src = $temp;
                $replace_pairs = array();
                if (strstr($v['replace'], '$ime_url')) {
                    $replace_pairs['$ime_url'] = P2Util::throughIme($url);
                }
                if (strstr($v['replace'], '$str')) {
                    $replace_pairs['$str'] = $str;
                }
                if (strstr($v['replace'], '$atag')) {
                    // ime
                    if ($_conf['through_ime']) {
                        $link_url = P2Util::throughIme($url);
                    } else {
                        $link_url = $url;
                    }
                    // HTML�|�b�v�A�b�v(PC�̎�����)
                    if ($_conf['iframe_popup'] && !$_conf['ktai']) {
                        // *pm �w��̏ꍇ�̂݁A���ʂɎ蓮�]���w���ǉ�����
                        if (substr($_conf['through_ime'], -2) == 'pm') {
                            $pop_url = P2Util::throughIme($url, -1);
                        } else {
                            $pop_url = $link_url;
                        }
                        $replace_pairs['$atag'] = ShowThreadPc::iframePopup(array($link_url, $pop_url), $str, $_conf['ext_win_target_at']);
                    } else {
                        $replace_pairs['$atag'] = "<a href=\"{$link_url}\"{$_conf['ext_win_target_at']}>{$str}</a>";
                    }
                }
                // �ϐ���W�J
                $src = strtr($src, $replace_pairs);
                break;
            }
        }
        return $src;
    }

}
