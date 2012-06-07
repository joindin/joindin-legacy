class joindin::app {

    # Initialize database structure
    exec { 'patch-db':
        creates => '/tmp/.patched',
        command => "/vagrant/src/scripts/patchdb.sh \
                    -t /vagrant -d ${params::dbname} -u ${params::dbuser} -p ${params::dbpass} -i -p \
                    && touch /tmp/.patched",
        require => Exec['create-db'],
    }

    # Generate seed data
    exec { 'seed-data':
        creates => '/tmp/seed.sql',
        command => 'php /vagrant/doc/dbgen/generate.php > /tmp/seed.sql',
        require => Package['php'],
    }

    # Seed database
    exec { 'seed-db':
        creates => '/tmp/.seeded',
        command => "mysql ${params::dbname} < /tmp/seed.sql && touch /tmp/.seeded",
        require => [
                       Exec['patch-db'],
                       Exec['seed-data'],
                   ],
    }

    # Set database config for application
    file { 'database-config':
        path   => '/vagrant/src/system/application/config/database.php',
        content => template('joindin/database.php.erb'),
    }

    # Set database config for application
    file { 'api-database-config':
        path    => '/vagrant/src/api-v2/database.php',
        content => template('joindin/database.php.erb'),
    }

    # Set core config for application
    file { 'application-config':
        path    => '/vagrant/src/system/application/config/config.php',
        source  => '/vagrant/src/system/application/config/config.php.dist',
        replace => no,
    }

    # Create directory for user-generated content
    file { 'upload-directory':
        ensure  => directory,
        path    => '/tmp/ctokens',
        mode    => '0644',
        owner   => 'apache',
        group   => 'apache',
        require => Service['apache'],
    }

}