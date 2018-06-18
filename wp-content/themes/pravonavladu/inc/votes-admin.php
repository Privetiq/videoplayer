<?php

return;


/**
 * Created by PhpStorm.
 * User: Max
 * Date: 11.09.14
 * Time: 21:26
 */

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function contestant_votes_add_meta_box() {
    add_meta_box('contestant_votes', __( 'Голоси' ), 'contestant_votes_meta_box_callback', 'contestant');
}
add_action( 'add_meta_boxes', 'contestant_votes_add_meta_box' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function contestant_votes_meta_box_callback( $post ) {
    global $wpdb;

    $votes = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "votes WHERE post_id = '" . $wpdb->escape($post->ID) . "' ORDER BY date DESC");
    ?>
    <div style="overflow-y: scroll; max-height: 300px;">
    <table class="wp-list-table widefat" >
<?php
    foreach ($votes as $i => $vote) {
        ?>
        <tr>
            <td style="font-size: 12px;">
                <?php echo $i + 1; ?>.
                <?php if ($vote->network == 'Facebook') { ?>
                    <a href="https://facebook.com/<?php echo $vote->user_id; ?>" target="_blank">Facebook</a>
                <?php } elseif ($vote->network == 'Vkontakte') { ?>
                    <a href="https://vk.com/id<?php echo $vote->user_id; ?>" target="_blank">Вконтакте</a>
                <?php } ?>
            </td>
            <td style="font-size: 12px;"><?php echo $vote->date; ?></td>
        </tr>
        <?php
    }
    ?>
    </table>
    </div>
<?php

}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function myplugin_save_meta_box_data( $post_id ) {

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['myplugin_meta_box_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['myplugin_meta_box_nonce'], 'myplugin_meta_box' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if ( ! isset( $_POST['myplugin_new_field'] ) ) {
        return;
    }

    // Sanitize user input.
    $my_data = sanitize_text_field( $_POST['myplugin_new_field'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_my_meta_value_key', $my_data );
}
add_action( 'save_post', 'myplugin_save_meta_box_data' );