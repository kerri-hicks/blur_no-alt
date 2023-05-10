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

/**
 * Plugin class
 */
class BlurNoAltMessageDisplay {
	private $plugin_version = '0.92';
	private $blur_no_alt_message_display_options;
	private $blur_no_alt_message_display_options_default = array(
		'show_or_hide_0' => 0,
		'blur_on_front'  => 0,
	);

	/**
	 * Register all actions class is instantiated
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'blur_no_alt_message_display_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'blur_no_alt_message_display_page_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'blur_admin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'blur_front_end_styles' ) );
	}

	/**
	 * Register settings page
	 */
	public function blur_no_alt_message_display_add_plugin_page() {
		add_options_page(
			__( 'Blur No-Alt Settings', 'blur-no-alt' ),
			__( 'Blur No-Alt', 'blur-no-alt' ),
			'manage_options',
			'blur-no-alt-message-display',
			array( $this, 'blur_no_alt_message_display_create_admin_page' ),
		);
	}

	/**
	 * Create output for admin settings page
	 */
	public function blur_no_alt_message_display_create_admin_page() {
		$this->blur_no_alt_message_display_options = get_option( 'blur_no_alt_message_display_option_name', $this->blur_no_alt_message_display_options_default ); ?>

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
		<?php
	}

	/**
	 * Register Settings page, sections, and settings
	 */
	public function blur_no_alt_message_display_page_init() {
		register_setting(
			'blur_no_alt_message_display_option_group',
			'blur_no_alt_message_display_option_name',
			array(
				'description'       => 'All settings for the Blur No-Alt plugin',
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'blur_no_alt_message_display_sanitize' ),
				'default'           => $this->blur_no_alt_message_display_options_default,
			)
		);

		add_settings_section(
			'blur_no_alt_message_display_setting_section',
			__( 'Editor Settings', 'blur-no-alt' ),
			false,
			'blur-no-alt-message-display-admin'
		);

		add_settings_section(
			'blur_no_alt_front_end_blur_setting_section',
			__( 'Front-end Blur Settings', 'blur-no-alt' ),
			array( $this, 'blur_no_alt_front_end_section_callback' ),
			'blur-no-alt-message-display-admin',
		);

		add_settings_field(
			'show_or_hide_0',
			__( 'Message explaining blurred images at top of editor page', 'blur-no-alt' ),
			array( $this, 'show_or_hide_setting_callback' ),
			'blur-no-alt-message-display-admin',
			'blur_no_alt_message_display_setting_section'
		);

		add_settings_field(
			'blur_no_alt_on_front',
			__( 'Blur images for logged-in users. EXPERIMENTAL!', 'blur-no-alt' ),
			array( $this, 'blur_no_alt_on_front_setting_callback' ),
			'blur-no-alt-message-display-admin',
			'blur_no_alt_front_end_blur_setting_section'
		);
	}

	/**
	 * Sanitize all plugin options
	 *
	 * @param  arr $input Unsanitized options to sanitize.
	 */
	public function blur_no_alt_message_display_sanitize( $input ) {
		$sanitized = array();

		if ( isset( $input['show_or_hide_0'] ) ) {
			$sanitized['show_or_hide_0'] = 'Show' === $input['show_or_hide_0'] ? 'Show' : false;
		}

		if ( isset( $input['blur_on_front'] ) ) {
			$sanitized['blur_on_front'] = intval( $input['blur_on_front'] );
		}

		return $sanitized;
	}

	/**
	 * Output help text about front-end blur capabilities filter
	 */
	public function blur_no_alt_front_end_section_callback() {
		echo '<p>' . wp_kses_post( __( 'Applies to all users with <code>edit_pages</code> capability. Customize role with <code>blur_no_alt_front_end_blur_capability</code> filter.' ) ) . '</p>';
	}

	/**
	 * Outputs radio buttons for
	 */
	public function show_or_hide_setting_callback() {
		$show_is_checked = (
			isset( $this->blur_no_alt_message_display_options['show_or_hide_0'] ) &&
			'Show' === $this->blur_no_alt_message_display_options['show_or_hide_0']
		);
		?>
		<fieldset>
			<input type="checkbox" name="blur_no_alt_message_display_option_name[show_or_hide_0]" id="show_or_hide_0-0" value="Show" <?php echo $show_is_checked ? 'checked' : ''; ?>>
			<label for="show_or_hide_0-0">
				<?php esc_html_e( 'Show informational message to editors', 'blur-no-alt' ); ?>
			</label>
		</fieldset>
		<?php
	}

	/**
	 * Outputs checkbox for blur_on_front setting
	 */
	public function blur_no_alt_on_front_setting_callback() {
		$checked = (
			isset( $this->blur_no_alt_message_display_options['blur_on_front'] ) &&
			1 === $this->blur_no_alt_message_display_options['blur_on_front']
		) ? 'checked' : '';
		?>
		<input type="checkbox" name="blur_no_alt_message_display_option_name[blur_on_front]" id="blur_on_front" value="1" <?php echo esc_attr( $checked ); ?>>
		<label for="blur_on_front">
			<?php esc_html_e( 'Blur images on front-end for logged-in users', 'blur-no-alt' ); ?>
		</label>
		<?php
	}

	/**
	 * Enqueues admin stylesheet to blur images
	 */
	public function blur_admin_styles() {
		/* Admin styles to blur images */
		wp_enqueue_style(
			'blur-admin-theme',
			plugins_url( 'css/blur-no-alt-editor.css', __FILE__ ),
			array(),
			$this->plugin_version,
		);

		/* Optional message at top of block editor */
		$blur_no_alt_message_display_options = get_option(
			'blur_no_alt_message_display_option_name',
			$this->blur_no_alt_message_display_options_default
		);

		$show_or_hide_0 = isset( $blur_no_alt_message_display_options['show_or_hide_0'] ) ? $blur_no_alt_message_display_options['show_or_hide_0'] : false;

		if ( 'Show' === $show_or_hide_0 ) {
			wp_enqueue_style(
				'blur-admin-theme-blur-message',
				plugins_url( 'css/blur-no-alt-message.css', __FILE__ ),
				array(),
				$this->plugin_version
			);
		}
	}

	/**
	 * Enqueues front-end stylesheet
	 */
	public function blur_front_end_styles() {
		$plugin_options = get_option(
			'blur_no_alt_message_display_option_name',
			$this->blur_no_alt_message_display_options_default
		);

		$front_end_cap = apply_filters( 'blur_no_alt_front_end_blur_capability', 'edit_pages' );

		if ( isset( $plugin_options['blur_on_front'] ) && $plugin_options['blur_on_front'] && current_user_can( esc_attr( $front_end_cap ) ) ) {
			wp_enqueue_style(
				'blur-front-styles',
				plugins_url( 'css/blur-no-alt-front.css', __FILE__ ),
				array(),
				$this->plugin_version,
			);
		}
	}

}

$blur_no_alt_message_display = new BlurNoAltMessageDisplay();
