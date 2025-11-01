<?php namespace EmailLog\Core\UI\Component;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Enhance Admin UI and add links about EmailLog in the following places.
 * - Plugin List page.
 * - Footer for all EmailLog pages.
 *
 * @since 2.0.0
 */
class AdminUIEnhancer {

	/**
	 * Plugin file name.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * Plugin basename.
	 *
	 * @var string
	 */
	protected $plugin_basename;

	/**
	 * Initialize the component and store the plugin basename.
	 *
	 * @param string|null $file Plugin file.
	 */
	public function __construct( $file = null ) {
		if ( null === $file ) {
			$email_log = email_log();
			$file      = $email_log->get_plugin_file();
		}

		$this->plugin_file     = $file;
		$this->plugin_basename = plugin_basename( $file );
	}

	/**
	 * Setup hooks.
	 *
	 *
	 */
	public function load() {
		add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'insert_view_logs_link' ) );

		add_action( 'el_admin_footer', array( $this, 'hook_footer_links' ) );
	}

	/**
	 * Add link to 'View logs' page in plugin listing page.
	 *
	 * @since 2.3.0 Added Settings link.
	 *
	 * @param array $links List of links.
	 *
	 * @return array Modified list of links.
	 */
	public function insert_view_logs_link( $links ) {
		$view_logs_link = '<a href="admin.php?page=email-log">' . __( 'View Logs', 'email-log' ) . '</a>';
		$pro_link  = '<a href="admin.php?page=email-log#open-pro-dialog"><b>' . __( 'Get PRO', 'email-log' ) . '</b></a>';

		array_unshift( $links, $view_logs_link );
    $links[] = $pro_link;

		return $links;
	}

	/**
	 * Hook Footer links.
	 */
	public function hook_footer_links() {
		//add_action( 'in_admin_footer', array( $this, 'add_credit_links' ) );
	}

	/**
	 * Adds Footer links.
	 *
	 * @since Genesis
	 * @see   Function relied on
	 * @link  http://striderweb.com/nerdaphernalia/2008/06/give-your-wordpress-plugin-credit/
	 */
	public function add_credit_links() {
		$plugin_data = get_plugin_data( $this->plugin_file );
		\EmailLog\Core\EmailLog::wp_kses_wf(sprintf(
			'%1$s ' . __( 'plugin', 'email-log' ) . ' | ' . __( 'Version', 'email-log' ) . ' %2$s | ' . __( 'by', 'email-log' ) . ' %3$s <br />',
			$plugin_data['Title'],
			$plugin_data['Version'],
			$plugin_data['Author']
        ));
	}
}
