<?php

if ( !empty( $options['show_icons'] ) )
{
	$networks = authorship_get_social_networks( 'active' );
    if ( $author['show_social_web'] )   $networks['web']   = array ( 'name' => 'Website', 'url' => 'https://www.example.com/', 'color' => '#333', 'premium' => false, );
	if ( $author['show_social_mail'] )  $networks['mail']  = array ( 'name' => 'E-mail',  'url' => 'your_name@example.com',    'color' => '#333', 'premium' => false );
	if ( $author['show_social_phone'] ) $networks['phone'] = array ( 'name' => 'Phone',   'url' => '123456789',                'color' => '#333', 'premium' => false, );
	$continue = false;
	foreach ( $networks as $id => $network )
	{
		if ( !empty( $author[$id] ) )
		{
			$continue = true;
			break; // There is at least one social network to show, no need to keep looking.
		}
	}
	if ( !$continue ) return;
	if ( isset( $options['icons_style'] ) )
	{
		$ico_style = $options['icons_style'];
		if ( $ico_style == 'default' ) $ico_style = '';
	}
	if ( isset( $options['icons_size'] ) ) $ico_size = $options['icons_size'];
	else $ico_size = 'normal';
	$ico_color = '';
	if ( isset( $options['icons_color'] ) and $options['icons_color'] != 'inherit' )
	{
		switch ( $options['icons_style'] )
		{
			case 'squared':
			case 'circled':

				$ico_color = 'border-color: ' . $options['icons_color'] . '; background-color: ' . $options['icons_color'] . ';';

			break;

			case 'boxed':

				$ico_color = 'border-color: ' . $options['icons_color'] . '; color: ' . $options['icons_color'] . ';';

			break;

			case 'branded':
			case 'branded-squared-reverse':
			case 'branded-circled-reverse':
			case 'branded-boxed':

				$ico_color = ''; // do nothing

			break;

			case 'branded-squared':
			case 'branded-circled':

				$ico_color = 'background-color: ' . $options['icons_color'];

			break;

			case 'default':
			default:

				$ico_color = 'color: ' . $options['icons_color'] . ';';

			break;
		}
	}
	$nofollow = $options['add_nofollow'] ? 'rel="nofollow"' : '' ;
    $target = !empty( $options['social_link_target'] ) ? $options['social_link_target'] : '_blank' ;
	echo '<div class="m-a-box-item m-a-box-social '.( ( isset( $options['profile_layout'] ) and !in_array( $options['profile_layout'], array( 'layout-7', 'layout-8' ) ) and isset( $options['profile_valign'] ) and !empty( $options['profile_valign'] ) and $options['profile_valign'] != 'center' ) ? 'molongui-align-self-'.$options['profile_valign'] : '' ).'">';
        foreach ( $networks as $id => $network )
        {
            $url = $author[$id];

            if ( !empty( $url ) )
            {
	            if ( $id == 'mail' )
	            {
                    $mail = sanitize_email( $url );
		            if ( !empty( $options['encode_email'] ) ) $url = molongui_ascii_encode( 'mailto:'.$mail );
		            else $url = 'mailto:'.$mail;
	            }
	            elseif ( $id == 'phone' )
	            {
		            $phone = $url;
		            if ( !empty( $options['encode_phone'] ) ) $url = molongui_ascii_encode( 'tel:'.$phone );
		            else $url = 'tel:'.$phone;
	            }
	            else
                {
                    $url = set_url_scheme( esc_url( $url ) );
                }
				?>
					<div class="m-a-box-social-icon m-a-list-social-icon">
						<a <?php echo ( $add_microdata ? 'itemprop="sameAs"' : '' ); ?> href="<?php echo $url; ?>" class="icon-container m-ico-<?php echo $ico_style; ?> m-ico-<?php echo $id; ?> molongui-font-size-<?php echo $ico_size; ?>-px" style="<?php echo $ico_color; ?>" <?php echo $nofollow; ?> target="<?php echo $target; ?>">
							<i class="m-a-icon-<?php echo $id; ?>"></i>
						</a>
					</div>
				<?php
            }
        }
    echo '</div>';
}