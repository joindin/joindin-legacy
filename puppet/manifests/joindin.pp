node default {
    include apache
    include puppi
    include mysql
    include php
    include something::db
}


  define mysqldb( $user, $password ) {
    exec { "create-${name}-db":
      unless => "/usr/bin/mysql -u${user} -p${password} ${name}",
      command => "/usr/bin/mysql -uroot -p$mysql_password -e \"create database ${name}; grant all on ${name}.* to ${user}@localhost identified by '$password';\"",
      require => Service["mysqld"],
    }
  }

Exec {
    path => [ '/bin/', '/sbin/' , '/usr/bin/', '/usr/sbin/' ]
}

class { "apache": }
php::module { mysql: }

apache::vhost{ 'dev.joind.in':
	docroot	=>	'/vagrant/src',
	template => 	'/vagrant/templates/virtualhost/vhost.conf.erb',
}

class something::db {
  mysqldb { "joindin":
    user => 'joindin',
    password => 'password',
  }
}

exec { 'patch-db':
    command => "/vagrant/src/scripts/patchdb.sh -t /vagrant -d joindin -u joindin -p password -i",
    path => [ '/bin/', '/sbin/' , '/usr/bin/', '/usr/sbin/' ]
}

file { 'database-config':
    path => '/vagrant/src/system/application/config/database.php',
    source => '/vagrant/templates/database.php.erb',
}
file { 'application-config':
    path => '/vagrant/src/system/application/config/config.php',
    source => '/vagrant/templates/config.php.erb',
}
