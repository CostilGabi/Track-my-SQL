<?php
/*

Plugin Name: Track my SQL
Description: PhP script that will track every database modification based on save_posts hook. This script was made as plugin type.
Author: Costil Gabriel
Version: 1.0

*/

// Register the database update listener
add_action('save_post', 'export_post_changes', 10, 3);

// Define the SQL file path
$file_path = __DIR__ . '/post_changes.sql';

// Initialize the SQL file
if (!file_exists($file_path)) {

    file_put_contents($file_path, "CREATE DATABASE IF NOT EXISTS [YOUR DATABASE NAME];\nUSE [YOUR DATABASE NAME];\n\n");

}

// Define the export function
function export_post_changes($post_ID, $post, $update) {
    
    // Only export updates, not new posts
    if (!$update) {
        return;
    }

    // Check if post_date_gmt is empty or invalid (it's for when SQL is in safe mode)
    if (empty($post->post_date_gmt) || $post->post_date_gmt == '0000-00-00 00:00:00') {

        $post_date_gmt = gmdate('Y-m-d H:i:s', current_time('timestamp', 1));

    } else {
        
        $post_date_gmt = $post->post_date_gmt;

    }

    // Define the post data to export
    $post_data = array(
        'ID' => $post_ID,
        'post_author' => $post->post_author,
        'post_date' => $post->post_date,
        'post_date_gmt' => $post->post_date_gmt,
        'post_content' => $post->post_content,
        'post_title' => $post->post_title,
        'post_excerpt' => $post->post_excerpt,
        'post_status' => $post->post_status,
        'comment_status' => $post->comment_status,
        'ping_status' => $post->ping_status,
        'post_password' => $post->post_password,
        'post_name' => $post->post_name,
        'to_ping' => $post->to_ping,
        'pinged' => $post->pinged,
        'post_modified' => $post->post_modified,
        'post_modified_gmt' => $post->post_modified_gmt,
        'post_content_filtered' => $post->post_content_filtered,
        'post_parent' => $post->post_parent,
        'guid' => $post->guid,
        'menu_order' => $post->menu_order,
        'post_type' => $post->post_type,
        'post_mime_type' => $post->post_mime_type,
        'comment_count' => $post->comment_count
    );

    // Build the SQL query to update the post data. If you have a different prefix, change it here!
    $sql_query = "REPLACE INTO wp_posts (";
    $sql_query .= implode(", ", array_keys($post_data));
    $sql_query .= ") VALUES (";
    
    foreach ($post_data as $value) {
        
        $sql_query .= "'" . esc_sql($value) . "', ";

    }

    $sql_query = rtrim($sql_query, ", ") . ");\n";

    // Append the SQL query to the SQL file
    file_put_contents(__DIR__ . '/post_changes.sql', $sql_query, FILE_APPEND);
    
}

// Define the import function
function import_sql_changes($file_path) {

    // Read the SQL file line by line
    $sql_file = file($file_path);
    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Execute each line as an SQL query. This part will make sure to sustain your modifications. You can delete this part if you only want to get tracked.
    foreach ($sql_file as $line) {

        // Skip comments and empty lines
        if (substr($line, 0, 2) == '--' || trim($line) == '') {

            continue;

        }

        // Execute the SQL query
        $result = mysqli_query($db, $line);

        // Log any errors or warnings
        if (!$result) {
            $error_log = fopen(__DIR__ . '/import_errors.log', 'a');
            fwrite($error_log, mysqli_error($db) . "\n");
            fclose($error_log);
        }
    }

    mysqli_close($db);

}

// Import the SQL changes. As above, this can be deleted too if you don't want that feature.
import_sql_changes(__DIR__ . '/post_changes.sql');

?>
