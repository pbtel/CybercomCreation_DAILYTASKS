DROP TABLE IF EXISTS tickets  CASCADE;
DROP TABLE IF EXISTS bookings CASCADE;
DROP TABLE IF EXISTS seats    CASCADE;
DROP TABLE IF EXISTS users    CASCADE;
DROP TABLE IF EXISTS theaters CASCADE;
DROP TABLE IF EXISTS movies   CASCADE;

CREATE TABLE movies(
	movie_id 		SERIAL 			PRIMARY KEY,
	title			VARCHAR(100) 	NOT NULL,
	genre			VARCHAR(50) 	NOT NULL,
	language 		VARCHAR(30) 	NOT NULL DEFAULT 'English',
	duration_min 	INT 			NOT NULL,
	rating 			DECIMAL(3,1) 	NOT NULL,
	created_at		TIMESTAMP 		NOT NULL DEFAULT NOW(),
	created_by		VARCHAR(50)		NOT NULL DEFAULT 'System',
	deleted_at		TIMESTAMP,		
	deleted_by 		VARCHAR(50)
);

CREATE TABLE theaters(
	theater_id		SERIAL			PRIMARY KEY,
	name			VARCHAR(100)	NOT NULL,
	city			VARCHAR(50)		NOT NULL,
	address			TEXT			NOT NULL,
	created_at		TIMESTAMP		NOT NULL DEFAULT NOW(),
	created_by		VARCHAR(50)		NOT NULL DEFAULT 'System',
	deleted_at		TIMESTAMP,
	deleted_by 		VARCHAR(50)
);

CREATE TABLE seats(
	seat_id			SERIAL 			PRIMARY KEY,
	theater_id		INT				NOT NULL REFERENCES theaters(theater_id) ON DELETE CASCADE,
	row_label		CHAR(1)			NOT NULL,
	seat_number		INT				NOT NULL,
	seat_type		VARCHAR(20)		NOT NULL DEFAULT 'Regular',
	price			DECIMAL(8,2)	NOT NULL
);

CREATE TABLE users(
	user_id			SERIAL			PRIMARY KEY,
	full_name		VARCHAR(100)	NOT NULL,
	email			VARCHAR(50)		NOT NULL UNIQUE,
	phone			VARCHAR(10)		NOT NULL,
	created_at		TIMESTAMP		NOT NULL DEFAULT NOW(),
	created_by		VARCHAR(50)		NOT NULL DEFAULT 'System',
	deleted_at		TIMESTAMP,
	deleted_by		VARCHAR(50)
);

CREATE TABLE bookings (
	booking_id		SERIAL			PRIMARY KEY,
	user_id			INT				NOT NULL REFERENCES users(user_id) ON DELETE RESTRICT,
	movie_id		INT				NOT NULL REFERENCES movies(movie_id) ON DELETE RESTRICT,
	show_date		DATE			NOT NULL,
	show_time		TIME			NOT NULL,
	total_amount	DECIMAL(10,2)	NOT NULL,
	status			VARCHAR(20)     NOT NULL DEFAULT 'Confirmed',
	payment_method	VARCHAR(20) 	NOT NULL DEFAULT 'UPI',
	created_at		TIMESTAMP		NOT NULL DEFAULT NOW(),
	created_by		VARCHAR(50)		NOT NULL DEFAULT 'System',
	deleted_at		TIMESTAMP,
	deleted_by		VARCHAR(50)
);


CREATE TABLE tickets(
	ticket_id	SERIAL		PRIMARY KEY,
	booking_id	INT			NOT NULL REFERENCES bookings(booking_id) ON DELETE CASCADE,
	seat_id		INT			NOT NULL REFERENCES seats(seat_id) ON DELETE RESTRICT
);

CREATE INDEX idx_bookings_user_id    ON bookings(user_id);
CREATE INDEX idx_bookings_movie_id   ON bookings(movie_id);
CREATE INDEX idx_bookings_show_date  ON bookings(show_date);
CREATE INDEX idx_bookings_status     ON bookings(status);
CREATE INDEX idx_tickets_booking_id  ON tickets(booking_id);
CREATE INDEX idx_seats_theater_id    ON seats(theater_id);
CREATE INDEX idx_users_email         ON users(email);

INSERT INTO movies (title, genre, language, duration_min, rating, created_by) VALUES
('The Dark Knight',       'Action',   'English', 152, 9.0, 'admin'),
('Inception',             'Sci-Fi',   'English', 148, 8.8, 'admin'),
('RRR',                   'Action',   'Telugu',  187, 8.0, 'admin'),
('Pathaan',               'Thriller', 'Hindi',   146, 6.5, 'admin'),
('KGF Chapter 2',         'Action',   'Kannada', 168, 8.2, 'admin'),
('3 Idiots',              'Comedy',   'Hindi',   170, 8.4, 'admin'),
('Interstellar',          'Sci-Fi',   'English', 169, 8.6, 'admin'),
('Pushpa: The Rise',      'Action',   'Telugu',  179, 7.6, 'admin'),
('Dune Part Two',         'Sci-Fi',   'English', 166, 8.5, 'admin'),
('Jawan',                 'Action',   'Hindi',   169, 7.2, 'admin');


