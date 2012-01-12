/*
* Use this file to load in some default data into the system including things like
* languages, blog categories, etc.
*/

/* Load blog categories */
insert into `blog_cats` (name,ID) values ('General',NULL);

/* Load a list of languages */
insert into `lang` (lang_name,lang_abbr,ID) values ('English - US','en',NULL);
insert into `lang` (lang_name,lang_abbr,ID) values ('English - UK','uk',NULL);
insert into `lang` (lang_name,lang_abbr,ID) values ('Dutch','de',NULL);
	
/* TODO: need more languages! */

/* Load a few sample talk categories */
insert into `categories` (cat_title,cat_desc,ID) values ('Talk','Talk',NULL);
insert into `categories` (cat_title,cat_desc,ID) values ('Social Event','Social Event',NULL);
insert into `categories` (cat_title,cat_desc,ID) values ('Keynote','Keynote',NULL);
insert into `categories` (cat_title,cat_desc,ID) values ('Workshop','Workshop',NULL);
insert into `categories` (cat_title,cat_desc,ID) values ('Event Related','Event Related',NULL);