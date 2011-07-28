CREATE LANGUAGE plperlu;

\set ECHO none
\i deny_updates.sql
\set ECHO all

CREATE TEMP TABLE test_users(id INTEGER PRIMARY KEY, name VARCHAR, password VARCHAR(32), enabled BOOLEAN);

-- Disallow update of name field --
CREATE TRIGGER deny_test_users_update BEFORE UPDATE ON test_users FOR EACH ROW EXECUTE PROCEDURE deny_updates('false', name);

INSERT INTO test_users VALUES(1, 'alexk', 'iddqd', true);
INSERT INTO test_users VALUES(2, 'alexk', 'idkfa', true);
INSERT INTO test_users VALUES(3, 'alexk', 'idspispopd', false);

-- Should be executed --
UPDATE test_users SET password='idclip' WHERE password='idspispopd';
UPDATE test_users SET enabled=false WHERE id=1;
UPDATE test_users SET id=id*10;

-- Should be cancelled --
UPDATE test_users SET name='devrim' WHERE name='alexk';

-- Disallow updates of 'enabled' field --
CREATE TRIGGER deny_test_users_update_most BEFORE UPDATE ON test_users FOR EACH ROW EXECUTE PROCEDURE deny_updates('FALSE', enabled);

-- Try to update id, should be allowed ---
UPDATE test_users SET id=id-1;
UPDATE test_users SET password='bestkeptsecret' WHERE name='alexk';

-- Try to update some other fields, should fail --
UPDATE test_users SET name='jd' WHERE enabled=true;
UPDATE test_users SET enabled=false;

-- Drop all triggers ---
DROP TRIGGER deny_test_users_update on test_users;
DROP TRIGGER deny_test_users_update_most on test_users;

-- Check the result ---
SELECT * from test_users;

-- Check that now can update fields ---
UPDATE test_users SET id=id-1;
UPDATE test_users SET name='andrei' WHERE id IN (SELECT id FROM test_users LIMIT 1);

-- Create trigger on multiple columns. Enable updates for 'name' and 'passsword' --
CREATE TRIGGER deny_test_users_multi BEFORE UPDATE ON test_users FOR EACH ROW EXECUTE PROCEDURE deny_updates('n', id, enabled);

-- The following updates should fail --
UPDATE test_users SET id=101, enabled=false WHERE name='alexk';
UPDATE test_users SET enabled=true;


-- Drop all triggers --
DROP TRIGGER deny_test_users_multi on test_users;

-- Create trigger which disallows updates for all attributes _except_ id and enabled --
CREATE TRIGGER deny_test_users_excluding BEFORE UPDATE ON test_users FOR EACH ROW EXECUTE PROCEDURE deny_updates('Y', id, enabled);

-- The following updates should be successfull  --
UPDATE test_users SET id=id+1, enabled=false WHERE name='alexk';
UPDATE test_users SET enabled=true;

-- The following updates should fail  --
UPDATE test_users SET name='stupid', password='simple';
UPDATE test_users SET id=id+1, password='empty'  WHERE name='alexk' AND id>2;

-- Check if we still can do INSERTs and DELETEs --
-- I guess Devrim has a rather cryptic password... --
INSERT INTO test_users VALUES(20, 'devrim', '!@#23asd~21112A2ss1!', false);
DELETE FROM test_users WHERE id<10;

-- Check the result --
SELECT * FROM test_users;

-- Drop all triggers --
DROP TRIGGER deny_test_users_excluding ON test_users;

-- Set trigger on non-existing attribute --
CREATE TRIGGER deny_test_not_exists BEFORE UPDATE ON test_users FOR EACH ROW EXECUTE PROCEDURE deny_updates('t', flag);

-- Insert should not be affected by the trigger --
INSERT INTO test_users VALUES(120, 'alexk', 'notexists', false);

-- Update should fail due to the incorrect trigger column name argument --
UPDATE test_users SET name='stupid', password='simple';

-- Check the result --
SELECT * FROM test_users;

-- Finally drop the test relation --
DROP TABLE test_users;

-- Check if quoted identifiers are supported --
CREATE TEMP TABLE quoted_test(" id" INTEGER, "a,""text"" in column" TEXT);

CREATE TRIGGER deny_quoted_test_id BEFORE UPDATE ON quoted_test FOR EACH ROW EXECUTE PROCEDURE deny_updates('f', " id" );

INSERT INTO quoted_test VALUES(1, 'some text');

-- Should be blocked --
UPDATE quoted_test SET " id" = 2;
-- Should be allowed --
UPDATE quoted_test SET "a,""text"" in column"='some other text';

-- Check the result --
SELECT * FROM quoted_test;

