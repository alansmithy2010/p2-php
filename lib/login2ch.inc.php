<?php
/**
 * rep2 - 2chログイン
 */

// {{{ login2ch()

/**
 * 2ch IDにログインする
 *
 * @return  string|false  成功したら2ch SIDを返す
 */
function login2ch()
{
    global $_conf;

    // 2ch●ID, PW設定を読み込む
    if ($array = P2Util::readIdPw2ch()) {
        list($login2chID, $login2chPW, $autoLogin2ch) = $array;

    } else {
        P2Util::pushInfoHtml("<p>p2 error: 2chログインのためのIDとパスワードを登録して下さい。[<a href=\"login2ch.php\" target=\"subject\">2chログイン管理</a>]</p>");
        return false;
    }

    // 浪人の有効性確認(要 ID / PW)
    if (!empty($login2chID) && !empty($login2chPW)) {
        P2Util::checkRoninExpiration();
    }

    $auth2ch_url= http_build_url(array(
        "scheme" => 'https',
        "host" => "2chv.tora3.net",
        "path" => "futen.cgi"));

    $dolib2ch = 'DOLIB/1.00';

    if($_conf['2chapi_use'] == 1) {
        if($_conf['2chapi_appname'] != "") {
            $x_2ch_ua = 'X-2ch-UA: ' . $_conf['2chapi_appname'];
        } else {
            P2Util::pushInfoHtml("<p>p2 error: 2chと通信するために必要な情報が設定されていません。</p>");
            return false;
        }
    } else {
        $x_2ch_ua = 'X-2ch-UA: ' . P2Commun::getP2UA(false,false);
    }

    try {
        $req = P2Commun::createHTTPRequest($auth2ch_url,HTTP_Request2::METHOD_POST);

        // ヘッダー
        $req->setHeader('User-Agent', $dolib2ch);
        $req->setHeader('X-2ch-UA', $x_2ch_ua);

        // POSTデータ
        $req->addPostParameter('ID', $login2chID);
        $req->addPostParameter('PW', $login2chPW);

        // POSTデータの送信
        $res = P2Commun::getHTTPResponse($req);

        $code = $res->getStatus();
        if ($code =! 200) {
            P2Util::pushInfoHtml("<p>p2 Error: HTTP Error({$code})</p>");
        } else {
            $body = $res->getBody();
        }

    } catch (Exception $e) {
        P2Util::pushInfoHtml("<p>p2 Error: ●の認証サーバに接続出来ませんでした。({$e->getMessage()})</p>");
    }

    // 接続失敗ならば
    if (empty($body)) {
        if (file_exists($_conf['idpw2ch_php'])) { unlink($_conf['idpw2ch_php']); }
        if (file_exists($_conf['sid2ch_php']))  { unlink($_conf['sid2ch_php']); }

        P2Util::pushInfoHtml('<p>p2 info: 2ちゃんねるへの●IDログインを行うには、PHPの<a href="'.
                P2Util::throughIme("http://www.php.net/manual/ja/ref.curl.php").
                '">cURL関数</a>又は<a href="'.
                P2Util::throughIme("http://www.php.net/manual/ja/ref.openssl.php").
                '">OpenSSL関数</a>が有効である必要があります。</p>');

        P2Util::pushInfoHtml("<p>p2 error: 2chログイン処理に失敗しました。{$curl_msg}</p>");
        return false;
    }

    $body = rtrim($body);

    // 分解
    if (preg_match('/SESSION-ID=(.+?):(.+)/', $body, $matches)) {
        $uaMona = $matches[1];
        $SID2ch = $matches[1] . ':' . $matches[2];
    } else {
        if (file_exists($_conf['sid2ch_php'])) { unlink($_conf['sid2ch_php']); }
        P2Util::pushInfoHtml("<p>p2 error: 2ch●ログイン接続に失敗しました。</p>");
        return false;
    }

    // 認証照合失敗なら
    if ($uaMona == 'ERROR') {
        file_exists($_conf['idpw2ch_php']) and unlink($_conf['idpw2ch_php']);
        file_exists($_conf['sid2ch_php']) and unlink($_conf['sid2ch_php']);
        P2Util::pushInfoHtml("<p>p2 error: 2ch●ログインのSESSION-IDの取得に失敗しました。IDとパスワードを確認の上、ログインし直して下さい。</p>");
        return false;
    }

    // SIDの記録保持
    $cont = sprintf('<?php $uaMona = %s; $SID2ch = %s;', var_export($uaMona, true), var_export($SID2ch, true));
    if (false === file_put_contents($_conf['sid2ch_php'], $cont, LOCK_EX)) {
        P2Util::pushInfoHtml("<p>p2 Error: {$_conf['sid2ch_php']} を保存できませんでした。ログイン登録失敗。</p>");
        return false;
    }

    return $SID2ch;
}

// }}}

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
