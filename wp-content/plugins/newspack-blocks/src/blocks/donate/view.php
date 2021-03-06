<?php
/**
 * Server-side rendering of the `newspack-blocks/donate` block.
 *
 * @package WordPress
 */

/**
 * Renders the footer of the donation form.
 *
 * @param array $attributes The block attributes.
 *
 * @return string
 */
function newspack_blocks_render_block_donate_footer( $attributes ) {
	$is_streamlined = $attributes['isStreamlined'];
	if ( $is_streamlined ) {
		$payment_data = WP_REST_Newspack_Donate_Controller::get_payment_data();
	}
	$button_text = $attributes['buttonText'];
	$campaign    = $attributes['campaign'] ?? false;

	ob_start();

	?>
		<p class='wp-block-newspack-blocks-donate__thanks thanks'>
			<?php echo esc_html__( 'Your contribution is appreciated.', 'newspack-blocks' ); ?>
		</p>

		<?php if ( $is_streamlined ) : ?>
			<div class="wp-block-newspack-blocks-donate__stripe stripe-payment stripe-payment--disabled" data-stripe-pub-key="<?php echo esc_attr( $payment_data['usedPublishableKey'] ); ?>">
				<div class="stripe-payment__card"></div>
				<input class="stripe-payment__email" placeholder="<?php echo esc_html__( 'Email', 'newspack-blocks' ); ?>" type="email" name="email" value="">
				<div class="stripe-payment__messages">
					<div class="type-error"></div>
					<div class="type-success"></div>
					<div class="type-info"></div>
				</div>
				<button type='submit' style="margin-left: 0; margin-top: 1em;">
					<?php echo wp_kses_post( $button_text ); ?>
				</button>
			</div>
		<?php else : ?>
			<button type='submit'>
				<?php echo wp_kses_post( $button_text ); ?>
			</button>
		<?php endif; ?>
		<?php if ( $campaign ) : ?>
			<input type='hidden' name='campaign' value='<?php echo esc_attr( $campaign ); ?>' />
		<?php endif; ?>
	<?php

	return ob_get_clean();
}

/**
 * Enqueue frontend scripts and styles for the streamlined version of the donate block.
 */
function newspack_blocks_enqueue_streamlined_donate_block_scripts() {
	if ( Newspack_Blocks::can_use_streamlined_donate_block() ) {
		$script_data = Newspack_Blocks::script_enqueue_helper( NEWSPACK_BLOCKS__BLOCKS_DIRECTORY . '/donateStreamlined.js' );
		wp_enqueue_script(
			Newspack_Blocks::DONATE_STREAMLINED_SCRIPT_HANDLE,
			$script_data['script_path'],
			[ 'wp-i18n' ],
			$script_data['version'],
			true
		);
		$style_path = NEWSPACK_BLOCKS__BLOCKS_DIRECTORY . 'donateStreamlined' . ( is_rtl() ? '.rtl' : '' ) . '.css';
		wp_enqueue_style(
			Newspack_Blocks::DONATE_STREAMLINED_SCRIPT_HANDLE,
			plugins_url( $style_path, NEWSPACK_BLOCKS__PLUGIN_FILE ),
			[],
			NEWSPACK_BLOCKS__VERSION
		);
	}
}

/**
 * Renders the `newspack-blocks/donate` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string
 */
