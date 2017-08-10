<?php

/**
 * Class Abstract_Post_Formats
 * Adds the Longform post format option so it can be enabled
 * on a per-site basis and adds a "Longform" meta box to
 * corresponding posts.
 */
abstract class Abstract_Post_Format {

	static $slug;
	static $name;
	static $option_name;

	public function __construct() {
		// Inject toggle settings into existing options page.
		add_filter( 'cpf_site_options_page', array( get_called_class(), 'register_setting' ) );
		if ( is_admin() ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( get_called_class(), 'save_post' ) );
		}

		// Need to find a way to assess if a custom post format is turned on, and only load scripts when enabled
		add_action( 'wp_enqueue_scripts', array( get_called_class(), 'enqueue_scripts' ) );
		add_action( 'wp_print_styles', array( get_called_class(), 'print_styles' ), 90 );
	}

	/**
	 * @param                   $custom_formats
	 * @param Abstract_Post_Formats $post_format
	 *
	 * @return array
	 */
	static function add_format( $custom_formats, $post_format ) {
		return array_merge( $custom_formats, [ strtolower( $post_format::$name ) ] );
	}

	/**
	 * Add meta box to 'post' post types
	 *
	 * @param $post_type
	 */
	public function add_meta_box( $post_type ) {
		// Register custom meta box if post format has custom settings
	}

	/**
	 * Display post meta box with values
	 *
	 * @param $post
	 */
	public function meta_callback( $post ) {
	}

	/**
	 * Save all post meta
	 *
	 * @param $post_id
	 */
	static function save_post( $post_id ) {
		// Handle saving of post formats custom meta if it exists.
	}

	/**
	 * Add scripts and styles
	 */
	static function enqueue_scripts() {
	}

	/**
	 * Print custom post format styles
	 */
	static function print_styles() {
	}

	/**
	 * Add site options for enabling custom format
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	static function register_setting( $options ) {
		$permissions = $options->parts['features'];
		$permissions->add_part( $pages_lock = new sm_checkbox( static::$option_name, array(
			'label'   => str_replace( [ '-', '_' ], ' ', static::$name ) . ' Post Format',
			'value'   => 'enabled',
			'classes' => array( 'onOffSwitch' ),
		) ) );

		return $options;
	}

	/**
	 * Build our own custom post format
	 * todo: this function could be reduced
	 *
	 * @param        $post
	 * @param string $format
	 *
	 * @return array|false|WP_Error
	 */
	static function set_custom_post_format( $post, $format = '' ) {

		$post = get_post( $post );

		if ( empty( $post ) ) {
			return new WP_Error( 'invalid_post', __( 'Invalid post.' ) );
		}

		if ( ! empty( $format ) ) {
			$format = sanitize_key( $format );
			if ( 'standard' === $format ) {
				$format = '';
			} else {
				$format = 'post-format-' . $format;
			}
		}

		return wp_set_post_terms( $post->ID, $format, 'post_format' );
	}

	public static function matches_custom_format( $format ) {
		if ( strtolower( $format ) == get_post_format() ) {
			return true;
		}

		return false;
	}
}
