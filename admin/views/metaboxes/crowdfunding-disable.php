<?php 
global $post;

$is_crowdfunding_disabled = eddcf_crowdfunding_disabled( $post );

do_action( 'eddcf_metabox_eddcf_crowdfunding_toggle_before', $post );
?>
<input type="checkbox" name="crowdfunding_disabled" id="eddcf_crowdfunding_disabled" class="widefat" <?php checked( $is_crowdfunding_disabled ) ?> />
<p class="description"><?php _e( 'If this isn\'t a crowdfunding campaign, tick this box to treat it as a normal downloadable product.', 'eddcf' ); ?></p>
<?php
do_action( 'eddcf_metabox_eddcf_crowdfunding_toggle_after', $post );