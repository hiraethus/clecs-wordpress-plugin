<?php
/*
* Plugin Name: clecs
* Plugin URI: http://github.com/hiraethus/clecs-wordpress-plugin
* Description: Plugin to write post notifications to the Clecs social network
* Version: alpha
* Author: Meical J Jones
* Author URI: http://github.com/hiraethus
* License: GPL
* */
/* for debugging purposes */
function log_me($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}


class ClecsPoster {
    static function retrieve_access_token() {
        log_me('Logging in');
        $login_fields = array (
            'username'      => '',
            'password'      => '',
            'grant_type'    => 'password'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.clecs.cymru/Token');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($login_fields));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        log_me( 'HTTP code: ' . $httpcode );
        log_me( 'result: ' . $response );
        log_me( 'curl_error: ' . curl_error($ch) );

        curl_close($ch);

        $result = json_decode($response);

        return $result->{'access_token'};
    }

    static function post_to_clecs() {
        log_me('ClecsPoster::post_to_clecs()');
        $access_token = self::retrieve_access_token();
        $fields = array (
            'PostText' => 'Lorem ipsum dolor sit amet'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.clecs.cymru/api/Posting/PostAPost');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array( 'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Bearer $access_token"));

        curl_exec($ch);
        curl_close($ch);
    }
}

add_action( 'publish_post', array('ClecsPoster', 'post_to_clecs'));
?>
