<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @author     GeoDirectory Team <info@wpgeodirectory.com>
 */
class UsersWP_Activator {

    /**
     * Background update class.
     *
     * @var object
     */
    private static $background_updater;

    /**
     * This method gets fired during plugin activation.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      void
     */
    public static function activate($network_wide = false) {

        if (is_multisite()) {
            $main_site = get_network()->site_id;
            if($network_wide){

                update_network_option('', 'uwp_is_network_active', 1);
                switch_to_blog( $main_site );
                self::uwp101_create_tables();
                self::uwp_update_usermeta();
                restore_current_blog();
                
                if (defined('UWP_ROOT_PAGES')) {
                    if (UWP_ROOT_PAGES == 'all') {
                        $blog_ids = self::uwp_get_blog_ids();

                        foreach ( $blog_ids as $blog_id ) {
                            switch_to_blog( $blog_id );
                            self::install();
                        }
                        restore_current_blog();
                    } else {
                        $blog_id = UWP_ROOT_PAGES;
                        switch_to_blog( $blog_id );
                        self::install();
                        restore_current_blog();
                    }
                } else {
                    switch_to_blog( $main_site );
                    self::install();
                    restore_current_blog();
                }

            } else {
                self::uwp101_create_tables();
                self::install();
                self::uwp_update_usermeta();
            }
        } else {
            self::uwp101_create_tables();
            self::install();
            self::uwp_update_usermeta();
        }

    }

    public static function install(){

        self::generate_pages();
        self::add_default_options();
        self::uwp_create_tables();

        if (!get_option('uwp_default_data_installed')) {
            self::uwp_create_default_fields();
            self::uwp_insert_form_extras();
            update_option('uwp_default_data_installed', 1);
            update_option('uwp_activation_redirect', 1);
        }

        self::uwp_flush_rewrite_rules();
        update_option('uwp_flush_rewrite', 1);

    }

    /**
     * Get all IDs of blogs that are not activated, not spam, and not deleted
     *
     * @global      object $wpdb
     * @return      array|false Array of IDs or false if none are found
     */
    public static function uwp_get_blog_ids() {
        global $wpdb;

        // Get an array of IDs
        $sql = "SELECT blog_id FROM $wpdb->blogs
                    WHERE archived = '0' AND spam = '0'
                    AND deleted = '0'";

        return $wpdb->get_col( $sql );
    }

    /**
     * Generates the default pages during plugin activation.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      void
     */
    public static function generate_pages() {
        uwp_generate_default_pages();
    }

    /**
     * Adds default settings during plugin activation.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      void
     */
    public static function add_default_options() {

        $settings = get_option( 'uwp_settings', array());

        $options = array(
            'uwp_registration_action' => 'auto_approve',
            'wp_register_redirect' => 1,
            'register_admin_notify' => 0,
            'login_redirect_to' => -1,
            'block_wp_login' => 0,
            'change_enable_old_password' => 1,
            'change_disable_password_nag' => 0,
            'enable_profile_header' => 1,
            'enable_profile_body' => 1,
            'profile_avatar_size' => '',
            'profile_banner_size' => '',
            'profile_banner_width' => 1000,
            'profile_no_of_items' => 10,
            'uwp_disable_author_link' => 0,
            'users_default_layout' => 'list',
            'author_box_enable_disable' => 1,
            'author_box_display_content' => 'below_content',
            'author_box_display_post_types' => array('post'),
            'author_box_content' => UsersWP_Defaults::author_box_content(),
            'registration_success_email_admin' => 1,
            'registration_success_email_subject_admin' => UsersWP_Defaults::email_user_new_account_subject(),
            'registration_success_email_content_admin' => UsersWP_Defaults::email_user_new_account_body(),
            'registration_activate_email' => 1,
            'registration_activate_email_subject' => UsersWP_Defaults::email_user_activation_subject(),
            'registration_activate_email_content' => UsersWP_Defaults::email_user_activation_body(),
            'registration_success_email' => 1,
            'registration_success_email_subject' => UsersWP_Defaults::registration_success_email_subject(),
            'registration_success_email_content' => UsersWP_Defaults::registration_success_email_body(),
            'forgot_password_email' => 1,
            'forgot_password_email_subject' => UsersWP_Defaults::forgot_password_email_subject(),
            'forgot_password_email_content' => UsersWP_Defaults::forgot_password_email_body(),
            'change_password_email' => 1,
            'change_password_email_subject' => UsersWP_Defaults::change_password_email_subject(),
            'change_password_email_content' => UsersWP_Defaults::change_password_email_body(),
            'reset_password_email' => 1,
            'reset_password_email_subject' => UsersWP_Defaults::reset_password_email_subject(),
            'reset_password_email_content' => UsersWP_Defaults::reset_password_email_body(),
            'account_update_email' => 1,
            'account_update_email_subject' => UsersWP_Defaults::update_account_email_subject(),
            'account_update_email_content' => UsersWP_Defaults::update_account_email_body(),
            'enable_profile_tabs' => array('more_info', 'posts', 'comments'),
        );

        foreach ($options as $option => $value){
            if (!isset($settings[$option])) {
                $settings[$option] = $value;
            }
        }

        update_option( 'uwp_settings', $settings );

    }