DROP TRIGGER deny_quoted_test_id ON quoted_test;

CREATE TRIGGER deny_quoted_test_not_id BEFORE UPDATE ON quoted_test FOR EACH ROW EXECUTE PROCEDURE deny_updates('ALLOW_LIST', " id");

-- Should be allowed --
UPDATE quoted_test SET " id" = 3;
-- Should be blocked --
UPDATE quoted_test SET "a,""text"" in column"='some more text';

-- Check the result --
SELECT * FROM quoted_test;

DROP TRIGGER deny_quoted_test_not_id on quoted_test;
DROP TABLE quoted_test;

-- Check if we can allow/disallow updates to all fields at once --
CREATE TEMP TABLE update_all_test(id INTEGER, rank INTEGER, name VARCHAR);

CREATE TRIGGER update_all_test_deny BEFORE UPDATE ON update_all_test FOR EACH ROW EXECUTE PROCEDURE deny_updates(1);

INSERT INTO update_all_test VALUES(1, 1, 'www.google.com');
INSERT INTO update_all_test VALUES(1, 2, 'www.yahoo.com');

-- Should be denied --
UPDATE update_all_test SET id=3;
UPDATE update_all_test SET rank = 3;
UPDATE update_all_test SET name='www.live.com';

-- Check the result --
SELECT * FROM update_all_test;

DROP TRIGGER update_all_test_deny ON update_all_test;

CREATE TRIGGER update_all_test_allow BEFORE UPDATE ON update_all_test FOR EACH ROW EXECUTE PROCEDURE deny_updates(0);

-- Should be allowed --
UPDATE update_all_test SET id=3;
UPDATE update_all_test SET rank = 3;
UPDATE update_all_test SET name='www.live.com';

-- Check the result --
SELECT * FROM update_all_test;

DROP TRIGGER update_all_test_allow ON update_all_test;

-- Check if we can handle missing parameters case --
CREATE TRIGGER update_all_test_incorrect BEFORE UPDATE ON update_all_test FOR EACH ROW EXECUTE PROCEDURE deny_updates();

-- Should be denied --
UPDATE update_all_test SET rank = rank + 1;

DROP TRIGGER update_all_test_incorrect ON update_all_test;

-- Check for the NULL values --
CREATE TRIGGER update_test_name BEFORE UPDATE ON update_all_test FOR EACH ROW EXECUTE PROCEDURE deny_updates('', name);

-- Should be allowed
UPDATE update_all_test SET rank = NULL;

-- Should be cancelled
UPDATE update_all_test SET name=NULL WHERE name = 'www.live.com';

-- Create trigger on rank
CREATE TRIGGER update_test_rank BEFORE UPDATE ON update_all_test FOR EACH ROW EXECUTE PROCEDURE deny_updates('false', rank);

-- Should be cancelled --
UPDATE update_all_test SET rank = 3;

-- 'Updating' rank field from NULL to NULL, should be allowed
UPDATE update_all_test SET rank = NULL, id=1;

-- Check the result --
SELECT *from update_all_test;

-- Drop all triggers --
DROP TRIGGER update_test_name ON update_all_test;
DROP TRIGGER update_test_rank ON update_all_test;

-- Test ONLY_FROM_NULL functionality
CREATE TRIGGER update_test_null BEFORE UPDATE ON update_all_test FOR EACH ROW EXECUTE PROCEDURE deny_updates('ONLY_FROM_NULL', rank);

-- Should be allowed --
UPDATE update_all_test SET rank = 42;
UPDATE update_all_test SET id = NULL;

-- Should be blocked --
UPDATE update_all_test SET rank = -42;

DROP TRIGGER update_test_null ON update_all_test;
CREATE TRIGGER update_test_null BEFORE UPDATE ON update_all_test FOR EACH ROW EXECUTE PROCEDURE deny_updates('ONLY_FROM_NULL,ALLOW_LIST', rank);

-- Should be allowed --
UPDATE update_all_test SET id = rank;
UPDATE update_all_test SET rank = NULL;

-- Should be blocked --
UPDATE update_all_test SET id = 10;

-- Check if invalid values of 'allowed' param are rejected --
CREATE TRIGGER update_test_invalid BEFORE UPDATE ON update_all_test FOR EACH ROW EXECUTE PROCEDURE deny_updates('foo', rank);

-- Should fail with error message stating that the allowed_arg value is invalid --
UPDATE update_all_test SET rank=rank+1;

-- DROP trigger and target table --
DROP TRIGGER update_test_invalid ON update_all_test;
DROP TABLE update_all_test;

DROP LANGUAGE plperlu CASCADE;
