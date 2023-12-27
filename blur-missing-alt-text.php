<?php
/**
 * Blur Missing Alt Text
 *
 * @package     blur-missing-alt-text
 * @author      Mark Root-Wiley
 * @license     GPL-3.0+
 *
 * Plugin Name: Blur Missing Alt Text
 * Plugin URI: https://github.com/kerri-hicks/blur_no-alt
 * Version: 1.0.0
 * Requires at least: 5.0
 * Author: Mark Root-Wiley (forked from Blur No-Alt by Kerri Hicks)
 * Author URI: https://MRWweb.com
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Description: Socially engineer your WordPress content to encourage alternative text! Blur images without alt text in the WordPress editor and on the front end for logged-in users.
 * Text Domain: blur-missing-alt-text
 * Update URI: false
 */

/**
 * Plugin class
 */
class BMAT_Plugin {
	/**
	 * Plugin Version
	 *
	 * @var string
	 */
	private $plugin_version = '1.0.0';

	/**
	 * Used to store plugin options?
	 *
	 * @var array
	 */
	private $message_display_options;

	/**
	 * Default options for the plugin when not or partially defined
	 *
	 * @var array
	 */
	private $message_display_options_default = array(
		'show_or_hide' => 0,
		'blur_on_front'  => 0,
	);

	/**
	 * Register all actions class is instantiated
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'message_display_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'message_display_page_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'blur_admin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'blur_front_end_styles' ) );
	}

	/**
	 * Register settings page
	 */
	public function message_display_add_plugin_page() {
		add_options_page(
			// page title
			esc_html__( 'Blur No-Alt Settings', 'blur-missing-alt-text' ),
			// menu title
			esc_html__( 'Blur No-Alt', 'blur-missing-alt-text' ),
			// capability required to edit settings
			'manage_options',
			// slug of options page
			'blur-no-alt-message-display',
			// callback to create output for options page
			array( $this, 'message_display_create_admin_page' ),
		);
	}

	/**
	 * Create output for admin settings page
	 */
	public function message_display_create_admin_page() {
		$this->message_display_options = get_option( 'message_display_option_name', $this->message_display_options_default ); ?>

		<div class="wrap">
			<h2><?php esc_html_e( 'Blur No-Alt Settings', 'blur-missing-alt-text' ); ?></h2>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'message_display_option_group' );
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
	public function message_display_page_init() {
		register_setting(
			// option group
			'message_display_option_group',
			// option name
			'message_display_option_name',
			array(
				'description'       => esc_html_x( 'All settings for the Blur No-Alt plugin', 'Description property of register_setting function', 'blur-missing-alt-text' ),
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'message_display_sanitize' ),
				'default'           => $this->message_display_options_default,
			)
		);

		add_settings_section(
			// section id
			'message_display_setting_section',
			// section title
			esc_html__( 'Editor Settings', 'blur-missing-alt-text' ),
			// no callback because no content between title and setting
			false,
			// slug of settings page to display section
			'blur-no-alt-message-display-admin'
		);

		add_settings_section(
			// section id
			'front_end_blur_setting_section',
			// section title
			esc_html__( 'Front-end Settings', 'blur-missing-alt-text' ),
			// callback to output info about capabilities filter
			array( $this, 'front_end_section_callback' ),
			// slug of settings page to display section
			'blur-no-alt-message-display-admin',
		);

		add_settings_field(
			// setting id
			'show_or_hide',
			// setting title showed in adjacent table cell
			esc_html__( 'Message above Block Editor title explaining blurred images', 'blur-missing-alt-text' ),
			// callback to output checkbox
			array( $this, 'show_or_hide_setting_callback' ),
			// slug of settings page to display setting
			'blur-no-alt-message-display-admin',
			// section id to display setting
			'message_display_setting_section'
		);

		add_settings_field(
			// setting id
			'on_front',
			// setting title showed in adjacent table cell
			esc_html__( 'EXPERIMENTAL! Front-end blurred images', 'blur-missing-alt-text' ),
			// callback to output checkbox
			array( $this, 'on_front_setting_callback' ),
			// slug of settings page to display setting
			'blur-no-alt-message-display-admin',
			// section id to display setting
			'front_end_blur_setting_section'
		);
	}

	/**
	 * Sanitize all plugin options
	 *
	 * @param  arr $input Unsanitized options to sanitize.
	 */
	public function message_display_sanitize( $input ) {
		$sanitized = array();

		if ( isset( $input['show_or_hide'] ) ) {
			$sanitized['show_or_hide'] = 'Show' === $input['show_or_hide'] ? 'Show' : false;
		}

		if ( isset( $input['blur_on_front'] ) ) {
			$sanitized['blur_on_front'] = intval( $input['blur_on_front'] );
		}

		return $sanitized;
	}

	/**
	 * Output help text about front-end blur capabilities filter
	 */
	public function front_end_section_callback() {
		echo '<p>' . wp_kses(
				__( 'When enabled, this option applies to all users with <code>edit_pages</code> capability. Customize capability with <code>bmat_front_end_capability</code> filter.', 'blur-missing-alt-text' ),
				array( 'code' => array() )
			) . '</p>';
	}

	/**
	 * Outputs radio buttons for
	 */
	public function show_or_hide_setting_callback() {
		$show_is_checked = (
			isset( $this->message_display_options['show_or_hide'] ) &&
			'Show' === $this->message_display_options['show_or_hide']
		);
		?>
		<fieldset>
			<input type="checkbox" name="message_display_option_name[show_or_hide]" id="show_or_hide-0" value="Show" <?php echo $show_is_checked ? 'checked' : ''; ?>>
			<label for="show_or_hide-0">
				<?php esc_html_e( 'Show informational message to editors', 'blur-missing-alt-text' ); ?>
			</label>
		</fieldset>
		<?php
	}

	/**
	 * Outputs checkbox for blur_on_front setting
	 */
	public function on_front_setting_callback() {
		$checked = (
			isset( $this->message_display_options['blur_on_front'] ) &&
			1 === $this->message_display_options['blur_on_front']
		) ? 'checked' : '';
		?>
		<input type="checkbox" name="message_display_option_name[blur_on_front]" id="blur_on_front" value="1" <?php echo esc_attr( $checked ); ?>>
		<label for="blur_on_front">
			<?php esc_html_e( 'Blur images on front-end for logged-in users', 'blur-missing-alt-text' ); ?>
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
			plugins_url( 'css/blur_no-alt_editor.css', __FILE__ ),
			array(),
			$this->plugin_version,
		);

		/* Optional message at top of block editor */
		$message_display_options = get_option(
			'message_display_option_name',
			$this->message_display_options_default
		);

		$show_or_hide = $message_display_options['show_or_hide'];

		if ( 'Show' === $show_or_hide ) {
			wp_enqueue_style(
				'blur-admin-theme-blur-message',
				plugins_url( 'css/blur_no-alt_message.css', __FILE__ ),
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
			'message_display_option_name',
			$this->message_display_options_default
		);

		$front_end_cap = apply_filters( 'bmat_front_end_capability', 'edit_pages' );

		if ( isset( $plugin_options['blur_on_front'] ) && $plugin_options['blur_on_front'] && current_user_can( esc_attr( $front_end_cap ) ) ) {
			wp_enqueue_style(
				'blur-front-styles',
				plugins_url( 'css/blur_no-alt_front.css', __FILE__ ),
				array(),
				$this->plugin_version,
			);
		}
	}

}

$bmat_plugin = new BMAT_Plugin();