    /**
     * Creates tables during plugin activation.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      void
     */
    public static function uwp_create_tables()
    {
        uwp_create_tables();
    }

    /**
     * Creates the new tables added in version 1.0.1 during plugin activation.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      void
     */
    public static function uwp101_create_tables() {
        uwp101_create_tables();
    }

    public static function init_background_updater(){
        include_once dirname( __FILE__ ) . '/class-uwp-background-updater.php';
        self::$background_updater = new UsersWP_Background_Updater();
    }

    /**
     * Syncs WP usermeta with UsersWP usermeta during plugin activation.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      void
     */
    public static function uwp_update_usermeta()
    {
        $update_callback = 'uwp_insert_usermeta';
        self::init_background_updater();

        uwp_error_log( sprintf( 'Queuing %s - %s', USERSWP_VERSION, $update_callback ) );
        self::$background_updater->push_to_queue( $update_callback );
        self::$background_updater->save()->dispatch();
    }

    /**
     * Saves default custom fields in the database.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      void
     */
    public static function uwp_create_default_fields()
    {
        $form_builder = new UsersWP_Form_Builder();

        $fields = self::uwp_default_custom_fields();

        $fields = apply_filters('uwp_before_default_custom_fields_saved', $fields);

        foreach ($fields as $field_index => $field) {
            $form_builder->uwp_admin_form_field_save($field);
        }
    }

    /**
     * Returns merged default custom fields.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      array       Merged custom fields.
     */
    public static function uwp_default_custom_fields(){

        $login = self::uwp_default_custom_fields_login();
        $forgot = self::uwp_default_custom_fields_forgot();
        $avatar = self::uwp_default_custom_fields_avatar();
        $banner = self::uwp_default_custom_fields_banner();
        $change = self::uwp_default_custom_fields_change();
        $reset = self::uwp_default_custom_fields_reset();
        $account = self::uwp_default_custom_fields_account();

        $fields = array_merge($login, $forgot, $account, $avatar, $banner, $change, $reset);

        $fields = apply_filters('uwp_default_custom_fields', $fields);

        return $fields;

    }

    /**
     * Returns Login form default custom fields.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      array       Login form default custom fields.
     */
    public static function uwp_default_custom_fields_login(){

        $fields = array();

        $fields[] = array(
            'form_type' => 'login',
            'field_type' => 'text',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Username', 'userswp'),
            'htmlvar_name' => 'username',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1'
        );

        $fields[] = array(
            'form_type' => 'login',
            'field_type' => 'password',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Password', 'userswp'),
            'htmlvar_name' => 'password',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1'
        );

        $fields = apply_filters('uwp_default_custom_fields_login', $fields);

        return  $fields;
    }

    /**
     * Returns Forgot form default custom fields.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      array       Forgot form default custom fields.
     */
    public static function uwp_default_custom_fields_forgot(){

        $fields = array();

        $fields[] = array(
            'form_type' => 'forgot',
            'field_type' => 'email',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Email', 'userswp'),
            'htmlvar_name' => 'email',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1'
        );

        $fields = apply_filters('uwp_default_custom_fields_forgot', $fields);

        return  $fields;
    }

