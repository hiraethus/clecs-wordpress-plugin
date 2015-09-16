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
    static function post_to_clecs() {
        log_me('ClecsPoster::post_to_clecs()');
    }
}

add_action( 'publish_post', array('ClecsPoster', 'post_to_clecs') );
?>
