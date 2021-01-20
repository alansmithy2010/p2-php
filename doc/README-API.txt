API対応パッチ README

●何？
あくまで非公式な実験機能です。
機能を使用するためには設定が必要になります。

2ch APIを使用したdatの取得に対応しています。

PHPがOpenSSLに対応している必要があります。

●設定方法
「設定管理」→「ユーザー設定編集」→「rep2基本設定」→「2ch API」から設定変更できます。

2chapi_use
「する」に設定した場合は2ch APIでdatの取得を行います。
dat取得以外のUser-Agentをrep2標準から2chapi_appnameを使用した物に変更します。
外部板のUser-Agentとdat取得ははrep2標準のままです。

2chapi_ssl.auth
2chapi_ssl.read
この設定はSSL通信がエラーとなる環境向けの設定です。
2chapi_use が「しない」の場合は無視されます。

2chapi_interval
2ch API 認証する間隔です。SessionIDがどれくらいの期間持つのか不明なためとりあえず1時間にしています。

2chapi_appkey
2chapi_hmkey
2ch APIの認証情報です。
変更した場合は再認証を行う必要があります。

2chapi_appname
2ch APIを使用するときに2chに送信するUser-Agentです。
「Hoge/1.00」の形式で指定します。
内部で「Monazilla/1.00 (Hoge/1.00)」のようなUser-Agentの一部として使用します。

●追加機能
「ログイン管理」に「2ch API認証管理」を追加しています。
2ch API認証状況の確認と認証・認証解除・再認証を行うことが出来ます。
通常はdat取得時に2chapi_intervalに設定した間隔で自動的に認証を行いますが、
2ch APIの認証情報を変更した場合は手動で再認証を行う必要があります。