    /**
     * Returns Avatar form default custom fields.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      array       Avatar form default custom fields.
     */
    public static function uwp_default_custom_fields_avatar(){

        $fields = array();

        $fields[] = array(
            'form_type' => 'avatar',
            'field_type' => 'file',
            'data_type' => 'TEXT',
            'site_title' => __('Avatar', 'userswp'),
            'htmlvar_name' => 'file',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1',
            'extra_fields'        =>  array(
                'uwp_file_types'  =>  array(
                    'jpg',
                    'jpe',
                    'jpeg',
                    'gif',
                    'png'
                ),
            )
        );

        $fields = apply_filters('uwp_default_custom_fields_avatar', $fields);

        return  $fields;
    }

    /**
     * Returns Banner form default custom fields.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      array       Banner form default custom fields.
     */
    public static function uwp_default_custom_fields_banner(){

        $fields = array();

        $fields[] = array(
            'form_type' => 'banner',
            'field_type' => 'file',
            'data_type' => 'TEXT',
            'site_title' => __('Banner', 'userswp'),
            'htmlvar_name' => 'file',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1',
            'extra_fields'        =>  array(
                'uwp_file_types'  =>  array(
                    'jpg',
                    'jpe',
                    'jpeg',
                    'gif',
                    'png'
                ),
            )
        );

        $fields = apply_filters('uwp_default_custom_fields_banner', $fields);

        return  $fields;
    }

    /**
     * Returns Change Password form default custom fields.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      array       Change Password form default custom fields.
     */
    public static function uwp_default_custom_fields_change(){

        $fields = array();

        $fields[] = array(
            'form_type' => 'change',
            'field_type' => 'password',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Old Password', 'userswp'),
            'htmlvar_name' => 'old_password',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1'
        );

        $fields[] = array(
            'form_type' => 'change',
            'field_type' => 'password',
            'data_type' => 'XVARCHAR',
            'site_title' => __('New Password', 'userswp'),
            'htmlvar_name' => 'password',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1'
        );

        $fields[] = array(
            'form_type' => 'change',
            'field_type' => 'password',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Confirm Password', 'userswp'),
            'htmlvar_name' => 'confirm_password',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1'
        );

        $fields = apply_filters('uwp_default_custom_fields_change', $fields);

        return  $fields;
    }

    /**
     * Returns Reset Password form default custom fields.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      array       Reset Password form default custom fields.
     */
    public static function uwp_default_custom_fields_reset(){

        $fields = array();

        $fields[] = array(
            'form_type' => 'reset',
            'field_type' => 'password',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Password', 'userswp'),
            'htmlvar_name' => 'password',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1'
        );

        $fields[] = array(
            'form_type' => 'reset',
            'field_type' => 'password',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Confirm Password', 'userswp'),
            'htmlvar_name' => 'confirm_password',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1'
        );

        $fields = apply_filters('uwp_default_custom_fields_reset', $fields);

        return  $fields;
    }

