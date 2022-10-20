<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: WPForms Drop Uploader
 * Plugin URI: https://codecanyon.net/item/drop-uploader-for-wpforms-dragdrop-file-uploader-addon/21945649
 * Description: WPForms Drop Uploader WordPress plugin allows you to add powerful Drag &amp; Drop File Uploading area to your Form, that was built with WPForms. You can add several file uploading areas for one form and set file maximum size and file type for each of them.
 * Version: 1.0.2
 * Author: borisolhor
 * Author URI: http://codecanyon.net/user/borisolhor
 * Text Domain: wpf-drop-uploader
 *
 * Copyright 2018 borisolhor.
 */

define("WPFDU_FOLDER", "wpfdu_upload");

define("WPFDU_TMP", "wpfdu_temp");

if(function_exists('random_bytes')) {
    $rand_submission_id = bin2hex(random_bytes(16));
} else {
    $rand_submission_id = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
}

/* Add Database Table */

global $wpfdu_db_version;
$wpfdu_db_version = "1.0";

register_activation_hook(__FILE__, 'wpfdu_install');

function wpfdu_install() {
    global $wpdb;
    global $wpfdu_db_version;

    $table_name = $wpdb->prefix . "wpf_drop_uploader";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
        $sql = "CREATE TABLE " . $table_name . " (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            fileid VARCHAR(32) NOT NULL,
            filepath text NOT NULL,
            created_at datetime NOT NULL,
            UNIQUE KEY id (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
 
        add_option("wpfdu_db_version", $wpfdu_db_version);

   }
}

/* Resources */

add_action('init', 'wpfdu_resources');

function wpfdu_resources() {
    
    wp_register_style('drop_uploader', plugins_url('css/drop_uploader.css', __FILE__));
    wp_enqueue_style('drop_uploader');

    wp_register_style('pe-icon-7-stroke', plugins_url('pe-icon-7-stroke/css/pe-icon-7-stroke.css', __FILE__));
    wp_enqueue_style('pe-icon-7-stroke');
    
    wp_enqueue_script('jquery');
    
    wp_register_script('drop_uploader', plugins_url('js/drop_uploader.js', __FILE__), array('jquery'));
    wp_enqueue_script('drop_uploader');

    wp_register_script('wp_drop_uploader', plugins_url('js/wp_drop_uploader.js', __FILE__), array('drop_uploader'));

    load_plugin_textdomain( 'wpf-drop-uploader', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 

    $options = get_option( 'wpfdu_options' );

    $browse_link_css_classes = $options['wpfdu_browse_link_css_classes']?$options['wpfdu_browse_link_css_classes']:'';

    $uploader_icon = $options['wpfdu_uploader_icon']?$options['wpfdu_uploader_icon']:'<i class="pe-7s-cloud-upload"></i>';

    $file_icon = $options['wpfdu_file_icon']?$options['wpfdu_file_icon']:'<i class="pe-7s-file"></i>';

    $progress_color = $options['wpfdu_progress_bar_color']?$options['wpfdu_progress_bar_color']:'#4A90E2';

    $drop_files_text = $options['wpfdu_drop_files_text']?$options['wpfdu_drop_files_text']:esc_html__("Drop files here, or", "wpf-drop-uploader");

    $browse_text = $options['wpfdu_browse_text']?$options['wpfdu_browse_text']:esc_html__("Browse", "wpf-drop-uploader");

    $time_show_errors = $options['wpfdu_time_show_errors']?$options['wpfdu_time_show_errors']:'5';

    $layout = $options['wpfdu_layout']?$options['wpfdu_layout']:'thumbnails';

    $du_localize_array = array(
        'uploader_text' => $drop_files_text,
        'browse_text' => $browse_text,
        'only_one_error_text' => esc_html__("Only one file allowed", "wpf-drop-uploader"),
        'not_allowed_error_text' => esc_html__("File type is not allowed", "wpf-drop-uploader"),
        'big_file_before_error_text' => esc_html__("Files, bigger than", "wpf-drop-uploader"),
        'big_file_after_error_text' => esc_html__("is not allowed", "wpf-drop-uploader"),
        'allowed_before_error_text' => esc_html__("Only", "wpf-drop-uploader"),
        'allowed_after_error_text' => esc_html__("files allowed", "wpf-drop-uploader"),
        'browse_css_class' => $browse_link_css_classes,
        'uploader_icon' => $uploader_icon,
        'file_icon' => $file_icon,
        'progress_color' => $progress_color,
        'time_show_errors' => $time_show_errors,
        'layout' => $layout,
        'ajax_url' => admin_url('admin-ajax.php')
    );
    wp_localize_script('wp_drop_uploader', 'wpfdu_localize', $du_localize_array);
    wp_enqueue_script('wp_drop_uploader');
}

add_action('admin_enqueue_scripts', 'wpfdu_admin_resources');

function wpfdu_admin_resources() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    wp_register_style('drop_uploader', plugins_url('css/drop_uploader.css', __FILE__));
    wp_enqueue_style('drop_uploader');
}

