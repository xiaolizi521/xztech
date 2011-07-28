CREATE LANGUAGE plperlu;

\set ECHO none
\i allow_on_condition.sql

--SET client_min_messages=debug;

\set ECHO all

-- check simple inserts/updates --
CREATE TEMP TABLE test1(id INTEGER);

CREATE TRIGGER test1_allow_trigger BEFORE INSERT OR UPDATE ON test1
FOR EACH ROW EXECUTE PROCEDURE allow_on_condition('%s < 5', 'NEW.id');

-- allow --
INSERT INTO test1 VALUES(1);

-- deny --
INSERT INTO test1 VALUES(10);

-- deny --
UPDATE test1 SET id = 5 WHERE id = 1;

DROP TRIGGER test1_allow_trigger ON test1;
DROP TABLE test1;

-- check non-standard column names --
CREATE TEMP TABLE test2("id'\" INTEGER);
INSERT INTO test2 VALUES(1);

CREATE TRIGGER test2_allow_trigger BEFORE INSERT OR UPDATE ON test2
FOR EACH ROW EXECUTE PROCEDURE allow_on_condition('%s = 42', E'NEW.id''\\');

-- deny --
INSERT INTO test2 VALUES(30);

-- allow --
INSERT INTO test2 VALUES(42);

-- allow --
UPDATE test2 SET "id'\" = 42;

DROP TRIGGER test2_allow_trigger ON test2;
DROP TABLE test2;

-- check string data --
CREATE TEMP TABLE test3(name VARCHAR);

CREATE TRIGGER test3_allow_trigger BEFORE INSERT OR UPDATE on test3
FOR EACH ROW EXECUTE PROCEDURE allow_on_condition('lower(%s) = lower(''PostgreSQL is free'')', 'NEW.name');

-- deny --
INSERT INTO test3 VALUES('PosgreSQL is fun');
-- allow --
INSERT INTO test3 VALUES('PostgreSQL is free');

-- deny, but shouldn't fail --
INSERT INTO test3 VALUES(E'PostgreSQL supports "escape" strings to display characters like \\ or \'');
DROP TRIGGER test3_allow_trigger ON test3;
DROP TABLE test3;

-- check DELETEs --
CREATE TEMP TABLE test4(id INTEGER, date date);

CREATE TRIGGER test4_allow_trigger BEFORE DELETE ON test4
FOR EACH ROW EXECUTE PROCEDURE allow_on_condition('%s < ''July 04, 2009''::date', 'OLD.date');

-- populate the table with data --
INSERT INTO test4 VALUES(1, 'July 05, 2009'::date);
INSERT INTO test4 VALUES(2, 'July 03, 2009'::date);

-- deny --
DELETE FROM test4 WHERE id = 1;

-- allow --
DELETE FROM test4 WHERE id = 2;

DROP TRIGGER test4_allow_trigger ON test4;
DROP TABLE test4;

DROP LANGUAGE plperlu CASCADE;
