#!/bin/bash -x
cd `dirname $0`
cd ../

php scripts/p2cmd.php update || exit 1


