CREATE LANGUAGE plperlu;

\set ECHO none
\i check_updates.sql
\set ECHO all

CREATE TEMP TABLE test(a INTEGER, b INTEGER, c INTEGER);
INSERT INTO test VALUES(1, 2, 3);

CREATE TRIGGER check_updates_on_test BEFORE INSERT OR UPDATE ON test
FOR EACH ROW EXECUTE PROCEDURE check_updates('f', 'a', 'b', '%s + %s > 20', 'NEW.a', 'NEW.b');

-- allow --
UPDATE test SET c = 10;

-- deny --
UPDATE test SET a = 5, b = 4 WHERE a = 1;

-- allow --
UPDATE test SET a = 11, b = 10 WHERE a = 1;

DROP LANGUAGE plperlu CASCADE;
