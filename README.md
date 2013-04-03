# Joind.in (http://joind.in)

This is the source code for the Joind.in website - a resource set up to allow
events to get real-time feedback from those attending. It also gives speakers a
way to claim and track their presentations over time.

You can either install joind.in on an existing PHP platform, or use our vagrant setup.

## Quick Start - Using Vagrant

You can set up a development virtual machine running joind.in by following these simple instructions.

1. Install requirements. (Note: these are not required by joind.in itself, but are required for this quick start guide.)
   - VirtualBox (https://www.virtualbox.org/) (versions 4.0 and 4.1 are currently supported)
   - Ruby (http://www.ruby-lang.org/)
   - Vagrant (http://vagrantup.com/)

2. Clone repository to any location and fetch required submodules (containing Puppet manifests).

        git clone https://github.com/joindin/joind.in --recursive
        cd joind.in

or

        git clone https://github.com/joindin/joind.in && cd joind.in
        git submodule init
        git submodule update

3. Start the process by running Vagrant.

        vagrant up

4. Add hostname to /etc/hosts.
   If you are on Linux, run this:

        echo "\n127.0.0.1 dev.joind.in api.dev.joind.in" | sudo tee -a /etc/hosts

   If you are on Windows, run this on the cmd line

        echo 127.0.0.1 dev.joind.in api.dev.joind.in >> %SYSTEMDRIVE%\Windows\System32\Drivers\Etc\Hosts

5. Browse to the newly provisioned development copy of joind.in.

        open http://dev.joind.in:8080

*Notes:*

- HTTP and SSH ports on the VM are forwarded to localhost (22 -> 2222, 80 -> 8080)
- The joind.in directory you cloned will be mounted inside the VM at `/vagrant`
- You can develop by editing the files you cloned in the IDE of you choice.
- You need to amend src/system/application/config/config.php to set

        $config['base_url']	= 'http://dev.joind.in:8080/';
        $config['api_base_url']	= 'http://api.dev.joind.in:8080/';

- The database is running inside the VM. You can get to it by doing the following:

        you@you> vagrant ssh
        vagrant@vm> sudo su
        root@vm> mysql joindin

- To stop the VM do one of the following:
  `vagrant suspend` if you plan on running it later
  `vagrant destroy` if you wish to delete the VM completely

- Also, when any of of the Puppet manifests change, it is a good idea to rerun them:

        vagrant provision

## Quick Start - Existing Platforms

1. Create a vhost entry for the site. The docroot should be `/src`.

2. Create directories for user-added content.

        mkdir src/system/cache/ctokens && chown apache:apache src/system/cache/ctokens

   (or whatever user and group your web server runs as)

3. Create a MySQL database with username and password.
   Use a database name of 'joindin'

4. Create configuration files for database and config (based on the .dist templates):

        cp src/system/application/config/database.php.dist src/system/application/config/database.php
        cp src/system/application/config/config.php.dist   src/system/application/config/config.php

   Edit these files as appropriate!

5. If you are using Fast-CGI you will need to edit the .htaccess file
   Change lines 17 & 24 from:

        RewriteRule ^(.*)$ /index.php/$1

   to

        RewriteRule ^(.*)$ /index.php?/$1

   Also you will need to amend the config.php so that the uri_protocol setting ends up as follows:

        $config['uri_protocol']	= "QUERY_STRING";

6. Initialise, patch, and populate the database.

        src/scripts/patchdb.sh -t /path/to/joind.in -d joindin -u username -p password -i

   (use the correct username and password)

   If you are using Windows And/Or Git bash you may see an error regarding "o being an invalid option" when running step 6.

   To fix this, you will need to visit http://gnuwin32.sourceforge.net/packages/grep.htm and download the binaries and dependencies zip files
   Extract the contents of the bin folder from the zip files to the bin folder of your Git install and restart Git Bash.

    This should also work for git via the commandline (cmd.exe) but cannot be guaranteed in that environment.

7. Create some sample data to get you started - see `/doc/dbgen/README` for information about this excellent tool

8. To enable useful error messages, add the following to your `.htaccess`

        SetEnv JOINDIN_DEBUG On

9. Enjoy the site!

## Other Resources

* The main website http://joind.in
* Issues list: http://joindin.jira.com/ (good bug reports ALWAYS welcome!)
* CI Environment: lots of output and information about tests, deploys etc: http://jenkins.joind.in
* Community: We hang out on IRC, pop in with questions or comments! #joind.in on Freenode

See LICENSE file for license information for this software
(located in /doc/LICENSE)

## Extensions

### API Tests

To run the frisby tests (frisby.js), you will first need to install node.js and
npm.  Then run:

        npm install -g frisby jasmine-node

I also found that I needed:

        export NODE_PATH=/usr/local/lib/node_modules

You should set the URL that the tests run against to be your local installation:

        export JOINDIN_API_BASE_URL=http://api.joindin.localhost

Then run the tests by going to `/src/tests/api_tests` and running:

        jasmine-node newapi_spec.js

### Unit Tests

There are some tests set up, which use PHPUnit; these can be found in the
src/tests directory and the src/api-v2/tests directory.  There is a phing task
configured to run them - from the root directory simply run "phing phpunit" to run
the tests. Unfortunately, there will be no output about whether the tests passed
or failed from the phing target. A better way to test when you are developing is
to run the tests from within the respective tests directory by just typing
phpunit. The phpunit.xml in each directory will configure the bootstrap as well
as any files that should not be included.

The phpunit.xml file in the src/tests directory will run all of the PHPUnit tests.
The phpunit.xml file in src/api-v2/tests will run only the API v2 unit tests.

### CODE STYLE

Please do your best to ensure that any code you contributed adheres to the
Joind.in coding style. This is roughly equivalent to the PEAR coding standard with
a couple of rules added or taken out. You can run php codesniffer using phing on an
individual file like so:

phing phpcs-human -Dfilename.php

This will run codesniffer on any file within the regular source for Joind.in or the
API-v2 source. Wildcards work as does specifying part of the path in case the
filename alone results in sniffing more files than you wanted.

To see a summary of the codesniff errors and warnings across the entire project, run

phing phpcs-human-summary

This will show the files that still need some attention.


