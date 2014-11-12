<?php
/**
 * Displays the campaign's pledge options.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

global $edd_options;
	
$campaign = eddcf_get_campaign();

$prices = edd_get_variable_prices( $campaign->ID );
$type   = edd_single_price_option_mode( $campaign->ID ) ? 'checkbox' : 'radio';

if ( $campaign->is_donations_only() || ! $campaign->has_reward_options() ) : ?>
	
	<input type="<?php echo $type ?>" name="edd_options[price_id][]" id="edd_price_option_<?php echo $campaign->ID ?>_0" class="edd_price_option_<?php echo $campaign->ID ?> hidden" value="0" checked />

<?php
elseif ( count( $prices )) : ?>

	<ul class="pledge-options">			

		<?php foreach ( $prices as $i => $price ) : ?>
			
			<?php 
				$has_limit = strlen( $price['limit'] ) > 0;
				$remaining = $price['limit'] - $price['bought'];
				$class = ! $has_limit ? 'limitless' : ( $remaining == 0 ? 'not-available' : 'available' );
			?>

			<li data-price="<?php echo edd_sanitize_amount( $price['amount'] )?>" class="pledge-option <?php echo $class ?>">
				
				<?php if ( ! $has_limit ) : ?>

					<input type="<?php echo $type ?>" name="edd_options[price_id][]" id="edd_price_option_<?php echo $campaign->ID ?>_<?php echo $i ?>" class="pledge-option-input edd_price_option_<?php echo $campaign->ID ?> edd_price_options_input" value="<?php echo $i ?>" />
					<h3 class="pledge-title"><?php 
						echo eddcf_get_pledge_amount_text( $price['amount'] ); 
					?></h3>
					<p class="pledge-limit"><?php 
						_e( 'Unlimited backers', 'eddcf' ); 
					?></p>
					<p class="pledge-description"><?php 
						echo $price['name']; 
					?></p>

				<?php else : ?>

					<?php if ( $remaining > 0 ) : ?>
						<input type="<?php echo $type ?>" name="edd_options[price_id][]" id="edd_price_option_<?php echo $campaign->ID ?>_<?php echo $i ?>" class="pledge-option-input edd_price_option_<?php echo $campaign->ID ?> edd_price_options_input" value="<?php echo $i ?>" />
					<?php endif ?>
					<h3 class="pledge-title"><?php 
						echo eddcf_get_pledge_amount_text( $price['amount'] );
					?></h3>
					<p class="pledge-limit"><?php 
						printf( __( '%d of %d remaining', 'eddcf' ), $remaining, $price['limit'] );
					?></p>
					<p class="pledge-description"><?php 
						echo $price['name']; 
					?></p>

				<?php endif ?>

			</li>

		<?php endforeach ?>

	</ul>

<?php endif;