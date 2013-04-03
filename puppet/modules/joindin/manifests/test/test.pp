class joindin::test::test {

    # Install ant to build test suite
    package { 'ant':
      #require => Notify['running'],
    }

    # Install required PEAR modules for test suite
    package { 'php-pear':
      #require => Package['php'],
    }

    # Discover the phpunit pear channel first - must do this separately because 
    # there is no guarantee of which tool will install first
    exec { 'phpunit-channel':
        command => 'pear channel-discover pear.phpunit.de; true',
        require => Package['php-pear'],
    }

    # PHP DOM extension required by PHPUnit on CentOS
    package { 'php-xml':
      require => Package['php-pear'],
    }

    # Install test-suite tools
    exec { 'phpunit':
      creates => '/usr/bin/phpunit',
      command => 'pear install phpunit/PHPUnit',
      require => [
        Exec['phpunit-channel'],
        Package['php-xml'],
      ],
      before  => Notify['test'],
    }

    exec { 'phploc':
      creates => '/usr/bin/phploc',
      command => 'pear channel-discover components.ez.no; true && /
                  pear channel-discover pear.netpirates.net; true && /
                  pear channel-discover pear.symfony.com; true && /
                  pear install phpunit/phploc',
      require => Exec['phpunit-channel'],
      before  => Notify['test'],
    }

    exec { 'phpcpd':
      creates => '/usr/bin/phpcpd',
      command => 'pear install phpunit/phpcpd',
      require => Exec['phpunit-channel'],
      before  => Notify['test'],
    }

    exec { 'pdepend':
      creates => '/usr/bin/pdepend',
      command => 'pear channel-discover pear.pdepend.org; true && \
                  pear install pdepend/PHP_Depend-beta',
      require => [
        Package['php-xml'],
        Package['php-pear']
      ]
    }

    exec { 'phpmd':
      creates => '/usr/bin/phpmd',
      command => 'pear channel-discover pear.phpmd.org; true && \
                  pear install phpmd/PHP_PMD',
      require => Exec['pdepend'],
      before  => Notify['test'],
    }

    exec { 'phing':
      creates => '/usr/bin/phing',
      command => 'pear channel-discover pear.phing.info; true && \
                  pear install phing/phing',
      require => Package['php-pear'],
      before  => Notify['test'],
    }

    package { 'graphviz': }

    exec { 'phpdoc':
      creates => '/usr/bin/phpdoc',
      command => 'pear channel-discover pear.phpdoc.org; true && \
                  pear install phpdoc/phpDocumentor-alpha',
      require => [
        Package['php-pear'],
        Package['graphviz'],
      ],
      before  => Notify['test'],
    }

    exec { 'phpcs':
      creates => '/usr/bin/phpcs',
      command => 'pear install PHP_CodeSniffer',
      require => Package['php-pear'],
      before  => Notify['test'],
    }

    # Announce test-suite
    notify { 'test':
      message => 'Test-suite ready - run in VM with `cd /vagrant && phing',
    }
}
