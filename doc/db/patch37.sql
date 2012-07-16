-- adding the links column to the talk

alter table talks add column links text;

INSERT INTO patch_history SET patch_number = 37;
