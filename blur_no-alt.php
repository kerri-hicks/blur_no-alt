<?php
/**
* Plugin Name: Blur No-Alt
* Plugin URI: https://github.com/kerri-hicks/blur_no-alt
* Version: 0.92
* Requires at least: 5.0
* Author: Kerri Hicks
* Author URI: https://kerri.is/
* License: GPL v3 or later
* License URI: https://www.gnu.org/licenses/gpl-3.0.html
* Description: Blur images in the WordPress editor interface if they don't have alt text. Socially engineer your WordPress content developers to write alt text for images.
* Text Domain: blur-no-alt
* Update URI: false
*/

function blur_admin_theme_style() {
    wp_enqueue_style('blur-admin-theme', plugins_url('blur_no-alt.css', __FILE__)) ;
}
add_action('admin_enqueue_scripts', 'blur_admin_theme_style') ;


class BlurNoAltMessageDisplay {
	private $blur_no_alt_message_display_options;
	private $blur_no_alt_message_display_options_default = array(
		'show_or_hide_0' => 'Hide'
	);

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'blur_no_alt_message_display_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'blur_no_alt_message_display_page_init' ) );
		add_action('admin_enqueue_scripts', array( $this, 'blur_admin_theme_style_blur_message') );
	}

	public function blur_no_alt_message_display_add_plugin_page() {
		add_options_page(
			'Blur No-Alt Message Display', // page_title
			'Blur No-Alt Message Display', // menu_title
			'manage_options', // capability
			'blur-no-alt-message-display', // menu_slug
			array( $this, 'blur_no_alt_message_display_create_admin_page' ) // function
		);
	}

	public function blur_no_alt_message_display_create_admin_page() {
		$this->blur_no_alt_message_display_options = get_option( 'blur_no_alt_message_display_option_name' ); ?>

		<div class="wrap">
			<h2>Blur No-Alt Message Display</h2>
			<p>Show/hide the "Blurred images require alt text" banner across the top of editor pages</p>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'blur_no_alt_message_display_option_group' );
					do_settings_sections( 'blur-no-alt-message-display-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function blur_no_alt_message_display_page_init() {
		register_setting(
			'blur_no_alt_message_display_option_group', // option_group
			'blur_no_alt_message_display_option_name', // option_name
			array(
				'sanitize_callback' => array( $this, 'blur_no_alt_message_display_sanitize' ),
				'default' => $this->blur_no_alt_message_display_options_default,
			)
		);

		add_settings_section(
			'blur_no_alt_message_display_setting_section', // id
			'Settings', // title
			array( $this, 'blur_no_alt_message_display_section_info' ), // callback
			'blur-no-alt-message-display-admin' // page
		);

		add_settings_field(
			'show_or_hide_0', // id
			'Show or hide?', // title
			array( $this, 'show_or_hide_0_callback' ), // callback
			'blur-no-alt-message-display-admin', // page
			'blur_no_alt_message_display_setting_section' // section
		);
	}

	public function blur_no_alt_message_display_sanitize($input) {
		$sanitized = array();
		$allowed_values = array( 'Show', 'Hide' );
		if ( isset( $input['show_or_hide_0'] ) ) {
			$sanitized['show_or_hide_0'] = in_array( $input['show_or_hide_0'], $allowed_values ) ? $input['show_or_hide_0'] : null;
		}

		return $sanitized;
	}

	public function blur_no_alt_message_display_section_info() {
		
	}

	public function show_or_hide_0_callback() {
		?> <fieldset><?php $checked = ( isset( $this->blur_no_alt_message_display_options['show_or_hide_0'] ) && $this->blur_no_alt_message_display_options['show_or_hide_0'] === 'Show' ) ? 'checked' : '' ; ?>
		<label for="show_or_hide_0-0"><input type="radio" name="blur_no_alt_message_display_option_name[show_or_hide_0]" id="show_or_hide_0-0" value="Show" <?php echo $checked; ?>> Show</label><br>
		<?php $checked = ( isset( $this->blur_no_alt_message_display_options['show_or_hide_0'] ) && $this->blur_no_alt_message_display_options['show_or_hide_0'] === 'Hide' ) ? 'checked' : '' ; ?>
		<label for="show_or_hide_0-1"><input type="radio" name="blur_no_alt_message_display_option_name[show_or_hide_0]" id="show_or_hide_0-1" value="Hide" <?php echo $checked; ?>> Hide</label></fieldset> <?php
	}

	public function blur_admin_theme_style_blur_message() {
		$blur_no_alt_message_display_options = get_option(
			'blur_no_alt_message_display_option_name',
			$this->blur_no_alt_message_display_options_default
		); // Array of All Options

		$show_or_hide_0 = $blur_no_alt_message_display_options['show_or_hide_0']; // Show or hide?

		if ( $show_or_hide_0 !== "Hide" ) {
		    wp_enqueue_style('blur-admin-theme-blur-message', plugins_url('blur_no-alt_message.css', __FILE__)) ;
		}
	}

}

if ( is_admin() ) {
	$blur_no_alt_message_display = new BlurNoAltMessageDisplay();
}
