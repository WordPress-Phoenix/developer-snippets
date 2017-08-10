<?php
/**
 * Class Custom_Event_Preview_Format
 */
class Custom_Event_Preview_Format extends Abstract_Post_Format {
	static $slug = 'post-format-event-preview';
	static $name = 'Event-Preview';
	static $option_name = 'event_preview_status';

	/**
	 * Add scripts and styles
	 */
	static function enqueue_scripts() {
		if ( ! static::matches_custom_format( get_post_format() ) ) {
			return;
		}

		//scripts
		wp_register_script( 'custom-event-preview-script', get_stylesheet_directory_uri() . '/assets/js/cpf-event-preview.min.js', array( 'jquery' ), CURRENT_THEME_VERSION, true );
		wp_enqueue_script( 'custom-event-preview-script' );
		wp_localize_script( 'custom-event-preview-script', 'evtPreviewData', self::event_preview_data() );
	}

	static function event_preview_data() {
		return array(
			'tab' => 'roundone',
			'passthrough' => array_key_exists( 'tab', $_GET ) ? $_GET['tab'] : '',
		);
	}
}
