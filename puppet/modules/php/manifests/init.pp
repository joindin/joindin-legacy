# Class: php
#
# Installs Php 
#
# Usage:
# include php
#
class php  {

    include php::params

    package { "php":
        name   => "${php::params::packagename}",
        ensure => present,
    }

    package { "php-common":
        name   => "${php::params::packagenamecommon}",
        ensure => present,
    }

}