add_action( 'plugins_loaded', 'wpfdu_override' );

function wpfdu_override() {
    if( class_exists('WPForms_Field') ) {
        do_action('wpfdu_res');

        require_once 'classes/class-drop-uploader.php';
    }   
}

/* Process Form Data */

add_filter( 'wpforms_process_filter', 'wpfdu_proccess_drop_uploader', 10, 3 );

function wpfdu_proccess_drop_uploader($fields, $entry, $form_data) {

    global $wpdb;

    global $rand_submission_id;

    $table_name = $wpdb->prefix . "wpf_drop_uploader";

    $wpfdu_options = get_option( 'wpfdu_options' );

    $field_ids = array();

    $new_value = "";

    // Get Drop Uploader field id's

    if( array($fields) ) {

        foreach ($fields as $key => $field) {

            if( $field['type'] == 'drop-uploader' ) {

                $field_ids[] = $key;
            }
        }
    }
    
    // Get files id's

    if( count( $field_ids ) > 0 ) {

        foreach ($field_ids as $field_id) {

            if( isset($entry['fields'][$field_id]) ) {

                $old_value_array = $entry['fields'][$field_id];

                // Process value

                if( is_array( $old_value_array ) ) {

                    foreach ($old_value_array as $old_value) {

                        $res = $wpdb->get_row("SELECT * FROM $table_name WHERE fileid = '$old_value'");

                        $filepath = $res->filepath;

                        $filepath_array = explode(DIRECTORY_SEPARATOR, $filepath);

                        $filepath_array_lenght = count($filepath_array);

                        $filename = $filepath_array[$filepath_array_lenght - 1];

                        // Replace file id's to names or links

                        if( isset($wpfdu_options['wpfdu_store']) && $wpfdu_options['wpfdu_store'] == 'checked' ) {
                            
                            // Store files on, move files

                            $wp_upload_dir = wp_upload_dir();

                            $wpfdu_upload_dir = $wp_upload_dir['basedir'].DIRECTORY_SEPARATOR.WPFDU_FOLDER.DIRECTORY_SEPARATOR.$rand_submission_id.DIRECTORY_SEPARATOR;

                            if (!file_exists($wpfdu_upload_dir)) {
                                mkdir($wpfdu_upload_dir, 0750, true);
                                protect_dir($wpfdu_upload_dir);
                            }

                            $new_file_path = $wpfdu_upload_dir.$filename;

                            if(copy($filepath, $new_file_path)) {

                                // Return links

                                $new_value .=  "\n".$wp_upload_dir['baseurl']."/".WPFDU_FOLDER."/".$rand_submission_id."/".urlencode($filename);
                            }

                        } else {

                            // Store files off, return names

                            $new_value .= $filename."\n";
                        }
                    }
                }

                $fields[$field_id]['file_ids'] = $old_value_array;

                $fields[$field_id]['value'] = $new_value;
            }
        }
    }

    return $fields;
}

/* Add Attachments */

add_filter( 'wpforms_email_attachments', 'wpfdu_add_attachments', 10, 2 );

function wpfdu_add_attachments( $attachments, $form ) {

    global $wpdb;

    $table_name = $wpdb->prefix . "wpf_drop_uploader";

    $wpfdu_options = get_option( 'wpfdu_options' );

    if( isset($wpfdu_options['wpfdu_attach']) && $wpfdu_options['wpfdu_attach'] == 'checked' ) {

        if( is_array($form->fields) ) {

            foreach ($form->fields as $key => $field) {

                if( $field['type'] == 'drop-uploader' ) {

                    if( isset($field['file_ids']) ) {

                        if( is_array($field['file_ids']) ) {

                            foreach ($field['file_ids'] as $file_id) {

                                $res = $wpdb->get_row("SELECT * FROM $table_name WHERE fileid = '$file_id'");

                                $attachments[] = $res->filepath;
                            }
                        }
                    }
                }
            }
        }
    }

    return $attachments;

}

