#!/bin/bash

###
#
# Parse command line options for:-
#   -t = TARGET
#   -d = DBNAME (Database name)
#   -u = DBUSER (Database username)
#   -p = DBPASS (Database password)
#   -i = INITDB (Initialise & Seed the database)
#
#   Parsing code adapted from http://www.linux.com/archive/feed/118031
#
###
#TARGET=
#DBNAME=
#DBUSER=
#DBPASS=
while getopts 't:d:u:p:i' OPTION
do
    case $OPTION in
        t)  TARGET="$OPTARG"
            ;;
        d)  DBNAME="$OPTARG"
            ;;
        u)  DBUSER="$OPTARG"
            ;;
        p)  DBPASS="$OPTARG"
            ;;
        i)  INITDB=1
            ;;
    esac
done


###
#
# Check we have TARGET and DBNAME ether from the command line or environment
#
###
if [ -z $TARGET ]
then
	printf "Please specify TARGET in the environment or using the -t option, eg %s -t /var/www/joind.in\n" $(basename $0) >&2
	exit 1
fi

if [ -z $DBNAME ]
then
	printf "Please specify DBNAME in the environment or using the -d option, eg %s -d joindin\n" $(basename $0) >&2
	exit 1
fi


###
#
# Build the start of the database command allowing for optional username and
# password options
#
###
DBCMD="mysql"
if [ "$DBUSER" != "" ]
then
    DBCMD="$DBCMD -u $DBUSER"
fi

if [ "$DBPASS" != "" ]
then
    DBCMD="$DBCMD -p$DBPASS"
fi
DBCMD="$DBCMD $DBNAME"


###
#
# Get the current patch level from the database (if we are not initialising)
#
###
if [ "$INITDB" ]
then
    PATCH_LEVEL=0
else
    PATCH_LEVEL=$($DBCMD -e 'select max(patch_number) as num from patch_history' | grep -v num)

    if [ $? -gt 0 ]
    then
        echo Fail
        exit 1
    fi
fi


###
#
# Init DB, Apply patches & Seed as required.
#
###
PATCH_DIR=$TARGET/db
MAX_PATCH_LEVEL=$(ls $PATCH_DIR/patch*.sql | egrep -o 'patch[0-9]*.sql' | egrep -o '[0-9]+' | sort -n | tail -n 1)

# Init
if [ "$INITDB" ]
then
    echo -n "Initialising DB..."
    $($DBCMD < $PATCH_DIR/init_db.sql)
    $($DBCMD < $PATCH_DIR/init_data.sql)
    echo " Ok"
fi


for ((i=$(($PATCH_LEVEL + 1)); i <= $(($MAX_PATCH_LEVEL)); i++));
do
	echo -n "Applying patch $i... "
	$($DBCMD < $PATCH_DIR/patch$i.sql)
	if [ $? -gt 0 ]
	then
        if [ $i -ne 17 ]
        then
            echo Fail
            exit 1
        fi
    else
        echo Ok
	fi
done

# Seed
if [ "$INITDB" ]
then
    echo -n "Seeding DB... "
    $($DBCMD < $PATCH_DIR/seed.sql)
    $($DBCMD < $PATCH_DIR/seed_countries.sql)
    echo "Ok"
fi

echo Success
