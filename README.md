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

        echo "127.0.0.1 dev.joind.in" | sudo tee -a /etc/hosts

5. Browse to the newly provisioned development copy of joind.in.

        open http://dev.joind.in:8080

*Notes:*

- HTTP and SSH ports on the VM are forwarded to localhost (22 -> 2222, 80 -> 8080)
- The joind.in directory you cloned will be mounted inside the VM at `/vagrant`
- You can develop by editing the files you cloned in the IDE of you choice.
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

2. Create a MySQL database with username and password.
   Use a database name of 'joindin'

3. Initialise, patch, and populate the database.

        src/scripts/patchdb.sh -t /path/to/joind.in -d joindin -u username -p password -i

   (use the correct username and password)

4. Create directories for user-added content.

        mkdir src/system/cache/ctokens && chown apache:apache src/system/cache/ctokens

   (or whatever user and group your web server runs as)

5. Create configuration files for database and config (based on the .dist templates):

        cp src/system/application/config/database.php.dist src/system/application/config/database.php
        cp src/system/application/config/config.php.dist   src/system/application/config/config.php

   Edit these files as appropriate!

6. Create some sample data to get you started - see `/doc/dbgen/README` for information about this excellent tool

7. To enable useful error messages, add the following to your `.htaccess`

        SetEnv JOINDIN_DEBUG On
        
8. Enjoy the site!

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

Then run the tests by going to `/src/tests/api_tests` and running:

        jasmine-node newapi_spec.js

### Unit Tests

There are some tests set up, which use PHPUnit; these can be found in the
src/tests directory.  There is a phing task configured to run them - from the
root directory simply run "phing phpunit" to run the tests.

