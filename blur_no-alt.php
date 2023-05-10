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

define( 'BLUR_NO_ALT_VERSION', '0.92');

function blur_admin_style() {
    wp_enqueue_style(
    	'blur-admin-theme',
    	plugins_url('css/blur-no-alt-editor.css', __FILE__),
    	array(),
    	BLUR_NO_ALT_VERSION,
    );
}
add_action('admin_enqueue_scripts', 'blur_admin_style');

function blur_front_end_style() {
	$plugin_options = get_option( 'blur_no_alt_message_display_option_name' );
	$front_end_display = isset( $plugin_options['blur_on_front'] ) && (bool) $plugin_options['blur_on_front'];

	$front_end_cap = apply_filters( 'blur_no_alt_front_end_blur_capability', 'edit_pages' );

	if( $front_end_display && current_user_can( esc_attr( $front_end_cap ) ) ) {
		wp_enqueue_style(
			'blur-front-styles',
			plugins_url( 'css/blur-no-alt-front.css', __FILE__ ),
			array(),
			BLUR_NO_ALT_VERSION,
		);
	}
}
add_action( 'wp_enqueue_scripts', 'blur_front_end_style' );


class BlurNoAltMessageDisplay {
	private $blur_no_alt_message_display_options;
	private $blur_no_alt_message_display_options_default = array(
		'show_or_hide_0' => 'Hide',
		'blur_on_front' => 0,
	);

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'blur_no_alt_message_display_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'blur_no_alt_message_display_page_init' ) );
		add_action('admin_enqueue_scripts', array( $this, 'blur_admin_theme_style_blur_message') );
	}

	public function blur_no_alt_message_display_add_plugin_page() {
		add_options_page(
			__( 'Blur No-Alt Settings', 'blur-no-alt' ), // page_title
			__( 'Blur No-Alt', 'blur-no-alt' ), // menu_title
			'manage_options', // capability
			'blur-no-alt-message-display', // menu_slug
			array( $this, 'blur_no_alt_message_display_create_admin_page' ) // function
		);
	}

	public function blur_no_alt_message_display_create_admin_page() {
		$this->blur_no_alt_message_display_options = get_option( 'blur_no_alt_message_display_option_name' ); ?>

		<div class="wrap">
			<h2><?php esc_html_e( 'Blur No-Alt Settings', 'blur-no-alt' ); ?></h2>
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
				'description' => 'All settings for the Blur No-Alt plugin',
				'type' => 'array',
				'sanitize_callback' => array( $this, 'blur_no_alt_message_display_sanitize' ),
				'default' => $this->blur_no_alt_message_display_options_default,
			)
		);

		add_settings_section(
			'blur_no_alt_message_display_setting_section', // id
			__( 'Editor Settings', 'blur-no-alt' ), // title
			false, // callback
			'blur-no-alt-message-display-admin' // page
		);

		add_settings_section(
			'blur_no_alt_front_end_blur_setting_section', // id
			__( 'Front-end Blur Settings', 'blur-no-alt' ), // title
			array( $this, 'blur_no_alt_front_end_section_callback' ), // callback
			'blur-no-alt-message-display-admin', // page
		);

		add_settings_field(
			'show_or_hide_0', // id
			__( 'Message explaining blurred images at top of editor page', 'blur-no-alt' ), // title
			array( $this, 'show_or_hide_0_callback' ), // callback
			'blur-no-alt-message-display-admin', // page
			'blur_no_alt_message_display_setting_section' // section
		);

		add_settings_field(
			'blur_no_alt_on_front', // id
			__( 'Blur images for logged-in users. EXPERIMENTAL!', 'blur-no-alt' ), // title
			array( $this, 'blur_no_alt_on_front_setting_callback' ), // callback
			'blur-no-alt-message-display-admin', // page
			'blur_no_alt_front_end_blur_setting_section' // section
		);
	}

	public function blur_no_alt_message_display_sanitize($input) {
		$sanitized = array();
		$allowed_values = array( 'Show', 'Hide' );

		if ( isset( $input['show_or_hide_0'] ) ) {
			$sanitized['show_or_hide_0'] = in_array( $input['show_or_hide_0'], $allowed_values ) ? $input['show_or_hide_0'] : null;
		}

		if( isset( $input['blur_on_front'] ) ) {
			$sanitized['blur_on_front'] = intval( $input['blur_on_front'] );
		}

		return $sanitized;
	}

	public function blur_no_alt_front_end_section_callback() {
		echo '<p>' . __( 'Applies to all users with <code>edit_pages</code> capability. Customize role with <code>blur_no_alt_front_end_blur_capability</code> filter.' ) . '</p>';
	}

	public function show_or_hide_0_callback() {
		$option = (
			isset( $this->blur_no_alt_message_display_options['show_or_hide_0'] ) &&
			$this->blur_no_alt_message_display_options['show_or_hide_0'] === 'Show'
		) ? 'show' : 'hide' ;
		?>
		<fieldset>
			<input type="radio" name="blur_no_alt_message_display_option_name[show_or_hide_0]" id="show_or_hide_0-0" value="Show" <?php echo $option === 'show' ? 'checked' : ''; ?>>
			<label for="show_or_hide_0-0">
				<?php esc_html_e( 'Show', 'blur-no-alt' ); ?>
			</label>
			<br>
			<input type="radio" name="blur_no_alt_message_display_option_name[show_or_hide_0]" id="show_or_hide_0-1" value="Hide" <?php echo $option === 'hide' ? 'checked' : ''; ?>>
			<label for="show_or_hide_0-1">
				<?php esc_html_e( 'Hide', 'blur-no-alt' ); ?>
			</label>
		</fieldset>
		<?php
	}

	public function blur_no_alt_on_front_setting_callback() {
		$checked = (
			isset( $this->blur_no_alt_message_display_options['blur_on_front'] ) &&
			$this->blur_no_alt_message_display_options['blur_on_front'] === 1
		) ? 'checked' : '' ;
		?>
		<input type="checkbox" name="blur_no_alt_message_display_option_name[blur_on_front]" id="blur_on_front" value="1" <?php echo $checked; ?>>
		<label for="blur_on_front">
			<?php esc_html_e( 'Blur images on front-end for logged-in users', 'blur-no-alt' ); ?>
		</label>
		<?php
	}

	public function blur_admin_theme_style_blur_message() {
		$blur_no_alt_message_display_options = get_option(
			'blur_no_alt_message_display_option_name',
			$this->blur_no_alt_message_display_options_default
		); // Array of All Options

		$show_or_hide_0 = $blur_no_alt_message_display_options['show_or_hide_0']; // Show or hide?

		if ( $show_or_hide_0 !== "Hide" ) {
		    wp_enqueue_style('blur-admin-theme-blur-message', plugins_url('css/blur-no-alt-message.css', __FILE__)) ;
		}
	}

}

if ( is_admin() ) {
	$blur_no_alt_message_display = new BlurNoAltMessageDisplay();
}
