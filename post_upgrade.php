<?php
// Include OpenCart config file which contains DB credentials
require_once('../config.php'); // Replace with the actual path to your config.php

// Create database connection
$conn = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to copy data from 'oc_url_alias' to 'oc_seo_url' without duplicates
$sql = "INSERT INTO " . DB_PREFIX . "seo_url (store_id, language_id, query, keyword) 
    SELECT '0', '1', ua.query, ua.keyword 
    FROM " . DB_PREFIX . "url_alias ua
    WHERE NOT EXISTS (
        SELECT 1 
        FROM " . DB_PREFIX . "seo_url su 
        WHERE su.query = ua.query AND su.keyword = ua.keyword
    )";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Data copied successfully to " . DB_PREFIX . "seo_url";
} else {
    echo "Error copying data: " . $conn->error;
}


// Function to copy contents from journal2 to journal3 tables
function copyJournalTablesPost($conn) {
    $prefix2 = DB_PREFIX . "journal2_blog_post";
    $prefix3 = DB_PREFIX . "journal3_blog_post";

    // Getting all journal2 blog tables
    $result = $conn->query("SHOW TABLES LIKE '{$prefix2}%'");
    if ($result) {
        while ($row = $result->fetch_row()) {
            $sourceTable = $row[0];
            $targetTable = str_replace($prefix2, $prefix3, $sourceTable);

            // Get common columns in both source and target tables
            $sourceColumnsResult = $conn->query("SHOW COLUMNS FROM {$sourceTable}");
            $targetColumnsResult = $conn->query("SHOW COLUMNS FROM {$targetTable}");
            if ($sourceColumnsResult && $targetColumnsResult) {
                $sourceColumns = array_column($sourceColumnsResult->fetch_all(), 0);
                $targetColumns = array_column($targetColumnsResult->fetch_all(), 0);
                $commonColumns = implode(", ", array_intersect($sourceColumns, $targetColumns));

                // Prepare a query to check for duplicates and insert data
                $insertQuery = "INSERT INTO {$targetTable} ({$commonColumns}) 
                                SELECT {$commonColumns} FROM {$sourceTable} as src 
                                WHERE NOT EXISTS (
                                    SELECT 1 FROM {$targetTable} as tgt 
                                    WHERE tgt.post_id = src.post_id
                                )";

                // Replace key_column with the column(s) that uniquely identify your records

                // Execute the query
                if ($conn->query($insertQuery) === TRUE) {
                    echo "Data copied successfully from {$sourceTable} to {$targetTable}\n";
                } else {
                    echo "Error copying data from {$sourceTable} to {$targetTable}: " . $conn->error . "\n";
                }
            } else {
                echo "Error fetching columns: " . $conn->error . "\n";
            }
        }
        $result->free();
    } else {
        echo "Error finding journal2 blog tables: " . $conn->error . "\n";
    }
}

// Run the function
copyJournalTablesPost($conn);

// Function to copy contents from journal2 to journal3 tables
function copyJournalTablesCategories($conn) {
    $prefix2 = DB_PREFIX . "journal2_blog_category";
    $prefix3 = DB_PREFIX . "journal3_blog_category";

    // Getting all journal2 blog tables
    $result = $conn->query("SHOW TABLES LIKE '{$prefix2}%'");
    if ($result) {
        while ($row = $result->fetch_row()) {
            $sourceTable = $row[0];
            $targetTable = str_replace($prefix2, $prefix3, $sourceTable);

            // Get common columns in both source and target tables
            $sourceColumnsResult = $conn->query("SHOW COLUMNS FROM {$sourceTable}");
            $targetColumnsResult = $conn->query("SHOW COLUMNS FROM {$targetTable}");
            if ($sourceColumnsResult && $targetColumnsResult) {
                $sourceColumns = array_column($sourceColumnsResult->fetch_all(), 0);
                $targetColumns = array_column($targetColumnsResult->fetch_all(), 0);
                $commonColumns = implode(", ", array_intersect($sourceColumns, $targetColumns));

                // Prepare a query to check for duplicates and insert data
                $insertQuery = "INSERT INTO {$targetTable} ({$commonColumns}) 
                                SELECT {$commonColumns} FROM {$sourceTable} as src 
                                WHERE NOT EXISTS (
                                    SELECT 1 FROM {$targetTable} as tgt 
                                    WHERE tgt.category_id = src.category_id
                                )";

                // Replace key_column with the column(s) that uniquely identify your records

                // Execute the query
                if ($conn->query($insertQuery) === TRUE) {
                    echo "Data copied successfully from {$sourceTable} to {$targetTable}\n";
                } else {
                    echo "Error copying data from {$sourceTable} to {$targetTable}: " . $conn->error . "\n";
                }
            } else {
                echo "Error fetching columns: " . $conn->error . "\n";
            }
        }
        $result->free();
    } else {
        echo "Error finding journal2 blog tables: " . $conn->error . "\n";
    }
}

// Run the function
copyJournalTablesCategories($conn);

// Query to copy data from 'oc_url_alias' to 'oc_seo_url' without duplicates
$sql = "UPDATE " . DB_PREFIX . "journal3_blog_post  set image = REPLACE(image, 'data/', 'catalog/');";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Images in Journal Blog updated successfully.\n";
} else {
    echo "Error updating images. " . $conn->error . "\n";
}

// Query to copy data from 'oc_url_alias' to 'oc_seo_url' without duplicates
$sql = "UPDATE " . DB_PREFIX . "journal3_blog_post SET image = REPLACE(image, 'data/', 'catalog/');";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Images in Journal Blog updated successfully.\n";
} else {
    echo "Error updating images. " . $conn->error . "\n";
}

// Query to copy data from 'oc_url_alias' to 'oc_seo_url' without duplicates
$sql = "UPDATE " . DB_PREFIX . "journal3_blog_post_description SET description = REPLACE(description, 'data/', 'catalog/');";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Images in descriptions in Journal Blog updated successfully.\n";
} else {
    echo "Error updating images. " . $conn->error . "\n";
}


//update `oc_journal3_blog_post` set post_data = "[]";

// Close connection
$conn->close();
?>
