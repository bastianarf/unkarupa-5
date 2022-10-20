<?php 

class WPFDUSettings
{
    private $options;
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }
    
    public function add_plugin_page() {
        $hook = add_options_page('Settings Admin', esc_html__('WPForms Drop Uploader Settings', 'wpf-drop-uploader'), 'manage_options', 'wpfdu-setting-admin', array($this, 'create_admin_page'));
        
        add_action('load-' . $hook, array($this, 'generate_wpfdu_css'));
        
    }
    
    public function create_admin_page() {
        $this->options = get_option('wpfdu_options');
?>
        <div class="wrap">
            <h2><?php echo esc_html__('WPForms Drop Uploader Settings', 'wpf-drop-uploader') ?></h2>     
            <form method="post" action="options.php">
            <?php
        settings_fields('wpfdu_option_group');
        do_settings_sections('wpfdu-setting-admin');
        submit_button();
?>
            </form>
        </div>
        <script>
        jQuery(document).ready(function($){
            var BorderOptions = {
                defaultColor: '#999999',
            };
        
            jQuery('#wpfdu_border_color').wpColorPicker(BorderOptions);
            
            var BorderHoverOptions = {
                defaultColor: '#4A90E2',
            };
            
            jQuery('#wpfdu_border_hover_color').wpColorPicker(BorderHoverOptions);

            var ProgressBarOptions = {
                defaultColor: '#4A90E2',
            };
            
            jQuery('#wpfdu_progress_bar_color').wpColorPicker(ProgressBarOptions);
            
            var TitleOptions = {
                defaultColor: '#9B9B9B',
            };
            
            jQuery('#wpfdu_title_color').wpColorPicker(TitleOptions);

            var IconOptions = {
                defaultColor: '#9B9B9B',
            };
            
            jQuery('#wpfdu_icon_color').wpColorPicker(IconOptions);
            
        });
        </script>
        <?php
    }
    
    public function page_init() {
        register_setting('wpfdu_option_group', 'wpfdu_options', array($this, 'sanitize'));
        
        // Sections

        add_settings_section('wpfdu_style', esc_html__('Drop Uploader Style', 'wpf-drop-uploader'), array($this, 'print_wpfdu_style'), 'wpfdu-setting-admin');
        
        add_settings_section('wpfdu_options', esc_html__('Drop Uploader Options', 'wpf-drop-uploader'), array($this, 'print_wpfdu_options'), 'wpfdu-setting-admin');

        // Fields

        add_settings_field('wpfdu_border_color', esc_html__('Border Color', 'wpf-drop-uploader'), array($this, 'wpfdu_border_color_callback'), 'wpfdu-setting-admin', 'wpfdu_style');
        
        add_settings_field('wpfdu_border_hover_color', esc_html__('Border Hover Color', 'wpf-drop-uploader'), array($this, 'wpfdu_border_hover_color_callback'), 'wpfdu-setting-admin', 'wpfdu_style');

        add_settings_field('wpfdu_progress_bar_color', esc_html__('Progress Bar Color', 'wpf-drop-uploader'), array($this, 'wpfdu_progress_bar_color_callback'), 'wpfdu-setting-admin', 'wpfdu_style');
        
        add_settings_field('wpfdu_border_thickness', esc_html__('Border Thickness', 'wpf-drop-uploader'), array($this, 'wpfdu_border_thickness_callback'), 'wpfdu-setting-admin', 'wpfdu_style');
        
        add_settings_field('wpfdu_title_color', esc_html__('Title Color', 'wpf-drop-uploader'), array($this, 'wpfdu_title_color_callback'), 'wpfdu-setting-admin', 'wpfdu_style');
        
        add_settings_field('wpfdu_icon_color', esc_html__('Icon Color', 'wpf-drop-uploader'), array($this, 'wpfdu_icon_color_callback'), 'wpfdu-setting-admin', 'wpfdu_style');
        
        add_settings_field('wpfdu_browse_link_css_classes', esc_html__('Browse Link CSS Classes', 'wpf-drop-uploader'), array($this, 'wpfdu_browse_link_css_classes_callback'), 'wpfdu-setting-admin', 'wpfdu_options');

        add_settings_field('wpfdu_uploader_icon', esc_html__('Uploader Icon', 'wpf-drop-uploader'), array($this, 'wpfdu_uploader_icon_callback'), 'wpfdu-setting-admin', 'wpfdu_options');

        add_settings_field('wpfdu_file_icon', esc_html__('File Icon', 'wpf-drop-uploader'), array($this, 'wpfdu_file_icon_callback'), 'wpfdu-setting-admin', 'wpfdu_options');

        add_settings_field('wpfdu_drop_files_text', esc_html__('Drop Files Here text', 'wpf-drop-uploader'), array($this, 'wpfdu_drop_files_text_callback'), 'wpfdu-setting-admin', 'wpfdu_options');

        add_settings_field('wpfdu_browse_text', esc_html__('Browse text', 'wpf-drop-uploader'), array($this, 'wpfdu_browse_text_callback'), 'wpfdu-setting-admin', 'wpfdu_options');

        add_settings_field('wpfdu_time_show_errors', esc_html__('Time Show Errors', 'wpf-drop-uploader'), array($this, 'wpfdu_time_show_errors_callback'), 'wpfdu-setting-admin', 'wpfdu_options');

        add_settings_field('wpfdu_layout', esc_html__('Layout', 'wpf-drop-uploader'), array($this, 'wpfdu_layout_callback'), 'wpfdu-setting-admin', 'wpfdu_options');

        add_settings_field('wpfdu_attach', esc_html__('Attach Files', 'wpf-drop-uploader'), array($this, 'wpfdu_attach_callback'), 'wpfdu-setting-admin', 'wpfdu_options');

        //add_settings_field('wpfdu_archive_attachments', esc_html__('Send attachments as archive', 'wpf-drop-uploader'), array($this, 'wpfdu_archive_attachments_callback'), 'wpfdu-setting-admin', 'wpfdu_options');

        add_settings_field('wpfdu_store', esc_html__('Store Files', 'wpf-drop-uploader'), array($this, 'wpfdu_store_callback'), 'wpfdu-setting-admin', 'wpfdu_options');
        
    }
    
    public function sanitize($input) {
        $new_input = array();

        $allowed_icon_html = array(
            'i' => array(
                'class' => true
            ),
            'span' => array(
                'class' => true
            )
        );

        if (isset($input['wpfdu_border_color'])) $new_input['wpfdu_border_color'] = sanitize_text_field($input['wpfdu_border_color']);
        
        if (isset($input['wpfdu_border_hover_color'])) $new_input['wpfdu_border_hover_color'] = sanitize_text_field($input['wpfdu_border_hover_color']);

        if (isset($input['wpfdu_progress_bar_color'])) $new_input['wpfdu_progress_bar_color'] = sanitize_text_field($input['wpfdu_progress_bar_color']);
        
        if (isset($input['wpfdu_border_thickness'])) $new_input['wpfdu_border_thickness'] = sanitize_text_field($input['wpfdu_border_thickness']);
        
        if (isset($input['wpfdu_title_color'])) $new_input['wpfdu_title_color'] = sanitize_text_field($input['wpfdu_title_color']);
        
        if (isset($input['wpfdu_icon_color'])) $new_input['wpfdu_icon_color'] = sanitize_text_field($input['wpfdu_icon_color']);
        
        if (isset($input['wpfdu_browse_link_css_classes'])) $new_input['wpfdu_browse_link_css_classes'] = sanitize_text_field($input['wpfdu_browse_link_css_classes']);
        
        if (isset($input['wpfdu_uploader_icon'])) $new_input['wpfdu_uploader_icon'] = wp_kses($input['wpfdu_uploader_icon'], $allowed_icon_html);
        
        if (isset($input['wpfdu_file_icon'])) $new_input['wpfdu_file_icon'] = wp_kses($input['wpfdu_file_icon'], $allowed_icon_html);

        if (isset($input['wpfdu_drop_files_text'])) $new_input['wpfdu_drop_files_text'] = sanitize_text_field($input['wpfdu_drop_files_text']);

        if (isset($input['wpfdu_browse_text'])) $new_input['wpfdu_browse_text'] = sanitize_text_field($input['wpfdu_browse_text']);
        
        if (isset($input['wpfdu_time_show_errors'])) $new_input['wpfdu_time_show_errors'] = sanitize_text_field($input['wpfdu_time_show_errors']);

        if (isset($input['wpfdu_layout'])) $new_input['wpfdu_layout'] = sanitize_text_field($input['wpfdu_layout']);

        if (isset($input['wpfdu_attach'])) $new_input['wpfdu_attach'] = sanitize_text_field($input['wpfdu_attach']);

        if (isset($input['wpfdu_archive_attachments'])) $new_input['wpfdu_archive_attachments'] = sanitize_text_field($input['wpfdu_archive_attachments']);

        if (isset($input['wpfdu_store'])) $new_input['wpfdu_store'] = sanitize_text_field($input['wpfdu_store']);
        
        return $new_input;
    }
    
    // Print

    public function print_wpfdu_style() {
        //print 'Drop Uploader Style:';
    }

    public function print_wpfdu_options() {
        //print 'Drop Uploader Options:';
    }
    
    // Callback

    public function wpfdu_border_color_callback() {
        printf('<input type="text" id="wpfdu_border_color" name="wpfdu_options[wpfdu_border_color]" value="%s" />', isset($this->options['wpfdu_border_color']) ? esc_attr($this->options['wpfdu_border_color']) : '#999999');
    }
    
    public function wpfdu_border_hover_color_callback() {
        printf('<input type="text" id="wpfdu_border_hover_color" name="wpfdu_options[wpfdu_border_hover_color]" value="%s" />', isset($this->options['wpfdu_border_hover_color']) ? esc_attr($this->options['wpfdu_border_hover_color']) : '#4A90E2');
    }

    public function wpfdu_progress_bar_color_callback() {
        printf('<input type="text" id="wpfdu_progress_bar_color" name="wpfdu_options[wpfdu_progress_bar_color]" value="%s" />', isset($this->options['wpfdu_progress_bar_color']) ? esc_attr($this->options['wpfdu_progress_bar_color']) : '#4A90E2');
    }

    public function wpfdu_border_thickness_callback() {
        printf('<input type="text" id="wpfdu_border_thickness" name="wpfdu_options[wpfdu_border_thickness]" size="4" value="%s" /> px', isset($this->options['wpfdu_border_thickness']) ? esc_attr($this->options['wpfdu_border_thickness']) : '3');
    }

    public function wpfdu_title_color_callback() {
        printf('<input type="text" id="wpfdu_title_color" name="wpfdu_options[wpfdu_title_color]" value="%s" />', isset($this->options['wpfdu_title_color']) ? esc_attr($this->options['wpfdu_title_color']) : '#9B9B9B');
    }

    public function wpfdu_icon_color_callback() {
        printf('<input type="text" id="wpfdu_icon_color" name="wpfdu_options[wpfdu_icon_color]" value="%s" />', isset($this->options['wpfdu_icon_color']) ? esc_attr($this->options['wpfdu_icon_color']) : '#9B9B9B');
    }

    public function wpfdu_browse_link_css_classes_callback() {
        printf('<input type="text" id="wpfdu_browse_link_css_classes" name="wpfdu_options[wpfdu_browse_link_css_classes]" size="40" value="%s" />', isset($this->options['wpfdu_browse_link_css_classes']) ? esc_attr($this->options['wpfdu_browse_link_css_classes']) : '');
    }

    public function wpfdu_uploader_icon_callback() {
        printf('<input type="text" id="wpfdu_uploader_icon" name="wpfdu_options[wpfdu_uploader_icon]" size="40" value="%s" /><br/><small>'.esc_html__('Use icons from', 'wpf-drop-uploader').' <a href="http://themes-pixeden.com/font-demos/7-stroke/" target="_blank">'.esc_html__('this pack', 'wpf-drop-uploader').'</a></small>', isset($this->options['wpfdu_uploader_icon']) ? esc_attr($this->options['wpfdu_uploader_icon']) : esc_attr('<i class="pe-7s-cloud-upload"></i>'));
    }

    public function wpfdu_file_icon_callback() {
        printf('<input type="text" id="wpfdu_file_icon" name="wpfdu_options[wpfdu_file_icon]" size="40" value="%s" /><br/><small>'.esc_html__('Use icons from', 'wpf-drop-uploader').' <a href="http://themes-pixeden.com/font-demos/7-stroke/" target="_blank">'.esc_html__('this pack', 'wpf-drop-uploader').'</a></small>', isset($this->options['wpfdu_file_icon']) ? esc_attr($this->options['wpfdu_file_icon']) : esc_attr('<i class="pe-7s-file"></i>'));
    }

    public function wpfdu_drop_files_text_callback() {
        printf('<input type="text" id="wpfdu_drop_files_text" name="wpfdu_options[wpfdu_drop_files_text]" size="40" value="%s" /><br/><small>'.esc_html__("If empty - will be used default translation").'</small>', isset($this->options['wpfdu_drop_files_text']) ? esc_attr($this->options['wpfdu_drop_files_text']) : '');
    }

    public function wpfdu_browse_text_callback() {
        printf('<input type="text" id="wpfdu_browse_text" name="wpfdu_options[wpfdu_browse_text]" size="40" value="%s" /><br/><small>'.esc_html__("If empty - will be used default translation").'</small>', isset($this->options['wpfdu_browse_text']) ? esc_attr($this->options['wpfdu_browse_text']) : '');
    }

    public function wpfdu_time_show_errors_callback() {
        printf('<input type="text" id="wpfdu_time_show_errors" name="wpfdu_options[wpfdu_time_show_errors]" size="5" value="%s" /> '.esc_html__('sec', 'wpf-drop-uploader'), isset($this->options['wpfdu_time_show_errors']) ? esc_attr($this->options['wpfdu_time_show_errors']) : '5');
    }

    public function wpfdu_layout_callback() {
        ?>
        <select id="wpfdu_layout" name="wpfdu_options[wpfdu_layout]" >
              <option value="thumbnails" <?php
        if ($this->options['wpfdu_layout'] == 'thumbnails') echo 'selected' ?>><?php echo esc_html__('Thumbnails', 'wpf-drop-uploader')?></option>
              <option value="list" <?php
        if ($this->options['wpfdu_layout'] == 'list') echo 'selected' ?>><?php echo esc_html__('List', 'wpf-drop-uploader') ?></option>
            </select><br>
        <?php
    }

    public function wpfdu_attach_callback() {
        printf('<input type="checkbox" id="wpfdu_attach" name="wpfdu_options[wpfdu_attach]" value="checked" %s />'.esc_html__('Attach uploaded files to email.', 'wpf-drop-uploader'), isset($this->options['wpfdu_attach']) ? esc_attr($this->options['wpfdu_attach']) : '');
    }

    public function wpfdu_archive_attachments_callback() {
        printf('<input type="checkbox" id="wpfdu_archive_attachments" name="wpfdu_options[wpfdu_archive_attachments]" value="checked" %s />'.esc_html__('Set this checkbox, if you want to send attached files as a zip archive.', 'wpf-drop-uploader'), isset($this->options['wpfdu_archive_attachments']) ? esc_attr($this->options['wpfdu_archive_attachments']) : '');
    }    

    public function wpfdu_store_callback() {
        printf('<input type="checkbox" id="wpfdu_store" name="wpfdu_options[wpfdu_store]" value="checked" %s />'.esc_html__('Send links to uploaded files in email.', 'wpf-drop-uploader'), isset($this->options['wpfdu_store']) ? esc_attr($this->options['wpfdu_store']) : '');
    }
    
    // Generate CSS file after settings was saved
    function generate_wpfdu_css() {
        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            ob_start();
            require ('css/drop_uploader.php');
            $css = ob_get_clean();
            file_put_contents(plugin_dir_path(__FILE__ ).'/css/drop_uploader.css', $css, LOCK_EX);
        }
    }
}

if (is_admin()) $wpfdu_settings = new WPFDUSettings();