<?php
include(__DIR__ . "/../config/dbcon.php");

// Function to update users table structure
function updateUsersTableStructure($con) {
    $alterTableQueries = [
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS postal_code VARCHAR(6) DEFAULT NULL"
    ];

    foreach ($alterTableQueries as $query) {
        if (!mysqli_query($con, $query)) {
            echo "Error updating table structure: " . mysqli_error($con);
        }
    }
}

// Run the update when file is included
updateUsersTableStructure($conn); 