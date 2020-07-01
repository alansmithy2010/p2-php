#!/bin/bash
export IFS=$'\n'
git add --all || exit 1
git commit -a || exit 1
git push || exit 1
