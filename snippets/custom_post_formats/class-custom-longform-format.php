<?php
/**
 * Class Custom_Longform_Format
 */
class Custom_Longform_Format extends Custom_Post_Format {
	static $slug = 'post-format-longform';
	static $name = 'Longform';
	static $option_name = 'longform_status';

	/**
	 * Add custom meta options for Longform
	 *
	 * @param $post_type
	 */
	public function add_meta_box( $post_type ) {
		// Limit meta box to certain post types.
		$post_types = array( 'post' );

		if ( in_array( $post_type, $post_types ) ) {

			add_meta_box(
				'cpf_longform_meta',
				__( 'Longform Settings', 'cpf-textdomain' ),
				array( $this, 'meta_callback' ),
				$post_type,
				'normal',
				'high'
			);

		}
	}

	/**
	 * Save longform meta box settings on post save
	 *
	 * @param $post_id
	 */
	static function save_post( $post_id ) {

		// Checks save status
		$is_autosave    = wp_is_post_autosave( $post_id );
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['cpf_longform_nonce'] ) && wp_verify_nonce( $_POST['cpf_longform_nonce'], basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Checks for input and sanitizes/saves if needed
		if ( isset( $_POST['cpf_longform_title'] ) ) {
			update_post_meta( $post_id, 'cpf_longform_title', $_POST['cpf_longform_title'] );
		}

		if ( isset( $_POST['cpf_longform_color'] ) ) {
			update_post_meta( $post_id, 'cpf_longform_color', $_POST['cpf_longform_color'] );
		}

		if ( isset( $_POST['cpf_longform_tfont'] ) ) {
			update_post_meta( $post_id, 'cpf_longform_tfont', $_POST['cpf_longform_tfont'] );
		}

		if ( isset( $_POST['cpf_longform_bfont'] ) ) {
			update_post_meta( $post_id, 'cpf_longform_bfont', $_POST['cpf_longform_bfont'] );
		}

		if ( isset( $_POST['cpf_longform_title_align'] ) ) {
			update_post_meta( $post_id, 'cpf_longform_title_align', $_POST['cpf_longform_title_align'] );
		}

		if ( isset( $_POST['cpf_longform_position'] ) ) {
			update_post_meta( $post_id, 'cpf_longform_position', $_POST['cpf_longform_position'] );
		}

	}

	/**
	 * Callback to build longform meta options html
	 *
	 * @param $post
	 */
	public function meta_callback( $post ) {

		wp_nonce_field( basename( __FILE__ ), 'cpf_longform_nonce' );
		$cpf_lf_meta = static::post_meta_array();
		?>

		<div class="cpf_longform_metabox cpf_longform_title">
			<div>
				<label for="cpf_longform_title">Longform Title: <span class='cpf_longform_title'>50 Characters Left</span></label>
				<input type="text" name="cpf_longform_title" placeholder="Enter title here" id="cpf_longform_title"
				       maxlength="50" class="full" value="<?php echo $cpf_lf_meta['title']; ?>"/>
			</div>
			<div class="thirds">
				<label for="cpf_longform_color">Longform Color:</label>
				<input type="color" name="cpf_longform_color" id="cpf_longform_color"
				       value="<?php echo $cpf_lf_meta['color'] ?>" class="full"/>
			</div>
			<div class="thirds">
				<label for="cpf_longform_tfont">Longform Title Font:</label>
				<select id="cpf_longform_tfont" class="full" name="cpf_longform_tfont">
					<?php echo $this->build_font_options( $cpf_lf_meta['tfont'] ); ?>
				</select>
			</div>
			<div class="thirds thirds-last">
				<label for="cpf_longform_bfont">Longform Body Font:</label>
				<select id="cpf_longform_bfont" class="full" name="cpf_longform_bfont">
					<?php echo $this->build_font_options( $cpf_lf_meta['bfont'],'bodyfont' ); ?>
				</select>
			</div>
			<div class="cpf_longform_align">
				<label class="block">Title Align:</label>

				<?php
				$alignment = array('left', 'right', 'center');

				foreach ( $alignment as $position ):

					$checked = ( $cpf_lf_meta['align'] == $position ) ? 'checked' : '';
					$name = 'cpf_longform_title_align_' . $position;
					?>
					<div class="checkbox_bt">
						<input type="radio" name="cpf_longform_title_align" id="<?php echo $name ?>"
						       value="<?php echo $position; ?>" <?php echo $checked; ?> />
						<label for="<?php echo $name ?>"><?php echo ucfirst( $position ) ?></label>
					</div>
				<?php endforeach; ?>

			</div>
			<div class="cpf_longform_position">
				<label for="cpf_longform_position">Title Position:</label>
				<input type="text" name="cpf_longform_position" id="cpf_longform_position" class="full"
				       value="<?php echo $cpf_lf_meta['position']; ?>" placeholder="10px 5% -4px 6%"/>
			</div>
		</div>
		<?php
	}