    /**
     * Returns Account form default custom fields.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      array       Account form default custom fields.
     */
    public static function uwp_default_custom_fields_account(){

        $fields = array();

        $fields[] = array(
            'form_type' => 'account',
            'field_type' => 'text',
            'data_type' => 'XVARCHAR',
            'site_title' => __('First Name', 'userswp'),
            'htmlvar_name' => 'first_name',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_public' => '1',
            'is_active' => '1',
            'is_required' => '1',
            'is_register_field' => '1',
            'is_search_field' => '1',
            'css_class' => 'uwp-half uwp-half-left',
        );

        $fields[] = array(
            'form_type' => 'account',
            'field_type' => 'text',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Last Name', 'userswp'),
            'htmlvar_name' => 'last_name',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_public' => '1',
            'is_active' => '1',
            'is_required' => '1',
            'is_register_field' => '1',
            'is_search_field' => '1',
            'css_class' => 'uwp-half uwp-half-right',
        );

        $fields[] = array(
            'form_type' => 'account',
            'field_type' => 'text',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Username', 'userswp'),
            'htmlvar_name' => 'username',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_public' => '1',
            'is_active' => '1',
            'is_required' => '1',
            'is_register_field' => '1',
            'is_search_field' => '1',
            'is_register_only_field' => '1',
        );

        $fields[] = array(
            'form_type' => 'account',
            'field_type' => 'text',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Display Name', 'userswp'),
            'htmlvar_name' => 'display_name',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_public' => '1',
            'is_active' => '1',
            'is_required' => '0',
            'is_register_field' => '0',
            'is_search_field' => '1',
        );

        $fields[] = array(
            'form_type' => 'account',
            'field_type' => 'email',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Email', 'userswp'),
            'htmlvar_name' => 'email',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1',
            'is_register_field' => '1',
            'is_search_field' => '1',
        );


        $fields[] = array(
            'form_type' => 'account',
            'field_type' => 'textarea',
            'data_type' => 'TEXT',
            'site_title' => __('Bio', 'userswp'),
            'htmlvar_name' => 'bio',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_public' => '1',
            'is_required' => '1',
            'is_search_field' => '1',
            'show_in' => array('[profile_side]', '[users]')
        );

        $fields[] = array(
            'form_type' => 'account',
            'field_type' => 'password',
            'data_type' => 'XVARCHAR',
            'site_title' => __('Password', 'userswp'),
            'htmlvar_name' => 'password',
            'default_value' => '',
            'option_values' => '',
            'is_default' => '1',
            'is_active' => '1',
            'is_required' => '1',
            'is_register_field' => '1',
            'is_register_only_field' => '1',
            'extra'        =>  array(
                'confirm_password'  =>  '1'
            )
        );


        $fields = apply_filters('uwp_default_custom_fields_account', $fields);

        return  $fields;
    }

    /**
     * Flushes rewrite rules.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      void
     */
    public static function uwp_flush_rewrite_rules() {
        flush_rewrite_rules();
    }

    /**
     * Inserts register form custom fields in form extras table.
     *
     * @since       1.0.0
     * @package     userswp
     * @return      void
     */
    public static function uwp_insert_form_extras() {
        global $wpdb;
        $extras_table_name = uwp_get_table_prefix() . 'uwp_form_extras';

        $fields = array();

        $fields[] = array(
            'form_type' => 'register',
            'field_type' => 'text',
            'is_default' => '0',
            'htmlvar_name' => 'uwp_account_first_name'
        );

        $fields[] = array(
            'form_type' => 'register',
            'field_type' => 'text',
            'is_default' => '0',
            'htmlvar_name' => 'uwp_account_last_name'
        );

        $fields[] = array(
            'form_type' => 'register',
            'field_type' => 'text',
            'is_default' => '1',
            'htmlvar_name' => 'uwp_account_username'
        );

        $fields[] = array(
            'form_type' => 'register',
            'field_type' => 'email',
            'is_default' => '1',
            'htmlvar_name' => 'uwp_account_email'
        );

        $fields[] = array(
            'form_type' => 'register',
            'field_type' => 'password',
            'is_default' => '0',
            'htmlvar_name' => 'uwp_account_password'
        );


        foreach ($fields as $field) {
            $last_order = $wpdb->get_var("SELECT MAX(sort_order) as last_order FROM " . $extras_table_name);
            $sort_order = (int)$last_order + 1;
            $wpdb->query(
                $wpdb->prepare(

                    "insert into " . $extras_table_name . " set
                        form_type = %s,
                        field_type = %s,
                        is_default = %s,
                        site_htmlvar_name = %s,
                        sort_order = %s",
                    array(
                        $field['form_type'],
                        $field['field_type'],
                        $field['is_default'],
                        $field['htmlvar_name'],
                        $sort_order
                    )
                )
            );
        }
    }

    public static function uwp_automatic_upgrade(){
        $uwp_db_version = get_option('uwp_db_version');

        if ( $uwp_db_version != USERSWP_VERSION ) {
            self::activate(is_plugin_active_for_network( 'userswp/userswp.php' ));
        }
    }

}