#!/bin/bash
REPO=$1
cd $REPO

if [ $# -eq 2 ]
then
        REV=$2
else
        REV=$(svn info |grep Revision: |cut -c11-)
fi

WWWROOT=/var/www/.staging/sos/r$REV

echo '***************** DEPLOYING TO WEB ****************'
echo "$REPO -> $WWWROOT"

rm -Rf $WWWROOT
mkdir $WWWROOT
sudo -u sos svn export -r $REV www/sos $WWWROOT

echo '***************** DEPLOYMENT END   ****************'
