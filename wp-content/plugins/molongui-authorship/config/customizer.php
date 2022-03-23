<?php

$is_pro = authorship_has_pro();

return array
(
	'add_panel'       => true,
	'id'              => MOLONGUI_AUTHORSHIP_NAME,
	'title'           => __( "Molongui Author Box", 'molongui-authorship' ),
	'description'     => sprintf( '%s%s%s', '<p>', __( "Customize visual settings to your like.", 'molongui-authorship' ), '</p>' ),
	'priority'        => 121,
	'capability'      => 'manage_options',
	'active_callback' => '',
	'sections'        => array
	(
		array
		(
			'id'                 => 'molongui_authorship_scheme',
			'title'              => __( "Pre-defined appearance schemes", 'molongui-authorship' ),
			'description'        => '',
			'display'            => false,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[scheme]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'scheme-1',
						'transport'            => 'refresh',
						'validate_callback'    => '',
						'sanitize_callback'    => '',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( 'Pre-defined schemes', 'molongui-authorship' ),
						'description'     => __( 'Choose the pre-defined appearance scheme you would like to use. Once loaded, you can customize any setting you need.', 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Select_Control',
						'type'            => 'molongui-select',
						'choices'         => array
						(
							'scheme-1' => array
							(
								'label'    => __( 'Scheme 1', 'molongui-authorship' ),
								'disabled' => false,
								'premium'  => false,
							),
							'scheme-2' => array
							(
								'label'    => __( 'Scheme 2', 'molongui-authorship' ),
								'disabled' => false,
								'premium'  => false,
							),
							'scheme-3' => array
							(
								'label'    => __( 'Scheme 3', 'molongui-authorship' ),
								'disabled' => false,
								'premium'  => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_box',
			'title'              => __( "Box", 'molongui-authorship' ),
			'description'        => __( "Customize to your likings the author box that will be displayed on your posts by selecting which layout to use, the position where to display it, and many more color, size and styling options. Make it fit the best with your site. You can preview premium settings. Upgrade to Pro to unlock them all :)", 'molongui-authorship' ),
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[layout]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'slim',
						'transport'            => 'refresh',
						'validate_callback'    => '',
						'sanitize_callback'    => '',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Layout", 'molongui-authorship' ),
						'description'     => __( "The template used to render the author box. The first two displays the author profile or the related posts in the same space while the third one shows related entries below author profile.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'slim' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-layout/box-layout-slim.png',
								'label'   => __( "Slim", 'molongui-authorship' ),
								'premium' => false,
							),
							'tabbed' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-layout/box-layout-tabbed.png',
								'label'   => __( "Tabbed", 'molongui-authorship' ),
								'premium' => false,
							),
							'stacked' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-layout/box-layout-stacked.png',
								'label'   => __( "Stacked", 'molongui-authorship' ),
								'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[position]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'below',
						'transport'            => 'refresh',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Position", 'molongui-authorship' ),
						'description'     => __( "Whether to show the author box above the post content, below or on both places.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'above' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-position/author-box-position-above.png',
								'label'   => __( "Above content", 'molongui-authorship' ),
								'premium' => false,
							),
							'below' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-position/author-box-position-below.png',
								'label'   => __( "Below content", 'molongui-authorship' ),
								'premium' => false,
							),
							'both' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-position/author-box-position-both.png',
								'label'   => __( "Above and below content", 'molongui-authorship' ),
								'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[box_margin]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 20,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Margin", 'molongui-authorship' ),
						'description'     => __( "Space in pixels to add above and below the author box.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
							'min'     => 0,
							'max'     => 200,
							'step'    => 1,
							'suffix'  => 'px',
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[box_width]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 100,
						'transport'            => 'postMessage',
						'validate_callback'    => 'molongui_authorship_validate_box_width',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Width", 'molongui-authorship' ),
						'description'     => __( "Amount of space in percentage the author box can take.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
							'min'     => 0,
							'max'     => 100,
							'step'    => 1,
							'suffix'  => '%',
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[box_border]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'all',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Border", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'none' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-border/box-border-none.png',
								'label'   => __( "None", 'molongui-authorship' ),
								'premium' => false,
							),
							'all' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-border/box-border-all.png',
								'label'   => __( "All", 'molongui-authorship' ),
								'premium' => false,
							),
							'horizontals' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-border/box-border-horizontal.png',
								'label'   => __( "Horizontals", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'verticals' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-border/box-border-vertical.png',
								'label'   => __( "Verticals", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'left' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-border/box-border-left.png',
								'label'   => __( "Left" ),
								'premium' => !$is_pro,
							),
							'top' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-border/box-border-top.png',
								'label'   => __( "Top", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'right' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-border/box-border-right.png',
								'label'   => __( "Right" ),
								'premium' => !$is_pro,
							),
							'bottom' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-border/box-border-bottom.png',
								'label'   => __( "Bottom", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[box_border_style]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'solid',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',//array( 'Molongui\Authorship\Fw\Customizer\Customizer', 'molongui_sanitize_to_default' ),//'molongui_sanitize_to_default',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Border style", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'solid' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-solid.png',
								'label'   => __( "Solid", 'molongui-authorship' ),
								'premium' => false,
							),
							'double' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-double.png',
								'label'   => __( "Double", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'dotted' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-dotted.png',
								'label'   => __( "Dotted", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'dashed' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-dashed.png',
								'label'   => __( "Dashed", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
                        'active_callback' => function( $control ) { return !molongui_get_customizer_setting( $control, 'box_border', 'none' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[box_border_width]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 3,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Border width", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
							'min'     => 1,
							'max'     => 10,
							'step'    => 1,
							'suffix'  => 'px',
						),
						'allow_addition'  => false,
                        'active_callback' => function( $control ) { return !molongui_get_customizer_setting( $control, 'box_border', 'none' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[box_border_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '#adadad',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Border color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
                        'active_callback' => function( $control ) { return !molongui_get_customizer_setting( $control, 'box_border', 'none' ); },
                    ),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[box_background]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '#efefef',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Background color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[box_shadow]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'right',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Shadow", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'left' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-shadow/box-shadow-left.png',
								'label'   => __( "Left" ),
								'premium' => false,
							),
							'none' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-shadow/box-shadow-none.png',
								'label'   => __( "None", 'molongui-authorship' ),
								'premium' => false,
							),
							'right' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-shadow/box-shadow-right.png',
								'label'   => __( "Right" ),
								'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_headline',
			'title'              => __( "Headline", 'molongui-authorship' ),
			'description'        => '',
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[show_headline]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 0,
						'transport'            => 'refresh',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Display", 'molongui-authorship' ),
						'description'     => __( "Whether to show a headline above the author box.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'1' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-headline/author-box-headline-on.png',
								'label'   => __( "Show headline", 'molongui-authorship' ),
								'premium' => false,
							),
							'0' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-headline/author-box-headline-off.png',
								'label'   => __( "Hide headline", 'molongui-authorship' ),
								'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[headline]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_string',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Text", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => 'WP_Customize_Control',
						'type'            => 'text',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium'     => false,
							'placeholder' => __( "About the author", 'molongui-authorship' ),
						),
						'allow_addition'  => false,
						'active_callback' => 'molongui_active_headline_setting',
					),
				),
				array
				(
					'id'      => 'molongui_headline_typography_settings',
					'display' => true,
					'setting' => array
					(
						'sanitize_callback' => 'esc_html',
					),
					'control' => array
					(
						'label'           => __( "Typography", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Group_Label_Control',
						'type'            => 'molongui-compact-group-label',
						'active_callback' => 'molongui_active_headline_setting',
						'input_attrs'     => array(),
						'choices'         => array(),
					),
				),
                array
                (
                    'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[headline_text_size]',
                    'display' => true,
                    'setting' => array
                    (
                        'type'                 => 'option',
                        'capability'           => 'manage_options',
                        'default'              => 18,
                        'transport'            => 'postMessage',
                        'validate_callback'    => '',
                        'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
                        'sanitize_js_callback' => '',
                        'dirty'                => false,
                    ),
                    'control' => array
                    (
                        'label'           => __( "Size", 'molongui-authorship' ),
                        'description'     => '',
                        'priority'        => 10,
                        'class'           => '\Molongui_Customize_Range_Control',
                        'type'            => 'molongui-compact-range-flat',
                        'choices'         => array(),
                        'input_attrs'     => array
                        (
                            'premium' => false,
                            'off'     => true, // 'min' = 7 => OFF; Font size is taken from theme's rules as 'molongui-font-size-7-px' doesn't exist.
                            'min'     => 7,
                            'max'     => 100,
                            'step'    => 1,
                            'suffix'  => 'px',
                        ),
                        'allow_addition'  => false,
                        'active_callback' => 'molongui_active_headline_setting',
                    ),
                ),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[headline_text_style]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Style", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Checkbox_Button_Control',
						'type'            => 'molongui-compact-image-checkbox',
						'choices'         => array
						(
							'normal' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-normal.png',
								'label'   => __( "Normal", 'molongui-authorship' ),
								'premium' => false,
							),
							'bold' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-bold.png',
								'label'   => __( "Bold", 'molongui-authorship' ),
								'premium' => false,
							),
							'italic' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-italic.png',
								'label'   => __( "Italic", 'molongui-authorship' ),
								'premium' => false,
							),
							'underline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-underline.png',
								'label'   => __( "Underline", 'molongui-authorship' ),
								'premium' => false,
							),
							'overline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-overline.png',
								'label'   => __( "Overline", 'molongui-authorship' ),
								'premium' => false,
							),
							'overunderline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-overunderline.png',
								'label'   => __( "Overline and underline", 'molongui-authorship' ),
								'premium' => false,
							),
						),
						'input_attrs'     => array( 'compact' => true ),
						'allow_addition'  => true,
						'active_callback' => 'molongui_active_headline_setting',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[headline_text_case]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'none',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Case", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-compact-image-radio',
						'choices'         => array
						(
							'none' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-none.png',
								'label'   => __( "Leave as is", 'molongui-authorship' ),
								'premium' => false,
							),
							'capitalize' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-capitalize.png',
								'label'   => __( "Capitalize", 'molongui-authorship' ),
								'premium' => false,
							),
							'uppercase' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-uppercase.png',
								'label'   => __( "Uppercase", 'molongui-authorship' ),
								'premium' => false,
							),
							'lowercase' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-lowercase.png',
								'label'   => __( "Lowercase", 'molongui-authorship' ),
								'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => 'molongui_active_headline_setting',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[headline_text_align]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'left',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Align", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-compact-image-radio',
						'choices'         => array
						(
							'left' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-left.png',
								'label'   => __( "Left" ),
								'premium' => false,
							),
							'center' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-center.png',
								'label'   => __( "Center" ),
								'premium' => false,
							),
							'right' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-right.png',
								'label'   => __( "Right" ),
								'premium' => false,
							),
							'justify' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-justify.png',
								'label'   => __( "Justify" ),
								'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => 'molongui_active_headline_setting',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[headline_text_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'inherit',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-compact-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
						'active_callback' => 'molongui_active_headline_setting',
					),
				),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_tabs',
			'title'              => __( "Tabs", 'molongui-authorship' ),
			'description'        => '',
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
            'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'box_layout', 'tabbed' ); },
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[tabs_position]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'top-full',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Position", 'molongui-authorship' ),
						'description'     => __( "Where to display the tabs.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'top-full' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-tabs/box-tabs-position-top-full.png',
								'label'   => __( "Top full width", 'molongui-authorship' ),
								'premium' => false,
							),
							'top-left' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-tabs/box-tabs-position-top-left.png',
								'label'   => __( "Top left", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'top-center' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-tabs/box-tabs-position-top-center.png',
								'label'   => __( "Top center", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'top-right' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/box-tabs/box-tabs-position-top-right.png',
								'label'   => __( "Top right", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[tabs_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '#f4f4f4',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Tabs color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => !$is_pro,
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[tabs_active_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '#efefef',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Active tab color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => !$is_pro,
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[tabs_background]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'transparent',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Background color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => !$is_pro,
						),
						'allow_addition'  => false,
						'active_callback' => 'molongui_active_tabs_background_setting',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[tabs_text_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'inherit',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Text color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => !$is_pro,
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[about_the_author]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => __( "About the author", 'molongui-authorship' ),
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_string',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "'About the author' label", 'molongui-authorship' ),
						'description'     => __( "Text to show as author bio tab label.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => 'WP_Customize_Control',
						'type'            => 'text',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium'     => false,
							'placeholder' => __( "About the author", 'molongui-authorship' ),
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[related_posts]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => __( "Related Posts", 'molongui-authorship' ),
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_string',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "'Related posts' label", 'molongui-authorship' ),
						'description'     => __( "Text to show as author related posts tab label", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => 'WP_Customize_Control',
						'type'            => 'text',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium'     => false,
							'placeholder' => __( "Related Posts", 'molongui-authorship' ),
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_profile',
			'title'              => __( "Template", 'molongui-authorship' ),
			'description'        => '',
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[profile_layout]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'layout-1',
						'transport'            => 'refresh',
						'validate_callback'    => '',
						'sanitize_callback'    => '', //'molongui_authorship_sanitize_setting', // Enabling this function would prevent live preview
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Profile template", 'molongui-authorship' ),
						'description'     => __( "The template to be used to render the author profile section.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'layout-1' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/profile-template/profile-template-1.png',
								'label'   => __( "Template 1", 'molongui-authorship' ),
								'premium' => false,
							),
							'layout-2' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/profile-template/profile-template-2.png',
								'label'   => __( "Template 2", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'layout-3' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/profile-template/profile-template-3.png',
								'label'   => __( "Template 3", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'layout-4' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/profile-template/profile-template-4.png',
								'label'   => __( "Template 4", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'layout-5' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/profile-template/profile-template-5.png',
								'label'   => __( "Template 5", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'layout-6' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/profile-template/profile-template-6.png',
								'label'   => __( "Template 6", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'layout-7' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/profile-template/profile-template-7.png',
								'label'   => __( "Template 7", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'layout-8' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/profile-template/profile-template-8.png',
								'label'   => __( "Template 8", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[profile_valign]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'center',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Alignment", 'molongui-authorship' ),
						'description'     => __( "Content vertical alignment", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Text_Radio_Button_Control',
						'type'            => 'molongui-text-radio',
						'choices'         => array
						(
							'top' => array
							(
                                'display' => true,
								'label'   => __( "Top", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'center' => array
							(
                                'display' => true,
								'label'   => __( "Center" ),
								'premium' => false,
							),
							'bottom' => array
							(
                                'display' => true,
								'label'   => __( "Bottom", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[profile_title]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => __( "Author Profile", 'molongui-authorship' ),
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => '',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Section label", 'molongui-authorship' ),
						'description'     => __( "Leave empty to remove the section label", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => 'WP_Customize_Control',
						'type'            => 'text',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium'     => false,
							'placeholder' => __( "Author Profile", 'molongui-authorship' ),
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'box_layout', 'stacked' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bottom_background_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '#e0e0e0',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Bottom background color", 'molongui-authorship' ),
						'description'     => __( "The color used to fill the background of the bottom section on a 'ribbon' layout.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => !$is_pro,
						),
						'allow_addition'  => false,
						'active_callback' => 'molongui_active_ribbon_layout_setting',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bottom_border_style]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'solid',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Bottom border style", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'none' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-none.png',
								'label'   => __( "None", 'molongui-authorship' ),
								'premium' => false,
							),
							'solid' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-solid.png',
								'label'   => __( "Solid", 'molongui-authorship' ),
								'premium' => false,
							),
							'double' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-double.png',
								'label'   => __( "Double", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'dotted' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-dotted.png',
								'label'   => __( "Dotted", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'dashed' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-dashed.png',
								'label'   => __( "Dashed", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => 'molongui_active_ribbon_layout_setting',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bottom_border_width]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 1,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Bottom border width", 'molongui-authorship' ),
						'description'     => __( "Width of the border to add at the top of bottom section on a 'ribbon' layout.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => !$is_pro,
							'min'     => 1,
							'max'     => 10,
							'step'    => 1,
							'suffix'  => 'px',
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return ( molongui_active_ribbon_layout_setting( $control ) and molongui_active_ribbon_border_setting( $control ) ? true : false ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bottom_border_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '#b6b6b6',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Bottom border color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => !$is_pro,
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return ( molongui_active_ribbon_layout_setting( $control ) and molongui_active_ribbon_border_setting( $control ) ? true : false ); },
					),
				),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_avatar',
			'title'              => __( "Avatar", 'molongui-authorship' ),
			'description'        => '',
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[show_avatar]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 1,
						'transport'            => 'refresh',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( 'Display', 'molongui-authorship' ),
						'description'     => __( 'Whether to show the author avatar.', 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Text_Radio_Button_Control',
						'type'            => 'molongui-text-radio',
						'choices'         => array
						(
							'1' => array
							(
                                'display' => true,
								'label'   => __( "Show" ),
								'premium' => false,
							),
							'0' => array
							(
                                'display' => true,
								'label'   => __( "Hide" ),
								'premium' => false,
							),
						),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_src]',
					'display' => true,
					'setting' => array
					(
                        'type'                 => 'option',
                        'capability'           => 'manage_options',
                        'default'              => authorship_is_feature_enabled( 'avatar' ) ? 'local' : 'gravatar',
                        'transport'            => 'refresh',
                        'validate_callback'    => '',
                        'sanitize_callback'    => '',
                        'sanitize_js_callback' => '',
                        'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Image Source", 'molongui-authorship' ),
						'description'     => __( "Where to retrieve the image from.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Text_Radio_Button_Control',
						'type'            => 'molongui-text-radio',
						'choices'         => array
						(
							'local' => array
							(
							    'display' => true,//authorship_is_feature_enabled( 'avatar' ),
								'label'   => __( "Local", 'molongui-authorship' ),
								'premium' => false,
							),
							'gravatar' => array
							(
                                'display' => true,
								'label'   => __( "Gravatar", 'molongui-authorship' ),
								'premium' => false,
							),
							'acronym' => array
							(
                                'display' => true,
								'label'   => __( "Acronym", 'molongui-authorship' ),
								'premium' => false,
							),
						),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
                        'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_avatar', '1' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_local_fallback]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'gravatar',
						'transport'            => 'refresh',
						'validate_callback'    => '',
						'sanitize_callback'    => '',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Fallback", 'molongui-authorship' ),
						'description'     => __( "What to display if no local profile image available?", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Text_Radio_Button_Control',
						'type'            => 'molongui-text-radio',
						'choices'         => array
						(
							'gravatar' => array
							(
                                'display' => true,
								'label'   => __( "Gravatar", 'molongui-authorship' ),
								'premium' => false,
							),
							'acronym' => array
							(
                                'display' => true,
								'label'   => __( "Acronym", 'molongui-authorship' ),
								'premium' => false,
							),
							'none' => array
							(
                                'display' => true,
								'label'   => __( "Nothing", 'molongui-authorship' ),
								'premium' => false,
							),
						),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
                        'active_callback' => function( $control ) { return ( ( molongui_get_customizer_setting( $control, 'show_avatar', '1' ) and molongui_get_customizer_setting( $control, 'avatar_src', 'local' ) ) ? true : false ); },
					),
				),
                array
                (
                    'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_default_gravatar]',
                    'display' => true,
                    'setting' => array
                    (
                        'type'                 => 'option',
                        'capability'           => 'manage_options',
                        'default'              => 'mp',
                        'transport'            => 'refresh',
                        'validate_callback'    => '',
                        'sanitize_callback'    => '',
                        'sanitize_js_callback' => '',
                        'dirty'                => false,
                    ),
                    'control' => array
                    (
                        'label'           => __( "Default Gravatar", 'molongui-authorship' ),
                        'description'     => __( 'What default image to display if no Gravatar found.', 'molongui-authorship' ),
                        'priority'        => 10,
                        'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
                        'type'            => 'molongui-image-radio',
                        'choices'         => array
                        (
                            'mp' => array
                            (
                                'image'   => 'https://www.gravatar.com/avatar/205e460b479eee5b48aec07710c08d50?forcedefault=yes&size=50&default=mp',
                                'label'   => __( "A simple, cartoon-style silhouetted outline of a person", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'identicon' => array
                            (
                                'image'   => 'https://www.gravatar.com/avatar/205e460b479eee5b48aec07710c08d50?forcedefault=yes&size=50&default=identicon',
                                'label'   => __( "A geometric pattern based on an email hash", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'monsterid' => array
                            (
                                'image'   => 'https://www.gravatar.com/avatar/205e460b479eee5b48aec07710c08d50?forcedefault=yes&size=50&default=monsterid',
                                'label'   => __( "A generated 'monster' with different colors, faces, etc.", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'wavatar' => array
                            (
                                'image'   => 'https://www.gravatar.com/avatar/205e460b479eee5b48aec07710c08d50?forcedefault=yes&size=50&default=wavatar',
                                'label'   => __( "A generated face with differing features and backgrounds", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'retro' => array
                            (
                                'image'   => 'https://www.gravatar.com/avatar/205e460b479eee5b48aec07710c08d50?forcedefault=yes&size=50&default=retro',
                                'label'   => __( "An awesome generated, 8-bit arcade-style pixelated face", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'robohash' => array
                            (
                                'image'   => 'https://www.gravatar.com/avatar/205e460b479eee5b48aec07710c08d50?forcedefault=yes&size=50&default=robohash',
                                'label'   => __( "A generated robot with different colors, faces, etc.", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'blank' => array
                            (
                                'image'   => 'https://www.gravatar.com/avatar/205e460b479eee5b48aec07710c08d50?forcedefault=yes&size=50&default=blank',
                                'label'   => __( "Blank. A transparent PNG image", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'random' => array
                            (
                                'image'   => 'http://placehold.jp/40/e6e6e6/29abe2/50x50.png?text=%3F&css=%7B%22font-weight%22%3A%22%20bold%22%7D',
                                'label'   => __( "A random option will be picked for each author", 'molongui-authorship' ),
                                'premium' => !$is_pro,
                            ),
                        ),
                        'input_attrs'     => array
                        (
                            'premium' => false,
                        ),
                        'allow_addition'  => false,
                        'active_callback' => function( $control ) { return ( ( molongui_get_customizer_setting( $control, 'show_avatar', '1' ) and ( molongui_get_customizer_setting( $control, 'avatar_src', 'gravatar' ) or ( molongui_get_customizer_setting( $control, 'avatar_src', 'local' ) and molongui_get_customizer_setting( $control, 'avatar_local_fallback', 'gravatar' ) ) ) ) ? true : false ); },
                    ),
                ),
                array
                (
                    'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_width]',
                    'display' => true,
                    'setting' => array
                    (
                        'type'                 => 'option',
                        'capability'           => 'manage_options',
                        'default'              => 150,
                        'transport'            => 'refresh',
                        'validate_callback'    => '',
                        'sanitize_callback'    => '',
                        'sanitize_js_callback' => '',
                        'dirty'                => false,
                    ),
                    'control' => array
                    (
                        'label'           => __( "Width", 'molongui-authorship' ),
                        'description'     => __( "Avatar image width in pixels. If bigger than actual image's width, image's width is taken. Square images take the lower value from given size values (width and height). You might need/consider to regenerate thumbnails.", 'molongui-authorship' ),
                        'priority'        => 10,
                        'class'           => '\Molongui_Customize_Number_Control',
                        'type'            => 'molongui-number',
                        'choices'         => array(),
                        'input_attrs'     => array
                        (
                            'min'     => '1',
                            'max'     => '',
                            'step'    => '1',
                            'premium' => false,
                        ),
                        'allow_addition'  => false,
                        'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_avatar', '1' ); },
                    ),
                ),
                array
                (
                    'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_height]',
                    'display' => true,
                    'setting' => array
                    (
                        'type'                 => 'option',
                        'capability'           => 'manage_options',
                        'default'              => 150,
                        'transport'            => 'refresh',
                        'validate_callback'    => '',
                        'sanitize_callback'    => '',
                        'sanitize_js_callback' => '',
                        'dirty'                => false,
                    ),
                    'control' => array
                    (
                        'label'           => __( "Height", 'molongui-authorship' ),
                        'description'     => __( "Avatar image height in pixels. If bigger than actual image's height, image's height is taken. Square images take the lower value from given size values (width and height). You might need/consider to regenerate thumbnails.", 'molongui-authorship' ),
                        'priority'        => 10,
                        'class'           => '\Molongui_Customize_Number_Control',
                        'type'            => 'molongui-number',
                        'choices'         => array(),
                        'input_attrs'     => array
                        (
                            'min'     => '1',
                            'max'     => '',
                            'step'    => '1',
                            'premium' => false,
                        ),
                        'allow_addition'  => false,
                        'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_avatar', '1' ); },
                    ),
                ),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_style]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'none',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Shape", 'molongui-authorship' ),
						'description'     => __( "Whether and how to shape the author avatar.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'none' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/avatar-style/avatar-style-none.png',
								'label'   => __( "None", 'molongui-authorship' ),
								'premium' => false,
							),
							'rounded' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/avatar-style/avatar-style-rounded.png',
								'label'   => __( "Rounded", 'molongui-authorship' ),
								'premium' => false,
							),
							'circled' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/avatar-style/avatar-style-circled.png',
								'label'   => __( "Circled", 'molongui-authorship' ),
								'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
                        'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_avatar', '1' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_border_style]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'solid',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Border style", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'none' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-none.png',
								'label'   => __( "None", 'molongui-authorship' ),
								'premium' => false,
							),
							'solid' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-solid.png',
								'label'   => __( "Solid", 'molongui-authorship' ),
								'premium' => false,
							),
							'double' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-double.png',
								'label'   => __( "Double", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'dotted' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-dotted.png',
								'label'   => __( "Dotted", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'dashed' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/border-style/border-style-dashed.png',
								'label'   => __( "Dashed", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
                        'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_avatar', '1' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_border_width]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 2,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Border width", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
							'min'     => 1,
							'max'     => 10,
							'step'    => 1,
							'suffix'  => 'px',
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return ( ( molongui_get_customizer_setting( $control, 'show_avatar', '1' ) and !molongui_get_customizer_setting( $control, 'avatar_border_style', 'none' ) ) ? true : false ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_border_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '#bfbfbf',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Border color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
                        'active_callback' => function( $control ) { return ( ( molongui_get_customizer_setting( $control, 'show_avatar', '1' ) and !molongui_get_customizer_setting( $control, 'avatar_border_style', 'none' ) ) ? true : false ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_text_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '#dd9933',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Font color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,//!$is_pro,
						),
						'allow_addition'  => false,
                        'active_callback' => function( $control ) { return ( ( molongui_get_customizer_setting( $control, 'show_avatar', '1' ) and molongui_get_customizer_setting( $control, 'avatar_src', 'acronym' ) ) ? true : false ); },
                    ),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_bg_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '#000000',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Background color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => !$is_pro,
						),
						'allow_addition'  => false,
                        'active_callback' => function( $control ) { return ( ( molongui_get_customizer_setting( $control, 'show_avatar', '1' ) and molongui_get_customizer_setting( $control, 'avatar_src', 'acronym' ) ) ? true : false ); },
					),
				),
                array
                (
                    'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[avatar_link_to_archive]',
                    'display' => true,
                    'setting' => array
                    (
                        'type'                 => 'option',
                        'capability'           => 'manage_options',
                        'default'              => 1,
                        'transport'            => 'postMessage',
                        'validate_callback'    => '',
                        'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
                        'sanitize_js_callback' => '',
                        'dirty'                => false,
                    ),
                    'control' => array
                    (
                        'label'           => __( "Link to archive", 'molongui-authorship' ),
                        'description'     => ( !$is_pro ? __( "Whether to make the author avatar link to the author's archive page.", 'molongui-authorship' ) : __( "Whether to make the author avatar link to the author's archive page. Regardless of this setting being enabled, the author avatar might not become a link. i.e. When author archive pages are disabled.", 'molongui-authorship' ) ),
                        'priority'        => 10,
                        'class'           => '\Molongui_Customize_Text_Radio_Button_Control',
                        'type'            => 'molongui-text-radio',
                        'choices'         => array
                        (
                            '1' => array
                            (
                                'display' => true,
                                'label'   => __( "Link", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            '0' => array
                            (
                                'display' => true,
                                'label'   => __( "No link", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                        ),
                        'input_attrs'     => array(),
                        'allow_addition'  => false,
                        'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_avatar', '1' ); },
                    ),
                ),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_name',
			'title'              => __( "Name", 'molongui-authorship' ),
			'description'        => '',
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => 'molongui_name_typography_settings',
					'display' => true,
					'setting' => array
					(
						'sanitize_callback' => 'esc_html',
					),
					'control' => array
					(
						'label'           => __( "Typography", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Group_Label_Control',
						'type'            => 'molongui-compact-group-label',
						'active_callback' => '',
						'input_attrs'     => array(),
						'choices'         => array(),
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[name_text_size]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 22,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Size", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'molongui-compact-range-flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
                            'off'     => true, // 'min' = 7 => OFF; Font size is taken from theme's rules as 'molongui-font-size-7-px' doesn't exist.
                            'min'     => 7,
							'max'     => 100,
							'step'    => 1,
							'suffix'  => 'px',
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[name_text_style]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Style", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Checkbox_Button_Control',
						'type'            => 'molongui-compact-image-checkbox',
						'choices'         => array
						(
							'normal' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-normal.png',
								'label'   => __( "Normal", 'molongui-authorship' ),
								'premium' => false,
							),
							'bold' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-bold.png',
								'label'   => __( "Bold", 'molongui-authorship' ),
								'premium' => false,
							),
							'italic' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-italic.png',
								'label'   => __( "Italic", 'molongui-authorship' ),
                                'premium' => false,
							),
							'underline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-underline.png',
								'label'   => __( "Underline", 'molongui-authorship' ),
                                'premium' => false,
							),
							'overline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-overline.png',
								'label'   => __( "Overline", 'molongui-authorship' ),
                                'premium' => false,
							),
							'overunderline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-overunderline.png',
								'label'   => __( "Overline and underline", 'molongui-authorship' ),
                                'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => true,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[name_text_case]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'none',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Case", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-compact-image-radio',
						'choices'         => array
						(
							'none' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-none.png',
								'label'   => __( "Leave as is", 'molongui-authorship' ),
								'premium' => false,
							),
							'capitalize' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-capitalize.png',
								'label'   => __( "Capitalize", 'molongui-authorship' ),
                                'premium' => false,
							),
							'uppercase' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-uppercase.png',
								'label'   => __( "Uppercase", 'molongui-authorship' ),
                                'premium' => false,
							),
							'lowercase' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-lowercase.png',
								'label'   => __( "Lowercase", 'molongui-authorship' ),
                                'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[name_text_align]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'left',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Align", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-compact-image-radio',
						'choices'         => array
						(
							'left' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-left.png',
								'label'   => __( "Left" ),
								'premium' => false,
							),
							'center' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-center.png',
								'label'   => __( "Center" ),
								'premium' => false,
							),
							'right' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-right.png',
								'label'   => __( "Right" ),
								'premium' => false,
							),
							'justify' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-justify.png',
								'label'   => __( "Justify" ),
								'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[name_text_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'inherit',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-compact-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[name_link_to_archive]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 1,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Link to archive", 'molongui-authorship' ),
						'description'     => ( !$is_pro ? __( "Whether to make the author name link to the author's archive page.", 'molongui-authorship' ) : __( "Whether to make the author name link to the author's archive page. Regardless of this setting being enabled, the author name might not become a link. i.e. When author archive pages are disabled.", 'molongui-authorship' ) ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Text_Radio_Button_Control',
						'type'            => 'molongui-text-radio',
						'allow_addition'  => false,
						'active_callback' => '',
						'input_attrs'     => array(),
						'choices'         => array
						(
							'1' => array
							(
                                'display' => true,
								'label'   => __( "Link", 'molongui-authorship' ),
								'premium' => false,
							),
							'0' => array
							(
                                'display' => true,
								'label'   => __( "No link", 'molongui-authorship' ),
								'premium' => false,
							),
						),
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[name_inherited_underline]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'keep',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Remove inherited underline", 'molongui-authorship' ),
						'description'     => __( "Whether to (try to) remove the underline added by your theme or any other third plugin.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Text_Radio_Button_Control',
						'type'            => 'molongui-text-radio',
						'allow_addition'  => false,
						'active_callback' => '',
						'input_attrs'     => array(),
						'choices'         => array
						(
							'keep' => array
							(
                                'display' => true,
								'label'   => __( "Keep as is", 'molongui-authorship' ),
								'premium' => false,
							),
							'remove' => array
							(
                                'display' => true,
								'label'   => __( "Remove it", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
						),
					),
				),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_meta',
			'title'              => __( "Metadata", 'molongui-authorship' ),
			'description'        => '',
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
                array
                (
                    'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[show_meta]',
                    'display' => true,
                    'setting' => array
                    (
                        'type'                 => 'option',
                        'capability'           => 'manage_options',
                        'default'              => 1,
                        'transport'            => 'refresh',
                        'validate_callback'    => '',
                        'sanitize_callback'    => '',
                        'sanitize_js_callback' => '',
                        'dirty'                => false,
                    ),
                    'control' => array
                    (
                        'label'           => __( "Display", 'molongui-authorship' ),
                        'description'     => __( "Whether to show the author metadata.", 'molongui-authorship' ),
                        'priority'        => 10,
                        'class'           => '\Molongui_Customize_Text_Radio_Button_Control',
                        'type'            => 'molongui-text-radio',
                        'choices'         => array
                        (
                            '1' => array
                            (
                                'display' => true,
                                'label'   => __( "Show" ),
                                'premium' => false,
                            ),
                            '0' => array
                            (
                                'display' => true,
                                'label'   => __( "Hide" ),
                                'premium' => false,
                            ),
                        ),
                        'input_attrs'     => array
                        (
                            'premium' => false,
                        ),
                        'allow_addition'  => false,
                        'active_callback' => '',
                    ),
                ),
				array
				(
					 'id'      => 'molongui_meta_typography_settings',
					 'display' => true,
					 'setting' => array
					 (
						 'sanitize_callback' => 'esc_html',
					 ),
					 'control' => array
					 (
						 'label'           => __( "Typography", 'molongui-authorship' ),
						 'description'     => '',
						 'priority'        => 10,
						 'class'           => '\Molongui_Customize_Group_Label_Control',
						 'type'            => 'molongui-compact-group-label',
						 'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_meta', '1' ); },
						 'input_attrs'     => array(),
						 'choices'         => array(),
					 ),
				 ),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[meta_text_size]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 12,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Size", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'molongui-compact-range-flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
                            'off'     => true, // 'min' = 7 => OFF; Font size is taken from theme's rules as 'molongui-font-size-7-px' doesn't exist.
                            'min'     => 7,
							'max'     => 100,
							'step'    => 1,
							'suffix'  => 'px',
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_meta', '1' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[meta_text_style]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Style", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Checkbox_Button_Control',
						'type'            => 'molongui-compact-image-checkbox',
						'choices'         => array
						(
							'normal' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-normal.png',
								'label'   => __( "Normal", 'molongui-authorship' ),
								'premium' => false,
							),
							'bold' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-bold.png',
								'label'   => __( "Bold", 'molongui-authorship' ),
								'premium' => false,
							),
							'italic' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-italic.png',
								'label'   => __( "Italic", 'molongui-authorship' ),
                                'premium' => false,
							),
							'underline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-underline.png',
								'label'   => __( "Underline", 'molongui-authorship' ),
                                'premium' => false,
							),
							'overline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-overline.png',
								'label'   => __( "Overline", 'molongui-authorship' ),
                                'premium' => false,
							),
							'overunderline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-overunderline.png',
								'label'   => __( "Overline and underline", 'molongui-authorship' ),
                                'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => true,
						'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_meta', '1' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[meta_text_case]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'none',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Case", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-compact-image-radio',
						'choices'         => array
						(
							'none' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-none.png',
								'label'   => __( "Leave as is", 'molongui-authorship' ),
								'premium' => false,
							),
							'capitalize' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-capitalize.png',
								'label'   => __( "Capitalize", 'molongui-authorship' ),
                                'premium' => false,
							),
							'uppercase' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-uppercase.png',
								'label'   => __( "Uppercase", 'molongui-authorship' ),
                                'premium' => false,
							),
							'lowercase' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-lowercase.png',
								'label'   => __( "Lowercase", 'molongui-authorship' ),
                                'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_meta', '1' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[meta_text_align]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'left',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Align", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-compact-image-radio',
						'choices'         => array
						(
							'left' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-left.png',
								'label'   => __( "Left" ),
								'premium' => false,
							),
							'center' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-center.png',
								'label'   => __( "Center" ),
								'premium' => false,
							),
							'right' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-right.png',
								'label'   => __( "Right" ),
								'premium' => false,
							),
							'justify' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-justify.png',
								'label'   => __( "Justify" ),
								'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_meta', '1' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[meta_text_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'inherit',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-compact-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_meta', '1' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[at]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
                        'default'              => '',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_string',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "'at' label", 'molongui-authorship' ),
						'description'     => __( "Text to show between author's job position and company.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => 'WP_Customize_Control',
						'type'            => 'text',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium'     => false,
							'placeholder' => __( "at", 'molongui-authorship' ),
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_meta', '1' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[web]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
                        'default'              => '',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_string',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "'Website' label", 'molongui-authorship' ),
						'description'     => __( "Text to show as link name to author's personal website.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => 'WP_Customize_Control',
						'type'            => 'text',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium'     => false,
							'placeholder' => __( "Website", 'molongui-authorship' ),
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_meta', '1' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[more_posts]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
                        'default'              => '',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_string',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "'+ posts' label", 'molongui-authorship' ),
						'description'     => __( "Text to show as toggle button to display author's related posts when displaying author bio.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => 'WP_Customize_Control',
						'type'            => 'text',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium'     => false,
							'placeholder' => __( "+ posts", 'molongui-authorship' ),
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return ( ( molongui_get_customizer_setting( $control, 'show_meta', '1' ) and molongui_get_customizer_setting( $control, 'box_layout', 'slim' ) ) ? true : false ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bio]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
                        'default'              => '',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_string',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "'Bio' label", 'molongui-authorship' ),
						'description'     => __( "Text to show as toggle button to display author's bio when displaying related posts.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => 'WP_Customize_Control',
						'type'            => 'text',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium'     => false,
							'placeholder' => __( 'Bio', 'molongui-authorship' ),
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return ( ( molongui_get_customizer_setting( $control, 'show_meta', '1' ) and molongui_get_customizer_setting( $control, 'box_layout', 'slim' ) ) ? true : false ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[meta_separator]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '|',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Separator", 'molongui-authorship' ),
						'description'     => __( "Character used to separate author meta data information", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Select_Control',
						'type'            => 'select',
						'choices'         => array
						(
							'|' => array
							(
								'label'    => __( '|' ),
								'disabled' => false,
								'premium'  => false,
							),
							'||' => array
							(
								'label'    => __( '||' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'/' => array
							(
								'label'    => __( '/' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'//' => array
							(
								'label'    => __( '//' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'-' => array
							(
								'label'    => __( '–' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'--' => array
							(
								'label'    => __( '--' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'–' => array
							(
								'label'    => __( '–' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'·' => array
							(
								'label'    => __( '·' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'•' => array
							(
								'label'    => __( '•' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'>' => array
							(
								'label'    => __( '>' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'>>' => array
							(
								'label'    => __( '>>' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'~' => array
							(
								'label'    => __( '~' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'*' => array
							(
								'label'    => __( '*' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'⇄' => array
							(
								'label'    => __( '⇄' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'⊥' => array
							(
								'label'    => __( '⊥' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'⋄' => array
							(
								'label'    => __( '⋄' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
							'<br>' => array
							(
								'label'    => __( 'Line break', 'molongui-authorship' ),
								'disabled' => !$is_pro,
								'premium'  => !$is_pro,
							),
						),
						'input_attrs'     => array
						(
							'premium' => !$is_pro,
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'show_meta', '1' ); },
					),
				),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_bio',
			'title'              => __( "Biography", 'molongui-authorship' ),
			'description'        => '',
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => 'molongui_bio_typography_settings',
					'display' => true,
					'setting' => array
					(
						'sanitize_callback' => 'esc_html',
					),
					'control' => array
					(
						'label'           => __( "Typography", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Group_Label_Control',
						'type'            => 'molongui-compact-group-label',
						'active_callback' => '',
						'input_attrs'     => array(),
						'choices'         => array(),
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bio_text_size]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 14,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Size", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'molongui-compact-range-flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
                            'off'     => true, // 'min' = 7 => OFF; Font size is taken from theme's rules as 'molongui-font-size-7-px' doesn't exist.
                            'min'     => 7,
							'max'     => 100,
							'step'    => 1,
							'suffix'  => 'px',
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bio_line_height]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 1,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Line height", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'molongui-compact-range-flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
							'min'     => 1,
							'max'     => 3,
							'step'    => 0.1,
							'suffix'  => '',
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bio_text_style]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'normal',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Style", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Checkbox_Button_Control',
						'type'            => 'molongui-compact-image-checkbox',
						'choices'         => array
						(
							'normal' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-normal.png',
								'label'   => __( "Normal", 'molongui-authorship' ),
								'premium' => false,
							),
							'bold' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-bold.png',
								'label'   => __( "Bold", 'molongui-authorship' ),
								'premium' => false,
							),
							'italic' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-italic.png',
								'label'   => __( "Italic", 'molongui-authorship' ),
                                'premium' => false,
							),
							'underline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-underline.png',
								'label'   => __( "Underline", 'molongui-authorship' ),
                                'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => true,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bio_text_case]',
					'display' => false,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'none',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Case", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-compact-image-radio',
						'choices'         => array
						(
							'none' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-none.png',
								'label'   => __( "Leave as is", 'molongui-authorship' ),
								'premium' => false,
							),
							'capitalize' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-capitalize.png',
								'label'   => __( "Capitalize", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'uppercase' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-uppercase.png',
								'label'   => __( "Uppercase", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
							'lowercase' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-lowercase.png',
								'label'   => __( "Lowercase", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bio_text_align]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'justify',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Align", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-compact-image-radio',
						'choices'         => array
						(
							'left' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-left.png',
								'label'   => __( "Left" ),
								'premium' => false,
							),
							'center' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-center.png',
								'label'   => __( "Center" ),
								'premium' => false,
							),
							'right' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-right.png',
								'label'   => __( "Right" ),
								'premium' => false,
							),
							'justify' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/text-align/text-align-justify.png',
								'label'   => __( "Justify" ),
								'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => true,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bio_text_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'inherit',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-compact-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
                array
                (
                    'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[bio_field]',
                    'display' => true,
                    'setting' => array
                    (
                        'type'                 => 'option',
                        'capability'           => 'manage_options',
                        'default'              => 'full',
                        'transport'            => 'refresh',
                        'validate_callback'    => '',
                        'sanitize_callback'    => '',
                        'sanitize_js_callback' => '',
                        'dirty'                => false,
                    ),
                    'control' => array
                    (
                        'label'           => __( "Content", 'molongui-authorship' ),
                        'description'     => __( "Whether to display short or full bio in the author box. Full bio will be displayed on author archive pages if your theme supports it.", 'molongui-authorship' ),
                        'priority'        => 10,
                        'class'           => '\Molongui_Customize_Text_Radio_Button_Control',
                        'type'            => 'molongui-text-radio',
                        'choices'         => array
                        (
                            'full' => array
                            (
                                'display' => true,
                                'label'   => __( "Full", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'short' => array
                            (
                                'display' => true,
                                'label'   => __( "Short", 'molongui-authorship' ),
                                'premium' => !$is_pro,
                            ),
                        ),
                        'input_attrs'     => array
                        (
                            'premium'  => !$is_pro,
                            'disabled' => !$is_pro,
                        ),
                        'allow_addition'  => false,
                        'active_callback' => '',
                    ),
                ),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_icons',
			'title'              => __( "Social icons", 'molongui-authorship' ),
			'description'        => '',
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[show_icons]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 1,
						'transport'            => 'refresh',
						'validate_callback'    => '',
						'sanitize_callback'    => '',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Social icons", 'molongui-authorship' ),
						'description'     => __( "Whether to show social media icons.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Text_Radio_Button_Control',
						'type'            => 'molongui-text-radio',
						'choices'         => array
						(
							'1' => array
							(
                                'display' => true,
								'label'   => __( "Show" ),
								'premium' => false,
							),
							'0' => array
							(
                                'display' => true,
								'label'   => __( "Hide" ),
								'premium' => false,
							),
						),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[icons_style]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'default',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Style", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'default' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/icons-style/icons-style-default.png',
								'label'   => __( "Default", 'molongui-authorship' ),
								'premium' => false,
							),
							'squared' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/icons-style/icons-style-squared.png',
								'label'   => __( "Squared", 'molongui-authorship' ),
                                'premium' => false,
							),
							'circled' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/icons-style/icons-style-circled.png',
								'label'   => __( "Circled", 'molongui-authorship' ),
                                'premium' => false,
							),
							'boxed' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/icons-style/icons-style-boxed.png',
								'label'   => __( "Boxed", 'molongui-authorship' ),
                                'premium' => false,
							),
							'branded' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/icons-style/icons-style-branded.png',
								'label'   => __( "Branded", 'molongui-authorship' ),
                                'premium' => false,
							),
							'branded-squared' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/icons-style/icons-style-branded-squared.png',
								'label'   => __( "Branded squared", 'molongui-authorship' ),
                                'premium' => false,
							),
							'branded-squared-reverse' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/icons-style/icons-style-branded-squared-reverse.png',
								'label'   => __( "Branded squared reverse", 'molongui-authorship' ),
                                'premium' => false,
							),
							'branded-circled' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/icons-style/icons-style-branded-circled.png',
								'label'   => __( "Branded circled", 'molongui-authorship' ),
                                'premium' => false,
							),
							'branded-circled-reverse' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/icons-style/icons-style-branded-circled-reverse.png',
								'label'   => __( "Branded circled reverse", 'molongui-authorship' ),
                                'premium' => false,
							),
							'branded-boxed' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/icons-style/icons-style-branded-boxed.png',
								'label'   => __( "Branded boxed", 'molongui-authorship' ),
                                'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => 'molongui_active_icons_setting',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[icons_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '#999999',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
						'active_callback' => 'molongui_active_icons_color_setting',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[icons_size]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 20,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Font size", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
							'min'     => 8,
							'max'     => 100,
							'step'    => 1,
							'suffix'  => 'px',
						),
						'allow_addition'  => false,
						'active_callback' => 'molongui_active_icons_setting',
					),
				),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_related',
			'title'              => __( "Template", 'molongui-authorship' ),
			'description'        => '',
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[related_layout]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'layout-1',
						'transport'            => 'refresh',
						'validate_callback'    => '',
                        'sanitize_callback'    => '', //'molongui_authorship_sanitize_setting', // Enabling this function would prevent live preview
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Related entries template", 'molongui-authorship' ),
						'description'     => __( "The template to be used to render related entries section.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
						'type'            => 'molongui-image-radio',
						'choices'         => array
						(
							'layout-1' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/related-template/related-template-1.png',
								'label'   => __( "Template 1", 'molongui-authorship' ),
								'premium' => false,
							),
							'layout-2' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/related-template/related-template-2.png',
								'label'   => __( "Template 2", 'molongui-authorship' ),
								'premium' => false,
							),
							'layout-3' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/img/related-template/related-template-3.png',
								'label'   => __( "Template 3", 'molongui-authorship' ),
								'premium' => !$is_pro,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[related_title]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
                        'default'              => __( "Related Entries", 'molongui-authorship' ),
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => '',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Section Label", 'molongui-authorship' ),
						'description'     => __( "If you have the Pro version of the plugin, you can use {author_name} (curly brackets included) in your string. It will be replaced by the actual author name on the frontend. Or leave empty to remove the section label.", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => 'WP_Customize_Control',
						'type'            => 'text',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium'     => false,
							'placeholder' => __( "Related Entries", 'molongui-authorship' ),
						),
						'allow_addition'  => false,
						'active_callback' => function( $control ) { return molongui_get_customizer_setting( $control, 'box_layout', 'stacked' ); },
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[no_related_posts]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
                        'default'              => '',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_string',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "'No related posts found' label", 'molongui-authorship' ),
						'description'     => __( "Text to display when no related entries are found", 'molongui-authorship' ),
						'priority'        => 10,
						'class'           => 'WP_Customize_Control',
						'type'            => 'text',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium'     => false,
							'placeholder' => __( "This author does not have any more posts.", 'molongui-authorship' ),
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
			),
		),
		array
		(
			'id'                 => 'molongui_authorship_related_config',
			'title'              => __( "Entries", 'molongui-authorship' ),
			'description'        => '',
			'display'            => true,
			'priority'           => '',
			'type'               => '',
			'capability'         => 'manage_options',
			'active_callback'    => '',
			'description_hidden' => true,
			'fields'             => array
			(
				array
				(
					'id'      => 'molongui_related_typography_settings',
					'display' => true,
					'setting' => array
					(
						'sanitize_callback' => 'esc_html',
					),
					'control' => array
					(
						'label'           => __( "Typography", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Group_Label_Control',
						'type'            => 'molongui-compact-group-label',
						'active_callback' => '',
						'input_attrs'     => array(),
						'choices'         => array(),
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[related_text_size]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 14,
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Size", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Range_Control',
						'type'            => 'molongui-compact-range-flat',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
                            'off'     => true, // 'min' = 7 => OFF; Font size is taken from theme's rules as 'molongui-font-size-7-px' doesn't exist.
                            'min'     => 7,
							'max'     => 100,
							'step'    => 1,
							'suffix'  => 'px',
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[related_text_style]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => '',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Style", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Image_Checkbox_Button_Control',
						'type'            => 'molongui-compact-image-checkbox',
						'choices'         => array
						(
							'normal' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-normal.png',
								'label'   => __( "Normal", 'molongui-authorship' ),
								'premium' => false,
							),
							'bold' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-bold.png',
								'label'   => __( "Bold", 'molongui-authorship' ),
								'premium' => false,
							),
							'italic' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-italic.png',
								'label'   => __( "Italic", 'molongui-authorship' ),
                                'premium' => false,
							),
							'underline' => array
							(
								'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-style/font-style-underline.png',
								'label'   => __( "Underline", 'molongui-authorship' ),
                                'premium' => false,
							),
						),
						'input_attrs'     => array(),
						'allow_addition'  => true,
						'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[related_text_case]',
                    'display' => true,
                    'setting' => array
                    (
                        'type'                 => 'option',
                        'capability'           => 'manage_options',
                        'default'              => 'none',
                        'transport'            => 'postMessage',
                        'validate_callback'    => '',
                        'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
                        'sanitize_js_callback' => '',
                        'dirty'                => false,
                    ),
                    'control' => array
                    (
                        'label'           => __( "Case", 'molongui-authorship' ),
                        'description'     => '',
                        'priority'        => 10,
                        'class'           => '\Molongui_Customize_Image_Radio_Button_Control',
                        'type'            => 'molongui-compact-image-radio',
                        'choices'         => array
                        (
                            'none' => array
                            (
                                'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-none.png',
                                'label'   => __( "Leave as is", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'capitalize' => array
                            (
                                'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-capitalize.png',
                                'label'   => __( "Capitalize", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'uppercase' => array
                            (
                                'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-uppercase.png',
                                'label'   => __( "Uppercase", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                            'lowercase' => array
                            (
                                'image'   => MOLONGUI_AUTHORSHIP_URL.'customizer/common/img/font-case/font-case-lowercase.png',
                                'label'   => __( "Lowercase", 'molongui-authorship' ),
                                'premium' => false,
                            ),
                        ),
                        'input_attrs'     => array(),
                        'allow_addition'  => false,
                        'active_callback' => '',
					),
				),
				array
				(
					'id'      => MOLONGUI_AUTHORSHIP_PREFIX.'_options[related_text_color]',
					'display' => true,
					'setting' => array
					(
						'type'                 => 'option',
						'capability'           => 'manage_options',
						'default'              => 'inherit',
						'transport'            => 'postMessage',
						'validate_callback'    => '',
						'sanitize_callback'    => 'molongui_authorship_sanitize_setting',
						'sanitize_js_callback' => '',
						'dirty'                => false,
					),
					'control' => array
					(
						'label'           => __( "Color", 'molongui-authorship' ),
						'description'     => '',
						'priority'        => 10,
						'class'           => '\Molongui_Customize_Color_Control',
						'type'            => 'molongui-compact-color',
						'choices'         => array(),
						'input_attrs'     => array
						(
							'premium' => false,
						),
						'allow_addition'  => false,
						'active_callback' => '',
					),
				),
			),
		),
    ), // !sections index
);