	/**
	 * Pull title from post meta
	 *
	 * @return mixed|string
	 */
	static function display_longform_title() {

		global $post;

		$longform_title = get_post_meta( $post->ID, 'cpf_longform_title', true );
		$featured_title = get_post_meta( $post->ID, 'featured_title', true );
		$popular_title  = get_post_meta( $post->ID, 'popular_title', true );

		if ( $longform_title ) {
			$post_title = $longform_title;
		} elseif ( $featured_title ) {
			$post_title = $featured_title;
		} elseif ( $popular_title ) {
			$post_title = $popular_title;
		} else {
			$post_title = get_the_title();
		}

		return $post_title;
	}

	/**
	 * Add font selection for title and body
	 *
	 * @return array
	 */
	public function font_options() {

		$fonts = array(
			// google fonts
			'Oswald',
			'Titillium Web',
			'Andale Mono',

			// websafe
			'Arial',
			'Arial Black',
			'Comic Sans MS',
			'Impact',
			'Tahoma',
			'Times New Roman',
			'Trebuchet MS',
			'Verdana',
			'Georgia',
			'Palatino',
			'Courier New'
		);

		return $fonts;
	}

	/**
	 * Build the select field options
	 *
	 * @param string $font_name Font name used to build the option tag
	 *
	 * @return string Concatenated list of font option tags
	 */
	public function build_font_options( $font_name = 'Oswald', $font_location = '' ) {

		$font_name = sanitize_title_with_dashes( $font_name );
		$options   = '';
		$selected  = '';

		$fonts = $this->font_options();

		// remove oswald font from use in body
		if ( 'bodyfont' == $font_location ) {
			unset( $fonts[0] );
		}

		foreach ( $fonts as $font ) {

			// reformat values to be comparable
			$font_alt = sanitize_title_with_dashes( $font );

			// if the value matches what is stored in post meta, generate the selected markup
			if ( $font_alt == $font_name ) {
				$selected = 'selected';
			} else {
				$selected = '';
			}

			$options .= sprintf( '<option value="%s" %s>%s</option>', $font, $selected, $font );
		}

		return $options;
	}

	/**
	 * Print longform styles
	 */
	static function print_styles () {

		if ( ! static::matches_custom_format( get_post_format() ) ) {
			return;
		}

		$frontend_styles = static::post_meta_array();
		echo '
		<style type="text/css" media="screen">
			.header-longform .article-details,
			.header-longform hgroup h1,
			.header-longform hgroup h2 {
				font-family: "' . $frontend_styles['tfont'] . '" !important;
			}

			body.single-format-longform .article-content,
			body.single-format-longform .article-content h5 {
				font-family: "' . $frontend_styles['bfont'] . '" !important;
			}
			body.single-format-longform .cpf-focus-title h5 span {
				background-color: ' . $frontend_styles['color'] . ' !important;
			}
			body.single-format-longform .article-content a {
				color: ' . $frontend_styles['color'] . ';
			}

			.header-longform .article-details {
				text-align: ' . $frontend_styles['align'] . ' !important;
			}
			.header-longform .article-details hgroup {
				margin: ' . $frontend_styles['position'] . ' !important;
			}
			body.single-format-longform .article-footer {
				max-width: 700px;
				margin-left: auto;
				margin-right: auto;
			}
		</style>
		';
	}

	/**
	 * Add scripts and styles
	 */
	static function enqueue_scripts() {
		if ( ! static::matches_custom_format( get_post_format() ) ) {
			return;
		}

		$fonts = static::post_meta_array();

		//styles
		wp_register_style( 'cpf-longform-google-title-font', 'https://fonts.googleapis.com/css?family=' . $fonts['tfont'], array(), CURRENT_THEME_VERSION );
		wp_enqueue_style( 'cpf-longform-google-title-font' );

		wp_register_style( 'cpf-longform-google-body-font', 'https://fonts.googleapis.com/css?family=' . $fonts['bfont'], array(), CURRENT_THEME_VERSION );
		wp_enqueue_style( 'cpf-longform-google-body-font' );

		//scripts
		wp_register_script( 'cpf-longform-script', get_stylesheet_directory_uri() . '/assets/js/longform.min.js', array('jquery'), CURRENT_THEME_VERSION, true );
		wp_enqueue_script( 'cpf-longform-script' );
	}

	/**
	 * Set post meta values
	 *
	 * @param $id ID of post to retrieve data from
	 *
	 * @return array Post meta data used to display info
	 */
	static function post_meta_array() {

		$id = get_the_ID();

		$meta_title    = get_post_meta( $id, 'cpf_longform_title', true );
		$meta_color    = get_post_meta( $id, 'cpf_longform_color', true );
		$meta_tfont    = get_post_meta( $id, 'cpf_longform_tfont', true );
		$meta_bfont    = get_post_meta( $id, 'cpf_longform_bfont', true );
		$meta_align    = get_post_meta( $id, 'cpf_longform_title_align', true );
		$meta_position = get_post_meta( $id, 'cpf_longform_position', true );

		$cpf_lf_meta = array(
			'title'       => $meta_title,
			'color'       => ( empty( $meta_color ) ) ? '#0433ff' : $meta_color,
			'tfont'       => ( empty( $meta_tfont ) ) ? 'Oswald'  : $meta_tfont,
			'bfont'       => ( empty( $meta_bfont ) ) ? 'Titillium Web'  : $meta_bfont,
			'align'       => ( empty( $meta_align ) ) ? 'left'    : $meta_align,
			'position'    => $meta_position
		);

		return $cpf_lf_meta;
	}
}
