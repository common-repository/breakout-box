<?php
/*
Plugin Name: Breakout Box
Description: Plugin used to add "breakout" box to posts
Author: Don Kukral
Version: 1.2
Author URI: http://joeboydston.com
*/

add_action( 'add_meta_boxes', 'breakout_box_add_box' );
add_action( 'admin_init', 'breakout_box_add_box' ); // backwards compat < WP 3.0
add_action( 'save_post', 'breakout_box_save_postdata' );

function breakout_box_add_box() {
    add_meta_box(
        'breakbox_boxid', 
        __( 'Breakout Box', 'breakbox_box'),
        'breakbox_box_inner_box',
        'post'
    );
}

function breakbox_box_inner_box( $post ) {
    $breakout_box = get_post_meta( $post->ID, 'breakout_box', true );
    wp_nonce_field( plugin_basename( __FILE__ ), 'breakbox_box_nonce' );
    ?>
    <textarea rows="1" cols="40" name="breakout_box" tabindex="6" id="breakout_box" style='width: 98%;height: 4em;'><?php echo $breakout_box; ?></textarea>    
    <?php
}

function breakout_box_save_postdata ( $post_id ) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;
        
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    if ( !wp_verify_nonce( $_POST['breakbox_box_nonce'], plugin_basename( __FILE__ ) ) )
        return;    
        
    // Check permissions
    if ( 'page' == $_POST['post_type'] ) 
    {
      if ( !current_user_can( 'edit_page', $post_id ) )
          return;
    }
    else
    {
      if ( !current_user_can( 'edit_post', $post_id ) )
          return;
    }
    
    // make sure its a post 
    if ( 'post' != $_POST['post_type'] )
        return;
    
    update_post_meta( $post_id, 'breakout_box',  $_POST['breakout_box'] );
}

function breakout_box_shortcode ( $atts ) {
    return get_post_meta( $atts['id'], 'breakout_box', true );
}
add_shortcode( 'breakout_box', 'breakout_box_shortcode' );

?>