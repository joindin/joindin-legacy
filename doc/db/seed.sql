/* 
Please note! This assumes that you've already loaded in 
the structure of the database via the other SQL file. Without
the table structure, this seed data cannot be added.
*/
	
/* Add in some sample users */
/* John Doe is a normal site user, is password is "password" */
insert into user (
	username,password,
	email,last_login,
	admin,full_name,
	active,ID
) value (
	'johndoe',
	'5f4dcc3b5aa765d61d8327deb882cf99',
	'johndoe@sampledomain.com',
	unix_timestamp(),
	0,
	'John Doe',
	1, NULL
);
SELECT @fuserid:=LAST_INSERT_ID();
/* Ima Admin is an admin, her password is also "password" */
insert into user (
	username,password,
	email,last_login,
	admin,full_name,
	active,ID
) value (
	'imaadmin',
	'5f4dcc3b5aa765d61d8327deb882cf99',
	'ima@sampledomain.com',
	unix_timestamp(),
	1,
	'Ima Admin',
	1, NULL
);
SELECT @suserid:=LAST_INSERT_ID();
/* ----------------------------------*/

/* Insert sample event data */
insert into events (
	event_name,
	event_start,event_end,
	event_lat,event_long,
	event_loc,event_desc,
	event_stub,
	event_icon,event_hashtag,
	event_href,event_cfp_start,event_cfp_end,
	event_voting,private,
	pending,active,
	ID
) values (
	'Test Event #1',
	unix_timestamp(),
	(select unix_timestamp()+86400),
	'','','Dallas, Tx',
	'This is a sample event from the seed load script',
	'seedload',
	'','seedload_hash',
	'http://sampledomain.com',
	'','',0,
	0,0,1,NULL
);
SELECT @evtid:=LAST_INSERT_ID();
/* ----------------------------------*/

/* Add a sample event comment */
insert into event_comments (
	event_id,
	comment,
	date_made,
	user_id,
	active,
	cname,
	comment_type,
	ID
) values (
	@evtid,
	'This is a sample comment on the Test Event #1',
	unix_timestamp(),
	@fuserid,
	1,
	'',NULL,NULL
);
/* ----------------------------------*/

/* Insert related talk data */
insert into talks (
	talk_title,speaker,slides_link,
	date_given,event_id,talk_desc,
	active,owner_id,lang,
	ID
) values (
	'Sample Talk #1 from Seed Load',
	'John Doe',
	'http://slideshare.com',
	unix_timestamp(),
	@evtid,
	'This is sample talk #1 from the seed load. This description is here to provide an example.',
	1,
	NULL,
	'en',
	NULL
);
SELECT @ftalkid:=LAST_INSERT_ID();

/* Insert speaker data for the talk */
insert into talk_speaker (
	talk_id,
	speaker,
	ID
)values(
	@ftalkid,
	'John Doe',
	NULL
)

insert into talks (
	talk_title,speaker,slides_link,
	date_given,event_id,talk_desc,
	active,owner_id,lang,
	ID
) values (
	'Sample Talk #2 from Seed Load',
	'Jane Doe',
	'http://slideshare.com',
	unix_timestamp(),
	@evtid,
	'This is sample talk #2 from the seed load. This description is here to provide an example.',
	1,
	NULL,
	'en',
	NULL
);
SELECT @stalkid:=LAST_INSERT_ID();

/* Insert speaker data for the talk */
insert into talk_speaker (
	talk_id,
	speaker,
	ID
)values(
	@stalkid,
	'Jane Doe',
	NULL
)

/* ----------------------------------*/

/* Add a pending claim for John Doe on the first talk */
insert into user_admin (
	uid,rid,
	rtype,rcode,
	ID
) values (
	@fuserid,@ftalkid,
	'talk','pending',
	NULL
);

/* Make the first user the admin of an event */
insert into user_admin (
	uid,rid,
	rtype,rcode,
	ID
) values (
	@fuserid,@evtid,
	'event','',NULL
);
