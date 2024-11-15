<?php 
/*
 * Plugin Name:       WordPress Debugger
 * Description:       WordPress Debugging plugin
 * Version:           1.0.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Sagar Shrestha
 * Text Domain:       codewing-debugger
 */

/**
 * Summary of sagar_write_log
 * @param mixed $data
 * @return void
 */
function sagar_write_log( $data ) {
    if ( true === WP_DEBUG ) {
        $log_file_path = WP_CONTENT_DIR . '/my_error_log.log'; // Logs to wp-content/my_error_log.log

        // Get the debug backtrace to find the file, line number, function, or class
        $debug_backtrace = debug_backtrace();
        $caller_file = isset($debug_backtrace[0]['file']) ? $debug_backtrace[0]['file'] : 'N/A';
        $caller_line = isset($debug_backtrace[0]['line']) ? $debug_backtrace[0]['line'] : 'N/A';
        $caller_function = isset($debug_backtrace[1]['function']) ? $debug_backtrace[1]['function'] : 'N/A';
        $caller_class = isset($debug_backtrace[1]['class']) ? $debug_backtrace[1]['class'] : '';

        $timestamp = date("Y-m-d H:i:s");
        $memory_usage = round(memory_get_usage() / 1024 / 1024, 2) . ' MB';
        
        // User context if a user is logged in
        $user_info = is_user_logged_in() ? 'User ID: ' . get_current_user_id() : 'User: Not logged in';

        $data_type = gettype($data);
        $log_entry = "------------------------------------\n";
        $log_entry .= "| Timestamp: $timestamp            \n";
        $log_entry .= "| Memory Usage: $memory_usage      \n";
        $log_entry .= "| File: $caller_file               \n";
        $log_entry .= "| Line: $caller_line               \n";
        $log_entry .= "| Function: $caller_function       \n";
        if ($caller_class) {
            $log_entry .= "| Class: $caller_class             \n";
        }
        $log_entry .= "| $user_info                       \n";
        $log_entry .= "------------------------------------\n";
        $log_entry .= "| Data Type         | Value         |\n";
        $log_entry .= "------------------------------------\n";
        $log_entry .= sprintf("| %-17s | %-12s |\n", $data_type, is_scalar($data) ? $data : print_r($data, true));
        $log_entry .= "------------------------------------\n";

        // Append a newline character for readability in the log file
        error_log($log_entry . "\n", 3, $log_file_path);
    }
}

function sagar_log_last_query() {
    global $wpdb;
    if ( true === WP_DEBUG ) {
        $log_file_path = WP_CONTENT_DIR . '/my_query_log.log'; // Path to the log file
        $timestamp = date("Y-m-d H:i:s");
        $query_info = sprintf(
            "------------------------------------\n".
            "| Timestamp: %s                   \n".
            "| Last Query: %s\n".
            "| Rows Affected: %d\n".
            "| Execution Time: %s\n".
            "------------------------------------\n",
            $timestamp,
            $wpdb->last_query,
            $wpdb->rows_affected,
            $wpdb->timer_stop()
        );

        error_log($query_info . "\n", 3, $log_file_path);
    }
}


function sagar_log_current_hook() {
    if ( true === WP_DEBUG ) {
        $log_file_path = WP_CONTENT_DIR . '/my_current_hook_log.log';
        $timestamp = date("Y-m-d H:i:s");
        $current_hook = current_filter();
        
        $hook_info = sprintf(
            "------------------------------------\n".
            "| Timestamp: %s                   \n".
            "| Current Hook: %s\n".
            "------------------------------------\n",
            $timestamp,
            $current_hook
        );

        error_log($hook_info . "\n", 3, $log_file_path);
    }
}

function sagar_log_user_meta_data( $user_id = null ) {
    if ( true === WP_DEBUG ) {
        if ( ! $user_id && is_user_logged_in() ) {
            $user_id = get_current_user_id();
        }

        if ( $user_id ) {
            $log_file_path = WP_CONTENT_DIR . '/my_user_meta_log.log';
            $timestamp = date("Y-m-d H:i:s");
            $user_meta = get_user_meta( $user_id );

            $log_entry = "------------------------------------\n";
            $log_entry .= "| Timestamp: $timestamp            \n";
            $log_entry .= "| User ID: $user_id                \n";
            $log_entry .= "| User Meta:                       \n";
            $log_entry .= print_r($user_meta, true);
            $log_entry .= "------------------------------------\n";

            error_log($log_entry . "\n", 3, $log_file_path);
        }
    }
}

