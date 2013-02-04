#!/bin/bash
REPO=$1
cd $REPO

if [ $# -eq 2 ]
then
        REV=$2
else
        REV=$(svn info |grep Revision: |cut -c11-)
fi

WWWROOT=/var/www/.staging/sos/rc

echo '***************** DEPLOYING TO WEB ****************'
echo "SOS REV $REV -> $WWWROOT"

sudo rm -Rf $WWWROOT
sudo -u sos svn export --force -r $REV http://esophie-soslab.googlecode.com/svn/trunk/www/sos $WWWROOT
sudo -u sos touch $WWWROOT/REV$REV

echo '***************** DEPLOYMENT END   ****************'
