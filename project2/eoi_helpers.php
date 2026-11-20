<?php
/** Creates the Expression of Interest table used by the apply form and the HR manager. */
function eoi_ensure_table(PDO $db): void
{
    $db->exec("CREATE TABLE IF NOT EXISTS eoi (
        EOInumber INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        date_of_birth DATE NOT NULL,
        gender VARCHAR(10) NOT NULL,
        email VARCHAR(254) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        street_address VARCHAR(100) NOT NULL,
        suburb VARCHAR(50) NOT NULL,
        state VARCHAR(3) NOT NULL,
        postcode CHAR(4) NOT NULL,
        job_reference CHAR(5) NOT NULL,
        position VARCHAR(30) NOT NULL,
        skills TEXT NOT NULL,
        other_skills VARCHAR(500) NOT NULL,
        cv_name VARCHAR(255) NOT NULL,
        cv_mime VARCHAR(100) NOT NULL,
        cv MEDIUMBLOB NOT NULL,
        terms TINYINT(1) NOT NULL,
        status ENUM('New','Current','Final') NOT NULL DEFAULT 'New',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (job_reference)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
