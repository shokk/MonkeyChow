
Installation:
- Untar the archive and create the cache directory with enough permissions 
  for the server to write in it.
  Alternately, use
    git clone https://github.com/shokk/MonkeyChow.git monkeychow
  in the root directory of your web server.

- Check for compatibility by going to the URL where you installed:
  http://www.site.com/monkeychow/simplepie/compatibility_test/sp_compatibility_test.php
  Make any recommended changes. Current included SimplePie version is 1.3.1.
  As development of SimplePie has ceased, I will be replacing Simplie with
  something else.

- Please use a mysql version 4.1 or higher.  Testing with 4.0 revealed that
  using CURRENT_TIMESTAMP does not work on an install of mysql 4.0 hosted
  at 1&1.  This has not been tested with mysql 5.0, but there is no reason
  that it should not work.  This causes install.php to present a white screen.
  I'm not sure that 4.0 is really at fault, but if you can find out why this
  happens, let me know.
  I recommend using phpMyAdmin to assist with quick database setup.

- copy config-example.php to config.php and alter to taste

- Visit the install.php page in the base install directory.
  For Spanish, you can visit install.php?language=es_US
  This will install the database  and its required tables.
  
- Set up the .htaccess file mentioned at the bottom of this page to 
  protect your new install.  Now you're ready to roll!



Use:
- Visit the /index.php page for a basic view of the feeds.

- Visit the /frames.php page for a frames view of the feeds.
 This view is being actively developed at the moment and will later 
 be merged with the plain panel view to provide all features to all
 users and minimize code duplication.

- Visit the rss.php link for a feed of select articles you have marked as
 "Recycle"

- Visit the rss2.php link for an aggregate feed of the last 200 articles.
 This may let everyone see feeds that are meant to be secure.  A feature
 in a coming update will allow you to make a feed subscribe excluded from
 public viewing.  Otherwise, you can let the public see this.

- Visit the aggregators.php page for a web view at the latest 50 articles.
 This may also let secure feeds be seen by the public and in the future
 will be limited by the above feed attribute excluding public viewing.
 Otherwise, you can let the public see this.

- Set up a cronjob to update your feeds with something like
0,12,24,36,48 * * * *  (/usr/bin/GET -t 120 -C "username:password" http://www.site.com/monkeychow/update-quiet.php)
  or
3,18,33,48 * * * *  (/usr/bin/wget -o /dev/null --user myusername --password mypassword http://localhost/monkeychow/update-quiet.php)




Upgrading from F0F-0.1.9:
-Add the following columns to your feeds table
    `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP,
    `tags` varchar(250) default NULL,
    `aging` int(11) NOT NULL default '30',
    `expir` int(11) NOT NULL default '0',
    `private` bool default '0',

- Add the following columns to your items table:
    `publish` bool default 0,
    `star` bool default 0,

- Add the following to your config.php
     define('FULLNAME',"My Name");
     define('EMAIL',"email@x.com");


Set up a .htaccess file so that any search engine spiders or strangers that
crawl your site will not delete or add rss that you're not interested in.  For
help setting up .htaccess, see http://www.tools.dynamicdrive.com/password/
Example .htaccess:
<FilesMatch
".*(index|edit|add|config|delete|feeds|newfeeds|dump|init|install|mark|uninstall|update|view).*">
AuthUserFile <auth passwd file>
AuthGroupFile /dev/null
AuthName "Monkeychow"
AuthType Basic
Require valid-user

<Limit GET>
order deny,allow
deny from all
allow from all
</Limit>
</FilesMatch>

