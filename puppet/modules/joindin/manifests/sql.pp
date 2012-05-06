class joindin::sql {

    include mysql

    # Create and grant privileges to joindin database
    exec { 'create-db':
        unless  => "mysql -u${params::dbuser} -p${params::dbpass} ${params::dbname}",
        command => "mysql -e \"create database ${params::dbname}; \
                    grant all on ${params::dbname}.* \
                    to ${params::dbuser}@localhost identified by '${params::dbpass}';\"",
        require => Service['mysql'],
    }

}