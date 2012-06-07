class joindin::setup {

    # Setup another EPEL repo, the default one is disabled.
    file { "EpelRepo" :
        path   => "/etc/yum.repos.d/epel.repo",
        source => "puppet:///modules/joindin/epel.repo",
        owner  => "root",
        group  => "root",
    }

    # Install some default packages
    $default_packages = [ "strace", "sysstat", "git" ]
    package { $default_packages :
        ensure => present,
    }

}
