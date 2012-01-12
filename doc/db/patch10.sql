# dropping unused tables

DROP TABLE features;
DROP TABLE event_feature;
DROP TABLE event_blog_posts;

INSERT INTO patch_history SET patch_number = 10;
