#!/bin/bash
WWWROOT=$1
REV=$2

URLREPO="http://esophie-soslab.googlecode.com/svn/trunk"

echo "***************** UPDATING DEPLOYMENT SCRIPTS *****"
echo "Deployment scripts REV $REV -> $WWWROOT/scripts/deploy-$REV"
if [ ! -f "$WWWROOT/scripts/deploy-$REV" ];
then
    sudo -u www-data svn export --force -r $REV $URLREPO/util/deploy $WWWROOT/scripts/deploy-$REV
else
    echo "Deployment script already up-to-date - no need to update."
fi
