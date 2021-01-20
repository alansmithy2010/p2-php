<?php
/*
使用例:
$mimizun = new Mimizun();
$mimizun->host = $host; // 指定がない場合は2chとみなす
$mimizun->bbs  = $bbs;
$mimizun->from  = 0;    // 1:ライブ, 2:過去, それ以外:全て
if ($mimizun->isEnabled()) {
    $mimizun->id = $id;
    echo $mimizun->getIDURL();
}

loadBoard関係は一度も実行されなければisEnableでも呼び出されるので、特に実行する必要はない。
再取得したい場合に使うこと。
loadAll…全ての板リストを読み込む
loadLive…ライブスレッドの板リストを読み込む
loadKako…過去ログの板リストを読み込む
isEnable…そのhost, bbsがfromで対応しているかチェック
getIDURL…そのIDのみみずんID検索のURLを返す
*/
class Mimizun
{
    public $liveBoards; //ライブスレッドの対応板
    public $kakoBoards; //過去ログの対応板
    public $host;       // ホスト(なるべく指定すること)
    public $bbs;        // 板のディレクトリ名 (必ず指定すること)
    public $from = 0;   // 0:全て, 1:ライブ, 2:過去
    public $id;         // ID (ID検索で必要)
    protected $enabled;

    /**
     * みみずん対応板を読み込む
     */
    public function load($type)
    {
        global $_conf;

        // 対応板の取得
        switch($type) {
            case 0:
                $url = 'http://mimizun.com/search/2chlive.html';
                $path = $_conf['cache_dir'] . '/search.mimizun.com/2chlive.html';
                $match = '{<input type="checkbox" name="idxname" value="_(.+?)">}';
                break;
            case 1:
                $url = 'http://mimizun.com/search/2ch.html';
                $path = $_conf['cache_dir'] . '/search.mimizun.com/2ch.html';
                $match = '{<input type="checkbox" name="idxname" value="(.+?)">}';
                break;
        }
        // キャッシュ用ディレクトリが無ければ作成
        FileCtl::mkdir_for($path);
        // メニューのキャッシュ時間の10倍キャッシュ
        P2Commun::fileDownload($url, $path, $_conf['menu_dl_interval'] * 36000);
        $file = @file_get_contents($path);
        preg_match_all($match, $file, $boards);
        return $boards[1];
    }

    /**
     * みみずん対応板(ライブ)を読み込む
     */
    public function loadLive()
    {
        $this->liveBoards = $this->load(0);
    }

    /**
     * みみずん対応板(過去ログ)を読み込む
     */
    public function loadKako()
    {
        $this->kakoBoards = $this->load(1);
    }

    /**
     * みみずん対応板を読み込む
     */
    public function loadAll()
    {
        $this->loadLive();
        $this->loadKako();
    }

    /**
     * みみずん検索に対応しているか調べる
     */
    public function isEnabled()
    {
        // hostがセットされてないかもしれないので
        // (セットされていなければ2chとみなす)
        if ($this->host) {
            // まちBBSならfalse
            if (P2HostMgr::isHostMachiBbs($this->host)) {
                return false;
            }
            // 2chでなければfalse
            if (!P2HostMgr::isHost2chs($this->host)) {
                return false;
            }
        }
        $this->enabled = true;
        return $this->enabled;
    }

    /**
     * みみずんID検索のURLを返す
     */
    public function getIDURL()
    {
        return "http://mimizun.com/search/perl/idsearch.pl?board={$this->bbs}&id={$this->id}";
    }

}
