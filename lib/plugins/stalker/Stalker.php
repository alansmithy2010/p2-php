<?php
/*
使用例:
$stalker = new Stalker();
$stalker->host = $host; // 指定しない場合は2chとみなす
$stalker->bbs  = $bbs;
if ($stalker->isEnabled()) {
    // bbs, date, idの指定が必要
    echo $stalker->getIDURL();
}
*/

class Stalker
{
    public $host;   // 板のホスト
    public $bbs;    // 板のディレクトリ名
    public $id;     // ID
    protected $enabled;

    /**
     * IDストーカーに対応しているか調べる
     * $boardがなければloadも実行される
     */
    public function isEnabled()
    {
        if ($this->host) {
            if (!P2HostMgr::isHost2chs($this->host)) {
                return false;
            }
        }
        return preg_match('/plus$/', $this->bbs);
    }

    /**
     * IDのURLを取得する
     */
    public function getIDURL()
    {
        return "http://stick.newsplus.jp/id.cgi?bbs={$this->bbs}&word=" . rawurlencode($this->id);
    }
}
