#!/bin/bash -x
cd `dirname $0`
cd ../

php scripts/fetch-subject-txt.php --mode fav
php scripts/fetch-subject-txt.php --mode recent
php scripts/fetch-subject-txt.php --mode res_hist