/* AJAX Upload */

add_action('wp_ajax_wpfdu_upload_file', 'wpfdu_upload_file');
add_action('wp_ajax_nopriv_wpfdu_upload_file', 'wpfdu_upload_file');

function wpfdu_upload_file() {

    global $wpdb;

    global $rand_submission_id;

    $wpfdu_options = get_option( 'wpfdu_options' );

    $table_name = $wpdb->prefix . "wpf_drop_uploader";

    // Check is delete action

    if(isset($_REQUEST['fileid'])) {

        $file_id = $_REQUEST['fileid'];

        $res = $wpdb->get_row("SELECT * FROM $table_name WHERE fileid = '$file_id'");

        $file_url = $res->filepath;

        if($file_url != "") {

            unlink($file_url);
        }

        wp_die();
    }

    $error_message = '';

    $field_name_array = array_keys($_FILES['wpforms']['name']['fields']);

    $field_name = $field_name_array[0];

    if(isset($_FILES['wpforms']['error']['fields'][$field_name])) {
        $code = $_FILES['wpforms']['error']['fields'][$field_name];
        switch ($code) { 
            case UPLOAD_ERR_OK: 
                $error_message .= ""; 
                break;
            case UPLOAD_ERR_INI_SIZE: 
                $error_message = esc_html__( "The uploaded file exceeds the upload_max_filesize directive in php.ini", "wpf-drop-uploader" ); 
                break; 
            case UPLOAD_ERR_FORM_SIZE: 
                $error_message = esc_html__( "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", "wpf-drop-uploader" ); 
                break; 
            case UPLOAD_ERR_PARTIAL: 
                $error_message = esc_html__( "The uploaded file was only partially uploaded", "wpf-drop-uploader" ); 
                break; 
            case UPLOAD_ERR_NO_FILE: 
                $error_message = esc_html__( "No file was uploaded", "wpf-drop-uploader" ); 
                break; 
            case UPLOAD_ERR_NO_TMP_DIR: 
                $error_message = esc_html__( "Missing a temporary folder", "wpf-drop-uploader" ); 
                break; 
            case UPLOAD_ERR_CANT_WRITE: 
                $error_message = esc_html__( "Failed to write file to disk", "wpf-drop-uploader" ); 
                break; 
            case UPLOAD_ERR_EXTENSION: 
                $error_message = esc_html__( "File upload stopped by extension", "wpf-drop-uploader" ); 
                break; 
            default: 
                $error_message = esc_html__( "Unknown upload error", "wpf-drop-uploader" ); 
                break; 
        } 
    } elseif(!isset($_FILES['wpforms']['error']['fields'][$field_name])) {

        $error_message = esc_html__( "File not recieved", "wpf-drop-uploader" );
    }

    $wp_upload_dir = wp_upload_dir();

    $uploaddir = $wp_upload_dir['basedir'].DIRECTORY_SEPARATOR.WPFDU_TMP.DIRECTORY_SEPARATOR.$rand_submission_id.DIRECTORY_SEPARATOR;

    if(is_multisite()){ 
        $uploaddir = $wp_upload_dir['basedir'].DIRECTORY_SEPARATOR.'sites'.DIRECTORY_SEPARATOR.get_current_blog_id().DIRECTORY_SEPARATOR.WPFDU_TMP.DIRECTORY_SEPARATOR.$rand_submission_id.DIRECTORY_SEPARATOR;
    }

    if (!file_exists($uploaddir)) {
        mkdir($uploaddir, 0750, true);
        protect_dir($uploaddir);
    }

    if(isset($_FILES['wpforms']['name']['fields'][$field_name])) {
        $uploadfile = $uploaddir.wpfdu_sanitize($_FILES['wpforms']['name']['fields'][$field_name]);
        // Security check
        if(wpfdu_is_file_safe($_FILES['wpforms']['name']['fields'][$field_name])) {
            // Move uploaded file
            if(move_uploaded_file($_FILES['wpforms']['tmp_name']['fields'][$field_name], $uploadfile)) {
                // ok
                $file_id = md5($uploadfile);
                // Store File id in database. We can delete file by this id from other script
                $wpdb->insert( 
                    $table_name, 
                    array( 
                        'fileid' => $file_id, 
                        'filepath' => $uploadfile,
                        'created_at' => date("Y-m-d H:i:s"),
                    )
                );
                $success_message = $file_id;
            } else {
                $error_message = esc_html__( "File not recieved", "wpf-drop-uploader" );
            }
        } else {
            $error_message = esc_html__( "File type is not allowed", "wpf-drop-uploader" );
        }
    }

    if($error_message != "") {
        $response['success'] = false;
        $response['message'] = $error_message;
    } else {
        $response['success'] = true;
        $response['file_id'] = $file_id;
    }

    echo json_encode($response);
    wp_die();
}

