#!/bin/bash -x
cd `dirname $0`

php scripts/p2cmd.php update || exit 1


