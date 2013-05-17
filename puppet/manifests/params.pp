# Tweak these variables to adjust your development environment:

class params {
    $host   = 'dev.joind.in'

    $port   = '80'              # Check `VagrantFile` for port forwarding settings

    # Database credentials. Test database will be setup with _test prefixed to these variables.
    $dbname = 'joindin'
    $dbuser = 'joindin'
    $dbpass = 'password'


    # True if phpmyadmin needs to be installed, false if not.
    $phpmyadmin = true

    # True if the test suite needs to be installed, false for faster provisioning with no tests
    $tests = false

    #$debug  = 'on'
}