/* Security check */

function wpfdu_is_file_safe($file) {

    $file_name_array = explode(DIRECTORY_SEPARATOR, $file);

    $file_name_array_len = count($file_name_array);

    $file_name_array_last = $file_name_array_len - 1;

    $file_name = $file_name_array[$file_name_array_last];

    if(
        stripos($file_name, '.php') !== FALSE ||
        stripos($file_name, '.phtml') !== FALSE ||
        stripos($file_name, '.shtml') !== FALSE ||
        stripos($file_name, '.pl') !== FALSE ||
        stripos($file_name, '.py') !== FALSE ||
        stripos($file_name, '.rb') !== FALSE ||
        stripos($file_name, '.cgi') !== FALSE ||
        stripos($file_name, '.asp') !== FALSE ||
        stripos($file_name, '.aspx') !== FALSE
    ) {
        return false;
    }

    return true;
}

/* Sanitize */

function wpfdu_sanitize ($string = '') {
    return $string;
    // remove html tags
    $clean = strip_tags($string);
    // transliterate
    if(function_exists('transliterator_transliterate')){
        $clean = transliterator_transliterate('Any-Latin;Latin-ASCII;', $clean);
    }
    // remove non-number and non-letter characters
    $clean = str_replace('--', '-', preg_replace('/[^a-z0-9-.\_]/i', '', preg_replace(array(
        '/\s/',
        '/[^\w-\.\-]/'
    ), array(
        '_',
        ''
    ), $clean)));
    // replace '-' for '_'
    $clean = strtr($clean, array(
        '-' => '_'
    ));
    // remove double '__'
    $positionInString = stripos($clean, '__');
    while ($positionInString !== false) {
        $clean = str_replace('__', '_', $clean);
        $positionInString = stripos($clean, '__');
    }
    // remove '_' from the end and beginning of the string
    $clean = rtrim(ltrim($clean, '_'), '_');
    // lowercase the string
    return strtolower($clean);
}

/* Remove old uploaded files */

add_action('init', 'wpfdu_clean_files');

function wpfdu_clean_files() {
    
    global $wpdb;

    $table_name = $wpdb->prefix . "wpf_drop_uploader";

    $hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));

    $old_files = $wpdb->get_results( "SELECT * FROM $table_name WHERE created_at < '$hour_ago'" );

    if( is_array($old_files) ) {

        $id_to_delete = "";

        foreach ($old_files as $old_file) {

            $old_filepath = $old_file->filepath;

            $old_dir_array = explode(DIRECTORY_SEPARATOR, $old_filepath);

            if( is_array($old_dir_array) ) {

                $dir_path_length = count($old_dir_array);

                $old_dirpath = "";

                for ($i=0; $i < $dir_path_length - 1; $i++) { 
                    $old_dirpath .= $old_dir_array[$i].DIRECTORY_SEPARATOR;
                }
            }

            unlink($old_filepath);

            if (is_dir($old_dirpath)) {
                rmdir($old_dirpath);
            }

            $id_to_delete .= $old_file->id.",";

        }

        $id_to_delete = rtrim($id_to_delete, ',');

        if($id_to_delete != "") {

            $wpdb->query( "DELETE FROM $table_name WHERE ID IN($id_to_delete)" );
        }
    }
}

/* Protect Dir */

function protect_dir($dir) {
    // Make .htaccess file in a dir

    $htaccess_content = '<Files ~ "\.(php|php3|php4|php5|phtml|shtml|py|pl|cgi|pcgi4|pcgi5)$">
         Order Deny,Allow
         Deny from All
    </Files>';

    $htaccess_file = fopen($dir.".htaccess", "w");

    fwrite($htaccess_file, $htaccess_content);

    fclose($htaccess_file);
}

/* Settings */

require_once 'wpf-settings.php';