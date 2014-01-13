#!/bin/bash

if [ -z $TARGETBASE ]
then
	echo "Please specify TARGETBASE in the environment, eg /var/www/joind.in"
	exit 1
fi
#TARGETBASE=/var/www/joind.in

if [ -z $DBNAME ]
then
	echo "Please specify DBNAME in the environment, eg joindin"
	exit 1
fi
#DBNAME=joindin

TARGET=${TARGETBASE}/${BUILD_NUMBER}
export TARGET


if [ -z $GITHUB_REPO ]
then
	GITHUB_REPO=joind.in
fi

if [ -z $GITHUB_USER ]
then
	GITHUB_USER=joindin
fi

if [ -z $BRANCH ]
then
	BRANCH=master
fi
LAUNCHREF=remotes/deployremote/$BRANCH

sg web -c "
mkdir -p $TARGET \
 ; git remote set-url deployremote https://github.com/$GITHUB_USER/$GITHUB_REPO.git \
&& git fetch deployremote \
&& git archive $LAUNCHREF | tar xC $TARGET \
&& (echo $TARGET ; echo $LAUNCHREF) > $TARGET/src/release.txt \
&& ln -s $TARGETBASE/config.php $TARGET/src/system/application/config/config.php \
&& ln -s $TARGETBASE/database.php $TARGET/src/system/application/config/database.php \
&& ln -s $TARGETBASE/tmp/csv $TARGET/src/inc/tmp \
&& mv $TARGET/src/inc/img/event_icons $TARGET/src/inc/img/event_icons.removed \
&& ln -s $TARGETBASE/tmp/event_icons $TARGET/src/inc/img/event_icons \
&& ln -s $TARGET $TARGETBASE/www.new \
&& mv -Tf $TARGETBASE/www.new $TARGETBASE/www
"

