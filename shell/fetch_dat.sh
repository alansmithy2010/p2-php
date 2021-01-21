#!/bin/bash -x
cd `dirname $0`
cd ../

php scripts/fetch-dat.php --mode fav
php scripts/fetch-dat.php --mode recent
php scripts/fetch-dat.php --mode res_hist