INSERT INTO theaters (name, city, address, created_by) VALUES
('PVR Cinemas',       'Mumbai',    '1st Floor, Phoenix Mills, Lower Parel', 'admin'),
('INOX Multiplex',    'Delhi',     'Select Citywalk Mall, Saket',           'admin'),
('Cinepolis',         'Bangalore', 'Forum Mall, Koramangala',               'admin'),
('Miraj Cinemas',     'Pune',      'Seasons Mall, Magarpatta City',         'admin'),
('Carnival Cinemas',  'Hyderabad', 'GVK One Mall, Banjara Hills',   'admin');

INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 1, 'A', s, 'Regular', 150.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 1, 'B', s, 'Regular', 150.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 1, 'C', s, 'Premium', 250.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 1, 'D', s, 'Premium', 250.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 1, 'E', s, 'VIP',     400.00 FROM generate_series(1, 30) s;

INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 2, 'A', s, 'Regular', 150.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 2, 'B', s, 'Regular', 150.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 2, 'C', s, 'Premium', 250.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 2, 'D', s, 'Premium', 250.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 2, 'E', s, 'VIP',     400.00 FROM generate_series(1, 30) s;

INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 3, 'A', s, 'Regular', 150.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 3, 'B', s, 'Regular', 150.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 3, 'C', s, 'Premium', 250.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 3, 'D', s, 'Premium', 250.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 3, 'E', s, 'VIP',     400.00 FROM generate_series(1, 30) s;

INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 4, 'A', s, 'Regular', 150.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 4, 'B', s, 'Regular', 150.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 4, 'C', s, 'Premium', 250.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 4, 'D', s, 'Premium', 250.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 4, 'E', s, 'VIP',     400.00 FROM generate_series(1, 30) s;

INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 5, 'A', s, 'Regular', 150.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 5, 'B', s, 'Regular', 150.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 5, 'C', s, 'Premium', 250.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 5, 'D', s, 'Premium', 250.00 FROM generate_series(1, 30) s;
INSERT INTO seats (theater_id, row_label, seat_number, seat_type, price) SELECT 5, 'E', s, 'VIP',     400.00 FROM generate_series(1, 30) s;

INSERT INTO users (full_name, email, phone, created_by)
SELECT
    'User ' || s                   AS full_name,
    'user' || s || '@example.com'  AS email,
    '98' || LPAD(s::TEXT, 8, '0')  AS phone,
    'system'
FROM generate_series(1, 500) s;

INSERT INTO bookings (user_id, movie_id, show_date, show_time, total_amount, status, payment_method, created_by)
SELECT
    (s % 500) + 1           AS user_id,
    (s % 10)  + 1           AS movie_id,
    DATE '2025-01-01' + (s % 365)   AS show_date,
    CASE s % 5
        WHEN 0 THEN '10:00'::TIME
        WHEN 1 THEN '13:00'::TIME
        WHEN 2 THEN '16:00'::TIME
        WHEN 3 THEN '19:00'::TIME
        ELSE        '22:00'::TIME
    END                     AS show_time,
    0.00                    AS total_amount,
    CASE s % 5
        WHEN 3 THEN 'cancelled'
        WHEN 4 THEN 'pending'
        ELSE        'confirmed'
    END                     AS status,
    CASE s % 3
        WHEN 0 THEN 'UPI'
        WHEN 1 THEN 'Card'
        ELSE        'Cash'
    END                     AS payment_method,
    'system'
FROM generate_series(1, 3500) s;

-- All bookings → 1st ticket
INSERT INTO tickets (booking_id, seat_id)
SELECT booking_id, ((booking_id - 1) % 750) + 1
FROM bookings;

-- All bookings → 2nd ticket
INSERT INTO tickets (booking_id, seat_id)
SELECT booking_id, (booking_id % 750) + 1
FROM bookings;

-- Every 2nd booking → 3rd ticket
INSERT INTO tickets (booking_id, seat_id)
SELECT booking_id, ((booking_id + 1) % 750) + 1
FROM bookings
WHERE booking_id % 2 = 0;

-- Every 3rd booking → 4th ticket
INSERT INTO tickets (booking_id, seat_id)
SELECT booking_id, ((booking_id + 2) % 750) + 1
FROM bookings
WHERE booking_id % 3 = 0;

UPDATE bookings
SET total_amount = (
    SELECT SUM(s.price)
    FROM tickets tk
    JOIN seats s ON s.seat_id = tk.seat_id
    WHERE tk.booking_id = bookings.booking_id
);

