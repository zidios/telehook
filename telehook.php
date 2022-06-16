<?php

/**
 * Plugin Name: TeleHook
 * Description: Webhook for sending form data to telegram
 * Author:      Vladimir Udachin
 * Version:     1.0.0
 *
 * Requires PHP: 7.4
 *
 * License:     MIT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;

}

function telehook_add_settings_page() {
    add_options_page( 'TeleHook settings page', 'TeleHook', 'manage_options', 'telehook-plugin', 'telehook_render_settings_page' );
}
add_action( 'admin_menu', 'telehook_add_settings_page' );

function telehook_render_settings_page() {
    ?>
    <h2>TeleHook Settings</h2>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'telehook_options' );
        do_settings_sections( 'telehook' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

add_filter( 'plugin_action_links_telehook/telehook.php', 'telehook_settings_link' );
function telehook_settings_link( $links ) {
    $url = esc_url( add_query_arg(
        'page',
        'telehook-plugin',
        get_admin_url() . 'admin.php'
    ) );
    $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
    array_push(
        $links,
        $settings_link
    );
    return $links;
}

function telehook_register_settings() {
    register_setting( 'telehook_options', 'telehook_options', 'telehook_options_validate' );
    add_settings_section( 'general_settings', 'General', 'general_section_text', 'telehook' );

    add_settings_field( 'telehook_setting_token', 'Bot token', 'telehook_setting_bot_token', 'telehook', 'general_settings' );
    add_settings_field( 'telehook_setting_chat_id', 'Chat ID', 'telehook_setting_chat_id', 'telehook', 'general_settings' );
    add_settings_field( 'telehook_setting_fields', 'Fields', 'telehook_setting_fields', 'telehook', 'general_settings' );
    add_settings_field( 'telehook_setting_message_template', 'Message template', 'telehook_setting_message_template', 'telehook', 'general_settings' );
}
add_action( 'admin_init', 'telehook_register_settings' );

function telehook_options_validate( $input ) {
    
    //TODO: Make all validations

    return $input;
}

function general_section_text() {
    $options = get_option( 'telehook_options' );
    if(!empty($options['bot_token']) && !empty($options['chat_id']) && !empty($options['message_template'])){
        echo '<p><strong>Webhook URL:</strong> ' . plugin_dir_url( __FILE__ ) . 'webhook.php</p>';
    } else {
        echo '<p>Please fill all setting fields to get a webhook URL</p>';
    }

}

function telehook_setting_bot_token() {
    $options = get_option( 'telehook_options' );
    echo "<input id='telehook_bot_token' name='telehook_options[bot_token]' type='text' value='" . esc_attr( $options['bot_token'] ) . "' />";
}

function telehook_setting_chat_id() {
    $options = get_option( 'telehook_options' );
    echo "<input id='telehook_setting_chat_id' name='telehook_options[chat_id]' type='text' value='" . esc_attr( $options['chat_id'] ) . "' />";
}

function telehook_setting_fields() {
    $options = get_option( 'telehook_options' );
    echo "<input id='telehook_setting_fields' name='telehook_options[fields]' type='text' placeholder='Comma separated fields that will be replaced in message template' value='" . esc_attr( $options['fields'] ) . "' />";
}
function telehook_setting_message_template() {
    $options = get_option( 'telehook_options' );
    echo "<textarea id='telehook_setting_message_template' rows='10' cols='24' name='telehook_options[message_template]' placeholder='Message to send. Fields must be wrapped with %%, example: %user%'>".esc_attr( $options['message_template'] )."</textarea>";
}