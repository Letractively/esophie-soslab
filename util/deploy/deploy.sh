#!/bin/bash
WWWROOT=$1
REV=$2

URLREPO=http://esophie-soslab.googlecode.com/svn/trunk

echo '***************** DEPLOYING TO WEB ****************'
echo "WWW REV $REV -> $WWWROOT"

sudo rm -Rf $WWWROOT/rc
sudo -u www-data svn export --force -r $REV $URLREPO/www/sos $WWWROOT/rc/www
sudo -u www-data svn export --force -r $REV $URLREPO/util/sh $WWWROOT/rc/sh
sudo -u www-data touch $WWWROOT/rc/REV-$REV

echo '***************** DEPLOYMENT END   ****************'
