<?php
class Spark_Build{
    
    public static function spark_create_build_table(){
        global $wpdb;
        $spark_build_db_version = '1.0.0';
        $table_name = $wpdb->prefix . 'spark_build';
        $charset_collate = $wpdb->get_charset_collate();
        /**
         * -- id mediumint(9) NOT NULL AUTO_INCREMENT,
         * -- PRIMARY KEY  (id)
         */
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            time DATETIME NOT NULL,
            message TEXT NOT NULL,
            token TEXT NOT NULL,
            status TEXT NOT NULL
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $status = dbDelta( $sql );
        add_option( 'spark_build_db_version', $spark_build_db_version );
    }


}

?>