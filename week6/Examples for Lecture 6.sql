-- Version 1: No user input.
CREATE OR REPLACE PROCEDURE show_dirname
IS

        director_name CHAR(20);
        movie_name CHAR(20);
BEGIN
        movie_name := 'North by Northwest';
        SELECT dirname INTO director_name
            FROM movie m JOIN director d ON m.dirnumb = d.dirnumb
            WHERE  m.mvtitle = movie_name;
        DBMS_OUTPUT.put_line('The director of the movie is:');
        DBMS_OUTPUT.put_line(director_name);
END;


-- Version 2: with a user-input

-- Fully functional version:

CREATE OR REPLACE PROCEDURE Show_dirname 
                  (movie_name IN movie.mvtitle%type)
AS
    director_name director.dirname%type;
     
BEGIN
    SELECT dirname INTO director_name
        FROM movie m JOIN director d ON 
                     m.dirnumb = d.dirnumb
        WHERE lower(m.mvtitle) = lower(movie_name);
    DBMS_OUTPUT.put_line('The director of the movie is:');
    DBMS_OUTPUT.put_line(director_name);
    
END;


-- Version 2: with a user-input and error checking

-- Fully functional version:
CREATE OR REPLACE PROCEDURE Show_dirname 
                  (movie_name IN movie.mvtitle%type)
AS
    director_name director.dirname%type;
     
BEGIN
    SELECT dirname INTO director_name
        FROM movie m JOIN director d ON 
                     m.dirnumb = d.dirnumb
        WHERE lower(m.mvtitle) = lower(movie_name);
    DBMS_OUTPUT.put_line('The director of the movie is:');
    DBMS_OUTPUT.put_line(director_name);

EXCEPTION
      WHEN NO_DATA_FOUND THEN
      DBMS_OUTPUT.PUT_LINE ('The query did not return a result set');
END;

-- a wrong example
-- show the list of movies directed by a specific director

CREATE OR REPLACE PROCEDURE show_movie_title (director_name IN director.dirname%type)
AS
        l_movie_title movie.mvtitle%type;
BEGIN
        
       SELECT mvtitle INTO l_movie_title
            FROM movie m JOIN director d ON m.dirnumb = d.dirnumb
            WHERE lower(d.dirname) = lower(director_name);
        DBMS_OUTPUT.put_line('The title of the movie is:');
        DBMS_OUTPUT.put_line(l_movie_title);

EXCEPTION
      WHEN NO_DATA_FOUND THEN
      DBMS_OUTPUT.PUT_LINE ('The query did not return a result set');

END;

-- improve it:
-- Add an exception

CREATE OR REPLACE PROCEDURE show_movie_title (director_name IN director.dirname%type)
AS
        l_movie_title movie.mvtitle%type;
BEGIN
        -- l_movie_name := movie_name;

        SELECT mvtitle INTO l_movie_title
            FROM movie m JOIN director d ON m.dirnumb = d.dirnumb
            WHERE lower(d.dirname) = lower(director_name);
        DBMS_OUTPUT.put_line('The title of the movie is:');
        DBMS_OUTPUT.put_line(l_movie_title);
EXCEPTION

      WHEN NO_DATA_FOUND THEN
      DBMS_OUTPUT.PUT_LINE ('The query did not return a result set');
      WHEN TOO_MANY_ROWS THEN 
      DBMS_OUTPUT.PUT_LINE ('The query resulted in multiple rows');
END;

exec show_movie_title('Allen, Woody')

-- A better way
-- With a simple cursor


           
CREATE OR REPLACE PROCEDURE show_movie_title (director_name IN director.dirname%type)
AS
        CURSOR mv_cursor IS SELECT *
        -- there is no INTO clause
            FROM movie m JOIN director d ON m.dirnumb = d.dirnumb
            WHERE lower(d.dirname) = lower(director_name);
            
        l_movie mv_cursor%ROWTYPE;
        
BEGIN
     
        OPEN mv_cursor;
        LOOP
            FETCH mv_cursor INTO l_movie;
            EXIT WHEN mv_cursor%NOTFOUND;
        
            DBMS_OUTPUT.put_line('The title of the movie is:');
            DBMS_OUTPUT.put_line(l_movie.mvtitle);
        END LOOP;
        CLOSE mv_cursor;
     
EXCEPTION

      WHEN NO_DATA_FOUND THEN
      DBMS_OUTPUT.PUT_LINE ('The query did not return a result set');

END;          
    
-- Another example with a nested cursor.

exec show_movie_title_and_stars('Ford, John')

