#!/bin/bash
TARGETBASE=/var/www/joind.in
TARGET=${TARGETBASE}/${BUILD_NUMBER}
LAUNCHREF=remotes/magicmonkey/ci-tools

sg web -c "mkdir -p $TARGET ; git archive $LAUNCHREF | tar xC $TARGET && ln -s $TARGET $TARGETBASE/www.new && mv -Tf $TARGETBASE/www.new $TARGETBASE/www"