function newspack_blocks_render_block_donate( $attributes ) {
	if ( ! class_exists( 'Newspack\Donations' ) ) {
		return '';
	}

	// Overwrite the attribute value.
	$attributes['isStreamlined'] = ( $attributes['isStreamlined'] ?? false ) && Newspack_Blocks::can_use_streamlined_donate_block();
	if ( $attributes['isStreamlined'] ) {
		newspack_blocks_enqueue_streamlined_donate_block_scripts();
	}

	Newspack_Blocks::enqueue_view_assets( 'donate' );

	$settings = Newspack\Donations::get_donation_settings();
	if ( is_wp_error( $settings ) || ! $settings['created'] ) {
		return '';
	}

	/* If block is in "manual" mode, override certain state properties with values stored in attributes */
	if ( $attributes['manual'] ?? false ) {
		$settings = array_merge( $settings, $attributes );
	}

	$frequencies = [
		'once'  => __( 'One-time', 'newspack-blocks' ),
		'month' => __( 'Monthly', 'newspack-blocks' ),
		'year'  => __( 'Annually', 'newspack-blocks' ),
	];

	$selected_frequency = $attributes['defaultFrequency'] ?? 'month';
	$suggested_amounts  = $settings['suggestedAmounts'];

	$uid = wp_rand( 10000, 99999 ); // Unique identifier to prevent labels colliding with other instances of Donate block.

	$form_footer = newspack_blocks_render_block_donate_footer( $attributes );

	ob_start();

	/**
	 * For AMP-compatibility, the donation forms are implemented as pure HTML forms (no JS).
	 * Each frequency and tier option is a radio input, styled to look like a button.
	 * As the radio inputs are checked/unchecked, fields are hidden/displayed using only CSS.
	 */
	if ( ! $settings['tiered'] ) :

		?>
		<div class='wp-block-newspack-blocks-donate wpbnbd untiered'>
			<form>
				<input type='hidden' name='newspack_donate' value='1' />
				<div class='wp-block-newspack-blocks-donate__options'>
					<?php foreach ( $frequencies as $frequency_slug => $frequency_name ) : ?>
						<?php
							$amount           = 'year' === $frequency_slug || 'once' === $frequency_slug ? 12 * $settings['suggestedAmountUntiered'] : $settings['suggestedAmountUntiered'];
							$formatted_amount = number_format( $amount, floatval( $amount ) - intval( $amount ) ? 2 : 0 );
						?>

						<div class='wp-block-newspack-blocks-donate__frequency frequency'>
							<input
								type='radio'
								value='<?php echo esc_attr( $frequency_slug ); ?>'
								id='newspack-donate-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>'
								name='donation_frequency'
								<?php checked( $selected_frequency, $frequency_slug ); ?>
							/>
							<label
								for='newspack-donate-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>'
								class='donation-frequency-label freq-label'
							>
								<?php echo esc_html( $frequency_name ); ?>
							</label>
							<div class='input-container'>
								<label
									class='donate-label'
									for='newspack-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>-untiered-input'
								>
									<?php echo esc_html__( 'Donation amount', 'newspack-blocks' ); ?>
								</label>
								<div class='wp-block-newspack-blocks-donate__money-input money-input'>
									<span class='currency'>
										<?php echo esc_html( $settings['currencySymbol'] ); ?>
									</span>
									<input
										type='number'
										name='donation_value_<?php echo esc_attr( $frequency_slug ); ?>_untiered'
										value='<?php echo esc_attr( $formatted_amount ); ?>'
										id='newspack-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>-untiered-input'
									/>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php echo $form_footer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</form>
		</div>
		<?php
	else :

		?>
		<div class='wp-block-newspack-blocks-donate wpbnbd tiered'>
			<form>
				<input type='hidden' name='newspack_donate' value='1' />
				<div class='wp-block-newspack-blocks-donate__options'>
					<div class='wp-block-newspack-blocks-donate__frequencies frequencies'>
						<?php foreach ( $frequencies as $frequency_slug => $frequency_name ) : ?>

							<div class='wp-block-newspack-blocks-donate__frequency frequency'>
								<input
									type='radio'
									value='<?php echo esc_attr( $frequency_slug ); ?>'
									id='newspack-donate-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>'
									name='donation_frequency'
									<?php checked( $selected_frequency, $frequency_slug ); ?>
								/>
								<label
									for='newspack-donate-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>'
									class='donation-frequency-label freq-label'
								>
									<?php echo esc_html( $frequency_name ); ?>
								</label>

								<div class='wp-block-newspack-blocks-donate__tiers tiers'>
									<?php foreach ( $suggested_amounts as $index => $suggested_amount ) : ?>
										<div class='wp-block-newspack-blocks-donate__tier'>
											<?php
												$amount           = 'year' === $frequency_slug || 'once' === $frequency_slug ? 12 * $suggested_amount : $suggested_amount;
												$formatted_amount = $settings['currencySymbol'] . number_format( $amount, floatval( $amount ) - intval( $amount ) ? 2 : 0 );
											?>
											<input
												type='radio'
												name='donation_value_<?php echo esc_attr( $frequency_slug ); ?>'
												value='<?php echo esc_attr( $amount ); ?>'
												id='newspack-tier-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>-<?php echo (int) $index; ?>'
												<?php checked( 1, $index ); ?>
											/>
											<label
												class='tier-select-label tier-label'
												for='newspack-tier-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>-<?php echo (int) $index; ?>'
											>
												<?php echo esc_html( $formatted_amount ); ?>
											</label>
										</div>
									<?php endforeach; ?>

									<div class='wp-block-newspack-blocks-donate__tier'>
										<?php $amount = 'year' === $frequency_slug || 'once' === $frequency_slug ? 12 * $suggested_amounts[1] : $suggested_amounts[1]; ?>
										<input
											type='radio'
											class='other-input'
											name='donation_value_<?php echo esc_attr( $frequency_slug ); ?>'
											value='other'
											id='newspack-tier-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>-other'
										/>
										<label
											class='tier-select-label tier-label'
											for='newspack-tier-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>-other'
										>
											<?php echo esc_html__( 'Other', 'newspack-blocks' ); ?>
										</label>
										<label
											class='other-donate-label odl'
											for='newspack-tier-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>-other-input'
										>
											<?php echo esc_html__( 'Donation amount', 'newspack-blocks' ); ?>
										</label>
										<div class='wp-block-newspack-blocks-donate__money-input money-input'>
											<span class='currency'>
												<?php echo esc_html( $settings['currencySymbol'] ); ?>
											</span>
											<input
												type='number'
												name='donation_value_<?php echo esc_attr( $frequency_slug ); ?>_other'
												value='<?php echo esc_attr( $amount ); ?>'
												id='newspack-tier-<?php echo esc_attr( $frequency_slug . '-' . $uid ); ?>-other-input'
											/>
										</div>
									</div>

								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php echo $form_footer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</form>
		</div>
		<?php
	endif;

	return apply_filters( 'newspack_blocks_donate_block_html', ob_get_clean(), $attributes );
}

/**
 * Registers the `newspack-blocks/donate` block on server.
 */
function newspack_blocks_register_donate() {
	register_block_type(
		'newspack-blocks/donate',
		array(
			'attributes'      => array(
				'className'               => [
					'type' => 'string',
				],
				'manual'                  => [
					'type' => 'boolean',
				],
				'suggestedAmounts'        => [
					'type'    => 'array',
					'items'   => [
						'type' => 'number',
					],
					'default' => [ 0, 0, 0 ],
				],
				'suggestedAmountUntiered' => [
					'type' => 'number',
				],
				'tiered'                  => [
					'type'    => 'boolean',
					'default' => true,
				],
				'campaign'                => [
					'type' => 'string',
				],
				'buttonText'              => [
					'type'    => 'string',
					'default' => __( 'Donate now!', 'newspack-blocks' ),
				],
				'defaultFrequency'        => [
					'type'    => 'string',
					'default' => 'month',
				],
			),
			'render_callback' => 'newspack_blocks_render_block_donate',
			'supports'        => [],
		)
	);
}
add_action( 'init', 'newspack_blocks_register_donate' );
