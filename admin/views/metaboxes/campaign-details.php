<?php 
global $post, $wp_locale;

$campaign = eddcf_get_campaign( $post );
$campaign_types = eddcf_campaign_types()->active_types();

$end_date = $campaign->end_date();
if ( ! $end_date && ! $campaign->is_endless() ) {
	$end_date = date( 'Y-m-d h:i:s', time() + ( 30 * 86400 ) );
}
$jj = mysql2date( 'd', $end_date );
$mm = mysql2date( 'm', $end_date );
$aa = mysql2date( 'Y', $end_date );
$hh = mysql2date( 'H', $end_date );
$mn = mysql2date( 'i', $end_date );
$ss = mysql2date( 's', $end_date );
?>
<!-- Campaign goal -->
<p>
	<label id="campaign-goal-prompt-text" for="campaign_goal"><strong><?php echo esc_html( __( 'Campaign Goal', 'eddcf' ), $post ) ?></strong></label><!-- #campaign-goal-prompt-text -->
</p>
<?php if ( ! isset( $edd_options[ 'currency_position' ] ) || $edd_options[ 'currency_position' ] == 'before' ) : ?>
	<?php echo edd_currency_filter( '' ); ?> <input type="text" name="campaign_goal" id="campaign_goal" value="<?php echo edd_format_amount( $campaign->goal(false) ); ?>" />
<?php else : ?>
	<input type="text" name="campaign_goal" id="campaign_goal" value="<?php echo edd_format_amount($campaign->goal(false) ); ?>" /><?php echo edd_currency_filter( '' ); ?>
<?php endif; ?>
<!-- Campaign funding type -->
<p>
	<strong><?php _e( 'Funding Type:', 'eddcf' ); ?></strong>
</p>
<p>
	<?php foreach ( $campaign_types as $key => $desc ) : ?>
	<label for="campaign_type[<?php echo esc_attr( $key ); ?>]"><input type="radio" name="campaign_type" id="campaign_type[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $campaign->type() ); ?> /> <strong><?php echo $campaign_types[ $key ][ 'title' ]; ?></strong> &mdash; <?php echo $campaign_types[ $key ][ 'description' ]; ?></label><br />
	<?php endforeach; ?>
</p>
<p>
	<label for="campaign_location"><strong><?php _e( 'Location:', 'eddcf' ); ?></strong></label><br />
	<input type="text" name="campaign_location" id="campaign_location" value="<?php echo esc_attr( $campaign->location() ); ?>" class="regular-text" />
</p>
<p>
	<label for="campaign_author"><strong><?php _e( 'Author:', 'eddcf' ); ?></strong></label><br />
	<input type="text" name="campaign_author" id="campaign_author" value="<?php echo esc_attr( $campaign->author() ); ?>" class="regular-text" />
</p>
<p>
	<label for="campaign_email"><strong><?php _e( 'Contact Email:', 'eddcf' ); ?></strong></label><br />
	<input type="text" name="campaign_contact_email" id="campaign_contact_email" value="<?php echo esc_attr( $campaign->contact_email() ); ?>" class="regular-text" />
</p>
<p>
	<strong><?php _e( 'End Date:', 'eddcf' ); ?></strong><br />
	<select id="campaign_end_mm" name="campaign_end_mm">
		<?php for ( $i = 1; $i < 13; $i = $i + 1 ) : $monthnum = zeroise($i, 2); ?>
			<option value="<?php echo $monthnum; ?>" <?php selected( $monthnum, $mm ); ?>>
			<?php printf( '%1$s-%2$s', $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ); ?>
			</option>
		<?php endfor; ?>
	</select>
	<input type="text" id="campaign_end_jj" name="campaign_end_jj" value="<?php echo esc_attr( $jj ); ?>" size="2" maxlength="2" autocomplete="off" />,
	<input type="text" id="campaign_end_aa" name="campaign_end_aa" value="<?php echo esc_attr( $aa ); ?>" size="4" maxlength="4" autocomplete="off" /> @
	<input type="text" id="campaign_end_hh" name="campaign_end_hh" value="<?php echo esc_attr( $hh ); ?>" size="2" maxlength="2" autocomplete="off" /> :
	<input type="text" id="campaign_end_mn" name="campaign_end_mn" value="<?php echo esc_attr( $mn ); ?>" size="2" maxlength="2" autocomplete="off" />
	<input type="hidden" id="campaign_end_ss" name="campaign_end_ss" value="<?php echo esc_attr( $ss ); ?>" />
	<input type="hidden" id="campaign_end_date" name="campaign_end_date" value="1" />
</p>
<p>
	<label for="campaign_endless">
		<input type="checkbox" name="campaign_endless" id="campaign_endless" value="1" <?php checked( 1, $campaign->is_endless() ); ?>> <?php printf( __( 'This %s never ends', 'eddcf' ), strtolower( edd_get_label_singular() ) ); ?>
	</label>
</p>