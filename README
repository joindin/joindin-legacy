Joind.in (http://joind.in)
##########################

This is the source code for the Joind.in website - a resource set up to allow
events to get real-time feedback from those attending. It also gives speakers a 
way to claim and track their presentations over time.


Quick start - Development
#########################
You can set up a development virtual machine running joind.in by following these simple instructions.
1. Install requirements. (Note: these are not required by joind.in itself, but are required for this quick start guide.)
   - VirtualBox (https://www.virtualbox.org/)
   - Ruby (http://www.ruby-lang.org/)
   - Vagrant (http://vagrantup.com/)
2. Clone repository to any location and fetch required submodules (containing Puppet manifests).
   > git clone https://github.com/joindin/joind.in && cd joind.in
   > git submodule init
   > git submodule update
3. Start the process by running Vagrant.
   > vagrant up
4. Add hostname to /etc/hosts.
   > echo "127.0.0.1 dev.joind.in" | sudo tee -a /etc/hosts
5. Browse to the newly provisioned development copy of joind.in.
   > open http://dev.joind.in:8080
Notes:
- The VM will be running in the background as VBoxHeadless
- HTTP and SSH ports on the VM are forwarded to localhost (22 -> 2222, 80 -> 8080)
- The joind.in directory you cloned will be mounted inside the VM at /vagrant
- You can develop by editing the files you cloned in the IDE of you choice.
- The database is running inside the VM. You can get to it by doing the following:
  you@you> vagrant ssh
  vagrant@vm> sudo su
  root@vm> mysql joindin
- To stop the VM do one of the following:
  > vagrant suspend # if you plan on running it later
  > vagrant destroy # if you wish to delete the VM completely
- Also, when any of of the Puppet manifests change, it is a good idea to rerun them:
  > vagrant provision

Quick Start - Production
########################
1. Create a vhost entry for the site. The docroot should be `/src`.
2. Create a MySQL database with username and password.
   Use a database name of 'joindin'
3. Initialise, patch, and populate the database.
   > src/scripts/patchdb.sh -t /path/to/joind.in -d joindin -u username -p password -i
   (use the correct username and password)
4. Create directories for user-added content.
   > mkdir src/system/cache/ctokens && chown apache:apache src/system/cache/ctokens
   (or whatever user and group your web server runs as)
5. Create configuration files for database and config (based on the .dist templates):
   > cp src/system/application/config/database.php.dist src/system/application/config/database.php
   > cp src/system/application/config/config.php.dist   src/system/application/config/config.php
   Edit these files as appropriate!
6. Enjoy the site!
   The default password for users is "password".


Detailed instructions
#####################

There's a few SQL files that are included with the source to help you get things 
up and running. The first four will bring your database to the most current, up-to-date
status and ALL should be run for a new install:
	-----------------------
	-> doc/db/init_db.sql
		Export of the initial data structure, ready for
		import (as exported from MySQL)
		
	-> doc/db/init_data.sql
		This file contains a few handy things for your database's initial setup
		like languages and session categories
		
	-> doc/db/patch*.sql
		Numbered patch files to run in order after running init_db.  All database changes to be
		managed using these, init_db does NOT get updated.  For more information on this approach,
		see http://www.lornajane.net/posts/2010/Simple-Database-Patching-Strategy. If patch 17
		fails it can be skipped.
		
	-> doc/db/seed_countries.sql
		Some seed data for the countries table (needed for timezone selection)

We also have excellent sample data generation, check out /doc/dbgen/README for information about how to generate sample data to use with your installation
	-----------------------
	
This last SQL file will give you a jumping off point to see how the system works. It will 
populate your system with some seed data (talks, users, events) so you can see how they all 
relate:
	-----------------------
	-> doc/db/seed.sql
		Some seed data you can load into your (MySQL) database to get the ball rolling
	-----------------------	

Finally, there's some other scripts that come with the source to make things easier to setup.
	- Release of CodeIgniter
	- Sample cron jobs (in /src/scripts)
	
You can find more on github: http://github.com/joindin/joind.in
Issues should be filed at: http://joindin.jira.com/

See LICENSE file for license information for this software
(located in /doc/LICENSE)


Installation
#####################
The following are the steps you'll need to correctly install the software:

- Download/clone the current repository and drop it into a 
	web-accessible directory for your server

- Set the document root to the /src directory

- In the /src/system/cache directory, create a web server-writeable 
	directory called "ctokens"

- In your src/system/application/config directory:
	> Make a database.php file using the database.php.dist as a guide
	> Make a config.php file using the config.php.dist as a guide
	
- Push the doc/db/init_db.sql file into your database

- Apply any of the other patches in the /db folder

If you'd like some seed data, you can use the seed.sql to load in a few events, 
users and talks. You'll also need to load in the seed_countries.sql file to
get the complete list of countries for the event details.

The default password for users is "password"


Extensions
###################
In order to run tests make sure you have the following extensions installed and
configured. This library can be ignored if you are not planning on running
tests.

To run the frisby tests (frisby.js), you will first need to install node.js and
npm.  Then run:
  npm install -g frisby jasmine-node

I also found that I needed:
  export NODE_PATH=/usr/local/lib/node_modules

Then run the tests by going to /src/tests/api_tests and running:
  jasmine-node newapi_spec.js

(I think the below no longer applies)
- pecl_http
  $ pecl install pecl_http
  
  in php.ini add:
  > linux: extension=http.so
  > windows: extension=http.dll

Debugging
#############
By default the application is in production mode. This primarily means that
error messages and notices are suppressed.

To allow errors and notices to be shown on the site you have to change the
environment variable JOINDIN_DEBUG to the value 'on'. It is discouraged to
do this on production environments as this will allow visitors to see any
errors produced by the site.

The JOINDIN_DEBUG environment variable is controlled from the accompanying
.htaccess file.

Tests
#############

There are some tests set up, which use PHPUnit; these can be found in the
src/tests directory.  There is an ant task configured to run them - from the
root directory simply run "ant phpunit" to run the tests.

Note 1: The API tests actually call the service externally and rely on pecl_http
being installed to run.

Issues
#############

The issue tracker can be found here: http://joindin.jira.com/

RewriteEngine
###################

It is advisable to enable the RewriteEngine (sudo a2enmod rewrite).
.htaccess in /src takes care of all the rewrite rules.

