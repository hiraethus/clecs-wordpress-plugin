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
            'username'      => get_option('clecs_username'),
            'password'      => get_option('clecs_password'),
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

    static function post_to_clecs( $ID, $post ) {
        log_me('ClecsPoster::post_to_clecs()');
        $access_token = self::retrieve_access_token();
        $fields = array (
            'PostText' => 'Diweddariad blog: ' . $post->post_title . ' ' . get_permalink( $ID )
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

add_action( 'publish_post', array('ClecsPoster', 'post_to_clecs'), 10, 2 );
add_action( 'admin_menu', 'clecs_create_settings_menu' );

function clecs_create_settings_menu() {
    add_menu_page( 'Ategyn Clecs', 'Gosodiadau Clecs', 'administrator', __FILE__,
        'clecs_settings_page');

    add_action( 'admin_init', 'register_clecs_settings');
}

function register_clecs_settings() {
    register_setting( 'clecs-settings-group', 'clecs_username' );
    register_setting( 'clecs-settings-group', 'clecs_password' );
}

function clecs_settings_page() {
?>
<div class="wrap">
<h2>Clecs</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'clecs-settings-group' ); ?>
    <?php do_settings_sections( 'clecs-settings-group' ); ?>

    <table class="form-table">
        <tr valign="top">
            <th scope="row">Ebost</th>
            <td><input type="text" name="clecs_username" value="<?php echo esc_attr(get_option('clecs_username')); ?>"</td>
        </tr>

        <tr valign="top">
            <th scope="row">Password</th>
            <td><input type="password" name="clecs_password" value="<?php echo esc_attr(get_option('clecs_password')); ?>"</td>
        </tr>
    </table>

    <?php submit_button(); ?>
</form>

</div>
<?php
}

?>
