# Joind.in (http://joind.in)

This is the source code for the Joind.in website - a resource set up to allow
events to get real-time feedback from those attending. It also gives speakers a
way to claim and track their presentations over time.

This README includes instructions for setting up joind.in. You can either install 
joind.in on an existing PHP platform, or use our vagrant setup. Note: contributors 
should start at the Contributor readme (CONTRIBUTING.md).

PLEASE NOTE: **This project is now in maintenance mode.**  Bug fixes are welcome, new features will not be accepted.

Please go to http://github.com/joindin/joindin-web2 to contribute to the next generation joind.in site: http://m.joind.in

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

6. Initialise, patch, and populate the database.  The database files are no longer part of this repository; they live in the API repo https://github.com/joindin/joindin-api which you will also need to fork and clone.  Within that codebase, you can run the following command to populate your database, using the credentials you created in the previous step.

        scripts/patchdb.sh -t /path/to/joindin-api -d joindin -u username -p password -i

   If you are using Windows And/Or Git bash you may see an error regarding "o being an invalid option" when running step 6.

   To fix this, you will need to visit http://gnuwin32.sourceforge.net/packages/grep.htm and download the binaries and dependencies zip files
   Extract the contents of the bin folder from the zip files to the bin folder of your Git install and restart Git Bash.

    This should also work for git via the commandline (cmd.exe) but cannot be guaranteed in that environment.

7. Create some sample data to get you started, this tool is also under the API repo.   Look at `tools/dbgen/README.md` for information about this excellent tool

8. To enable useful error messages, add the following to your `.htaccess`

        SetEnv JOINDIN_DEBUG on

9. Enjoy the site!

## Quick Start - Using Vagrant

The virtual machine has been moved to a different repo. To use it [fork the joindin-vm](https://github.com/joindin/joindin-vm) repository and follow the instructions in there.

This VM will load all three Joind.in projects (joind.in, joindin-vm and joindin-web2). 


## Other Resources

* The main website http://joind.in
* Issues list: http://joindin.jira.com/ (good bug reports ALWAYS welcome!  To get started, try an issue labelled "easy-pick")
* The API repo, which much of the website also depends on: https://github.com/joindin/joindin-api
* CI Environment: lots of output and information about tests, deploys etc: http://jenkins.joind.in
* Community: We hang out on IRC, pop in with questions or comments! #joind.in on Freenode

See LICENSE file for license information for this software
(located in /doc/LICENSE)

## Extensions

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

## Global .gitignore

git has the capability to define a global gitignore file , which means you can 
set up rules on your machine to ignore everything you don't want to include in 
your commits. This works not only for this project, but for all your other
projects too.

You can define the gitignore file with a command that looks like this, where the 
last argument is the file that holds the patterns to ignore: 

    $ git config --global core.excludesfile ~/.gitignore_global

Octocat gives [a good starting point](https://gist.github.com/octocat/9257657) for 
what to include, but you can also ignore the files used by your editor:

    # Eclipse
    .classpath
    .project
    .settings/
    
    # Intellij
    .idea/
    *.iml
    *.iws
        
    # Maven
    log/
    target/

    # Netbeans
    nbproject/private/

For more info on ignoring files, [github has an excellent help page](https://help.github.com/articles/ignoring-files/).