-- Find the movies directed by a given director and actors in each of them.
CREATE OR REPLACE PROCEDURE show_movie_title_and_stars (director_name IN director.dirname%type)
AS
        CURSOR mv_cursor IS SELECT m.mvnumb, m.mvtitle
            FROM movie m JOIN director d ON m.dirnumb = d.dirnumb
            WHERE lower(d.dirname) = lower(director_name);
            
        l_movie mv_cursor%ROWTYPE;
        
        CURSOR star_cursor IS SELECT *
            FROM movstar ms JOIN star s ON ms.starnumb = s.starnumb
            WHERE ms.mvnumb = l_movie.mvnumb;
        l_star star_cursor%ROWTYPE;
        BEGIN
     
        OPEN mv_cursor;
        LOOP
            FETCH mv_cursor INTO l_movie;
            EXIT WHEN mv_cursor%NOTFOUND;
        
            -- DBMS_OUTPUT.put_line('The title of the movie is:');
            DBMS_OUTPUT.put_line(l_movie.mvtitle);
            DBMS_OUTPUT.put_line('Stars');
            
            OPEN star_cursor;
            LOOP
                FETCH star_cursor INTO l_star;
                EXIT WHEN star_cursor%NOTFOUND;
                      DBMS_OUTPUT.put_line('    ' || l_star.starname);
                      
            END LOOP;
            CLOSE star_cursor;
            
        END LOOP;
        CLOSE mv_cursor;
     
EXCEPTION

      WHEN NO_DATA_FOUND THEN
      DBMS_OUTPUT.PUT_LINE ('The query did not return a result set');
      
END;      

-- Functions
-- Award success rate = awrd/noms %

-- Wrong version
select success_rate('Laura') from dual;
select success_rate('Rope') from dual;

CREATE OR REPLACE FUNCTION success_rate (movie_name IN movie.mvtitle%TYPE) RETURN NUMBER
AS
    l_success DECIMAL (6,2);

BEGIN 
    SELECT awrd/noms*100.0 INTO l_success
        FROM movie
        WHERE lower(mvtitle) = lower(movie_name);
RETURN l_success;
END;

-- Corrected version
CREATE OR REPLACE FUNCTION success_rate (movie_name IN movie.mvtitle%TYPE) RETURN NUMBER
AS
    l_success DECIMAL (6,2);

BEGIN 
    SELECT awrd/noms*100.0 INTO l_success
        FROM movie
        WHERE lower(mvtitle) = lower(movie_name);
RETURN l_success;
EXCEPTION

      WHEN ZERO_DIVIDE THEN
      RETURN 0;
END;

--Have a value
select success_rate('Laura') from dual;


-- Call a function from a procedure

-- for a given director, find the title of movies, along with award success rate.
CREATE OR REPLACE PROCEDURE show_movie_title_and_success (director_name IN director.dirname%type)
AS
        CURSOR mv_cursor IS SELECT *
        -- there is no INTO clause
            FROM movie m JOIN director d ON m.dirnumb = d.dirnumb
            WHERE lower(d.dirname) = lower(director_name);
            
        l_movie mv_cursor%ROWTYPE;
        l_success DECIMAL(6,2);
        
BEGIN
     
        OPEN mv_cursor;
        LOOP
            FETCH mv_cursor INTO l_movie;
            EXIT WHEN mv_cursor%NOTFOUND;
            l_success := success_rate(l_movie.mvtitle);
            DBMS_OUTPUT.put_line('The title of the movie is: ' ||l_movie.mvtitle);
            DBMS_OUTPUT.put_line('Award Success Rate: ' || l_success || '%');
        END LOOP;
        CLOSE mv_cursor;
     
EXCEPTION

      WHEN NO_DATA_FOUND THEN
      DBMS_OUTPUT.PUT_LINE ('The query did not return a result set');

END; 

exec show_movie_title_and_success('Ford, John')

-- Call a "local" function from a procedure
create or replace PROCEDURE show_movie_title_and_success (director_name IN director.dirname%type)
AS
       CURSOR mv_cursor IS SELECT *
        -- there is no INTO clause
            FROM movie m JOIN director d ON m.dirnumb = d.dirnumb
            WHERE lower(d.dirname) = lower(director_name);

        l_movie mv_cursor%ROWTYPE;
        l_success DECIMAL(6,2);

        FUNCTION my_success_rate (movie_name IN movie.mvtitle%TYPE) RETURN NUMBER
        IS
        l_success DECIMAL (6,2);
        BEGIN 
            SELECT awrd/noms*100.0 INTO l_success
                FROM movie
                WHERE lower(mvtitle) = lower(movie_name);
            RETURN l_success;
        EXCEPTION

            WHEN ZERO_DIVIDE THEN
            RETURN 0;
        END my_success_rate;

BEGIN

        OPEN mv_cursor;
        LOOP
            FETCH mv_cursor INTO l_movie;
            EXIT WHEN mv_cursor%NOTFOUND;
            l_success := my_success_rate(l_movie.mvtitle);
            DBMS_OUTPUT.put_line('The title of the movie is: ' ||l_movie.mvtitle);
            DBMS_OUTPUT.put_line('Award Success Rate: ' || l_success || '%');
        END LOOP;
        CLOSE mv_cursor;

EXCEPTION

      WHEN NO_DATA_FOUND THEN
      DBMS_OUTPUT.PUT_LINE ('The query did not return a result set');

END;


