
# Todo, Roadamap, Bugs


* Turn on op cache revalidation again to a reasonable value (currently it is 0 during development)
* Turn off detailed error reporting for production site


## Short term todo

+ The sidebar alignment still violates Gestalt Theorie.

* Backup/Restore Concept in the browser
  * Extract all pages in all categories as xml and offer download as xml, optionally compressed
  * Extract all images and offer download as tar
  * Make a DB dump and offer download as sql, optionally compressed
  * Import link into the sidebar
* Include automagic import of current state into the generate.sh/run.sh process of git
  * Shutdown container leads to dump before shutdown 
  * Start of container automates import of current state 

* Do a regular / cron based dump 
* Do a dump on / before shutdown. 


* We need dynamicInject scripts not only as such but also differently for the different Dantepedias.


* Maybe: Use Extension:DynamicSidebar to have maintenance aspects only for users allowed to do maintenance and similar.

* Provide the DOCKERFILE with a reasonable 1) NTP if needed and 2) timezone adjustment




## Design default content

* Admin Links Extension, see https://workingwithmediawiki.com/book/chapter15.html

* Set proper permissions for the Wiki

* Add some useful templates
* Create page in das "more" menu setzen der Skin oben rechts
* Create page in die Sidebar setzen

* Sidebar scroll position should be conserved from page call to page call

* Design robots.txt for different use cases and allow activation / choice via browser interface 
  * Allow all
  * Disallow archiving 
  * Disallow all




* Multi Language Wiki: https://workingwithmediawiki.com/book/chapter15.html
* Search Engine Optimization: https://workingwithmediawiki.com/book/chapter15.html

* Make cooked maintenance scripts available as links to the administrator, under protection of a special password and an additional link, 
  via Apache, seperate directory and apache based permission check. 
 * In particular: Extend the export mechanism to allow an option to export all pages of the wiki, not only specific categories of the Main namespace.   
 * all rebuild scripts, update script. 

* We need to configure a possibility for Mediawiki to send emails.

* MediaWiki:Common.css  
* MediaWiki:Common.js
* Template for navigation boxes as in https://workingwithmediawiki.com/book/chapter4.html#toc-Section-26
* Improving the table of contents display
* Improving the side panel display
* Impressum and Privacy Statement
* License text
* Help texts 
* Check * https://workingwithmediawiki.com/book/  for hints on Extensions
* Mediawiki Extension for embedding certain websites as iframe into specific pages of ones personal wiki
** Could be: Todoist, Email, Calendar, Weather, favorite news sites, search engines, and more 
** Might need a browser extension to overcome selectively the no-framing headers sent from a website 
** Might need some approach to automatize the logon needs of these sites as well  
* Include feature for a YubiKey or similar second factor logon  as part of wiki  
* For the use as personal dashboard: Is the a (secure) possibility, to make certain pages visible only to the owner.
 * And in particular: Iframes
 * Or is an architecture better, where you always have two installations, a public and a private one?
* The floating ToC should become part of the User Preferences
* We need a prepopulation of the contents of the side bar and the side bar tree with the categories as empty template  
* we need an idea of how the comment / talk can be used 
* We might enjoy some form of geoip based restriction, especially for the admin logon
* For YUbikey:
 ** https://phabricator.wikimedia.org/T150609



## Known Bugs and Issues

* Currently it is a problem for some pages when we dump from an english speaking and restore into a German speaking Dantepedia. 
Mixed language dump-restore scenarios produce problems !!



## Low Priority

* Support https. (Currently low priority, since the idea is to run this in a LAN and 
  to place public instances behind a caching service such as Amazon AWS Cloudfront
* Support path settings. Low priority since it is better anyhow to have one container per wiki so we need no path settings.









