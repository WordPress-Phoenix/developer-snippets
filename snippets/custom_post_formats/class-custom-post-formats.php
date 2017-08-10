<?php

/**
 * Class Post_Formats
 *
 * TODO: not matching previously stored post format in db, likely related to uppercase first letter? @see line 93
 * TODO: figure out how to disable post formats custom meta box if all custom formats are disabled (check screen options too)
 */
class Custom_Post_Formats {
	public $formats = [ 'video' => 'Video', 'slideshow' => 'Slideshow' ];
	public $enabled = [ 'video' => 'video_status', 'slideshow' => 'slideshow_status' ];

	function __construct( $theme ) {
		// Load up the abstract post format class
		include_once( 'class-custom-post-format.php' );
		// Begin creating new post formats
		include_once( 'class-custom-longform-format.php' );
		$this->add_format( new Longform_Format() );
		include_once( 'class-custom-event-preview-format.php' );
		$this->add_format( new Event_Preview_Format() );
		add_action( 'save_post', array( get_called_class(), 'save_post' ) );

		if ( is_admin() ) {
			add_action( 'add_meta_boxes', array( $this, 'add_custom_formats_meta_box' ) );
			add_action( 'admin_init', array( $this, 'add_post_formats' ), 110 );
		}
	}

	/**
	 * @param Post_Format $post_format
	 */
	public function add_format( $post_format ) {
		$this->formats[ strtolower( $post_format::$name ) ] = $post_format::$name;
		$this->enabled[ strtolower( $post_format::$name ) ] = $post_format::$option_name;
	}

	/**
	 * Add the custom post format
	 */
	function add_post_formats() {
		static::add_custom_theme_support( 'post-formats', $this->formats );
	}

	public function add_custom_formats_meta_box( $post_type ) {
		add_meta_box(
			'post_format',
			__( 'Post Formats', 'cpf-textdomain' ),
			array( $this, 'build_custom_post_formats' ),
			$post_type,
			'side',
			'default'
		);
	}

	/**
	 * Commandeer post formats functionality
	 * Work around to enable custom post formats
	 *
	 * @param $feature
	 */
	static function add_custom_theme_support( $feature ) {

		// Confirm only post formats are handled through here
		if ( 'post-format' !== $feature ) {
			add_theme_support( $feature );
		}

		global $_wp_theme_features;

		if ( func_num_args() == 1 ) {
			$args = true;
		} else {
			$args = array_slice( func_get_args(), 1 );
		}

		$_wp_theme_features[ $feature ] = $args;
	}

	/**
	 * Build custom post formats metabox
	 *
	 * @param $post
	 */
	public function build_custom_post_formats( $post ) {

		$post_formats = static::get_custom_theme_support( 'post-formats' );
		$post_format  = get_post_format( $post->ID );

		if ( is_array( $post_formats[0] ) ) :
			//$post_format = get_post_format( $post->ID );
			if ( ! $post_format ) {
				$post_format = '0';
			}
			// Add in the current one if it isn't there yet, in case the current theme doesn't support it
			// lowercase $post_format matches key (not value) - check key or convert $post_format to lower?
			if ( $post_format && ! array_key_exists( $post_format, $post_formats[0] ) ) {
				$post_formats[0][] = $post_format;
			}
			?>

			<style id="custom-formats-mods">
				#formatdiv,
				#formatdiv input {
					display: none !important;
				}

				<?php
				// hide checkbox from screen options if not post format longform
				if ('longform' !== $post_format):?>
				label[for="longform_meta-hide"] {
					display: none !important;
				}

				<?php endif; ?>

			</style>

			<div id="post-formats-select">
				<fieldset>
					<legend class="screen-reader-text"><?php _e( 'Post Formats' ); ?></legend>
					<input type="radio" name="post_format" class="post-format" id="fs-post-format-0"
					       value="0" <?php checked( $post_format, '0' ); ?> /> <label for="fs-post-format-0"
					                                                                  class="post-format-icon post-format-standard"><?php echo get_post_format_string( 'standard' ); ?></label>

					<?php foreach ( $post_formats[0] as $format ) :
						$slug = strtolower( $format );
						$format_enabled = $this->is_post_format_enabled( $this->enabled[ $slug ] );
						if ( 'enabled' == $format_enabled || true == $format_enabled) {
							$label = str_replace( ['-', '_'], ' ', esc_html( $this->get_custom_post_format_string( $slug ) ) );
							?>
							<br/><input type="radio" name="post_format" class="post-format"
							            id="fs-post-format-<?php echo esc_attr( $slug ); ?>"
							            value="<?php echo esc_attr( $format ); ?>" <?php checked( $post_format, $slug ); ?> />
							<label for="fs-post-format-<?php echo esc_attr( $slug ); ?>"
							       class="post-format-icon post-format-<?php echo esc_attr( $slug ); ?>"><?php echo $label ?></label>
						<?php } ?>
					<?php endforeach; ?>
				</fieldset>
			</div>

		<?php endif;
	}

	/**
	 * Combine the custom post format with built in post format
	 *
	 * @return array Array of post formats
	 */
	function get_custom_post_format_strings() {
		$strings = get_post_format_strings();
		$formats = $this->formats;
		$strings = array_merge( $strings, $formats );

		return $strings;
	}

	/**
	 * Retrieve any custom post format types set to the $_wp_theme_features global
	 *
	 * @param $feature
	 *
	 * @return mixed
	 */
	static function get_custom_theme_support( $feature ) {

		// Confirm only post formats are handled through here
		if ( 'post-formats' !== $feature ) {
			return get_theme_support( $feature );
		}

		global $_wp_theme_features;

		return $_wp_theme_features[ $feature ];
	}

	/**
	 * Set the labels to display on post metabox
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	function get_custom_post_format_string( $slug ) {
		$slug = strtolower( $slug );
		$strings = $this->get_custom_post_format_strings();

		if ( ! $slug ) {
			return $strings['standard'];
		} else {
			return ( isset( $strings[ $slug ] ) ) ? $strings[ $slug ] : '';
		}
	}

	/**
	 * Check whether custom post format is enabled
	 *
	 * @param $format
	 *
	 * @return bool|mixed|void
	 */
	function is_post_format_enabled( $format ) {
		// video and slideshow post formats do not have options set in db
		if ( 'video_status' == $format || 'slideshow_status' == $format ) {
			return true;
		}
		$option_value = 'option_' . $format;
		$enabled = get_option( $option_value );

		return $enabled;
	}

	/**
	 * Save  meta box settings on post save
	 *
	 * @param $post_id
	 */
	static function save_post( $post_id ) {
		// Checks save status
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );

		if ( $is_autosave || $is_revision || ! isset( $_POST['post_format'] ) ) {
			return;
		}

		// Set post to slideshow if page breaks exist in the content. This correlates to tps plugin.
		if ( empty( $_POST['post_format'] ) ) {
			$post = get_post( $post_id );
			if ( stristr( $post->post_content, '<!--nextpage-->' ) ) {
				$_POST['post_format'] = 'slideshow';
			}
		}

		Post_Format::set_custom_post_format( $post_id, $_POST['post_format'] );
	}
}
