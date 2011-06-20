#!/bin/bash

DBDIR=$1
DBNAME=$2
DBUSER=$3
DBPASSWORD=$4
SAVE_DUMP=$5

if [ -z $DBDIR ]
then
    echo ""
    echo "************************************************************************"
    echo "* WARNING! This script will replace all tables in dbname with an empty *"
    echo "* joind.in database containing just the example seed data              *"
    echo "************************************************************************"
    echo ""
	echo "Usage: reset_db.sh path/to/joind.in/doc/db dbname dbuser dbpassword save_dump_for_next_time=0"
    echo ""
	exit 1
fi
if [ -z $DBNAME ]
then
	echo "Usage: reset_db.sh path/to/joind.in dbname dbuser dbpassword"
	exit 1
fi
if [ -z $DBUSER ]
then
	echo "Usage: reset_db.sh path/to/joind.in dbname dbuser dbpassword"
	exit 1
fi
if [ -z $DBPASSWORD ]
then
	echo "Usage: reset_db.sh path/to/joind.in dbname dbuser dbpassword"
	exit 1
fi
if [ -z $SAVE_DUMP ]
then
	SAVE_DUMP=0
fi


FILENAME=${DBDIR}/apiv2test_dump.sql

if [ -f $FILENAME ]
then
    echo "Restoring $FILENAME"
    mysql -u $DBUSER -p${DBPASSWORD} $DBNAME < $FILENAME

    PATCH_LEVEL=$(mysql -u $DBUSER -p${DBPASSWORD} $DBNAME -e 'select max(patch_number) as num from patch_history' | grep -v num)
    PATCH_LEVEL=$(($PATCH_LEVEL+1))
    MAX_PATCH_LEVEL=$(ls $DBDIR/patch*.sql | egrep -o 'patch[0-9]*.sql' | egrep -o '[0-9]+' | sort -n | tail -n 1)

    for (( i=$PATCH_LEVEL; i<=$MAX_PATCH_LEVEL; i++ ))
    do
        echo "Applying patch $i"
        mysql -u $DBUSER -p${DBPASSWORD} $DBNAME < $DBDIR/patch$i.sql
        if [ $? -gt 0 ]
        then
            echo Fail
            exit 1
        fi
    done
    exit 0
fi


echo "drop tables in $DBNAME"
(
    mysql --skip-column-names --silent -u $DBUSER -p${DBPASSWORD} $DBNAME -e 'show tables' 
) | while read TABLE
do
    mysql -u $DBUSER -p${DBPASSWORD} $DBNAME -e "DROP TABLE $TABLE"
done


echo "Applying init_db.sql"
mysql -u $DBUSER -p${DBPASSWORD} $DBNAME < $DBDIR/init_db.sql
if [ $? -gt 0 ]
then
	echo Fail
	exit 1
fi


echo "Applying init_data.sql"
mysql -u $DBUSER -p${DBPASSWORD} $DBNAME < $DBDIR/init_data.sql
if [ $? -gt 0 ]
then
	echo Fail
	exit 1
fi

# Apply patches 1 to 8 manually as patch 8 is where patch_history is created
for i in {1..8}
do
	echo "Applying patch $i"
	mysql -u $DBUSER -p${DBPASSWORD} $DBNAME < $DBDIR/patch$i.sql
	if [ $? -gt 0 ]
	then
		echo Fail
		exit 1
	fi
done


MAX_PATCH_LEVEL=$(ls $DBDIR/patch*.sql | egrep -o 'patch[0-9]*.sql' | egrep -o '[0-9]+' | sort -n | tail -n 1)

for (( i=9; i<=$MAX_PATCH_LEVEL; i++ ))
do
	echo "Applying patch $i"
	mysql -u $DBUSER -p${DBPASSWORD} $DBNAME < $DBDIR/patch$i.sql
	if [ $? -gt 0 ]
	then
		echo Fail
		exit 1
	fi
done

echo "Applying seed_countries.sql"
mysql -u $DBUSER -p${DBPASSWORD} $DBNAME < $DBDIR/seed_countries.sql
if [ $? -gt 0 ]
then
	echo Fail
	exit 1
fi

echo "Applying seed.sql"
mysql -u $DBUSER -p${DBPASSWORD} $DBNAME < $DBDIR/seed.sql
if [ $? -gt 0 ]
then
	echo Fail
	exit 1
fi

if [ $SAVE_DUMP -gt 0 ]
then
    mysqldump  -u $DBUSER -p${DBPASSWORD} $DBNAME > ${FILENAME}
fi

echo Success
exit 0

