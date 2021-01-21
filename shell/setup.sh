#!/bin/bash -x
cd `dirname $0`
cd ../

curl -O https://getcomposer.org/download/1.10.19/composer.phar || exit 1
chmod +x composer.phar || exit 1

./composer.phar install || exit 1

chmod 0777 data/* rep2/ic

php scripts/p2cmd.php check


