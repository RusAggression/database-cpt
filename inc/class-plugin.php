<?php

namespace RusAggression\DatabaseCPT;

final class Plugin {
	/** @var self|null */
	private static $instance;

	public static function get_instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$plugin = dirname( __DIR__ ) . '/index.php';
		register_activation_hook( $plugin, [ $this, 'activate' ] );
		register_deactivation_hook( $plugin, [ $this, 'deactivate' ] );

		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
		add_action( 'init', [ $this, 'init' ] );

		if ( is_admin() ) {
			Admin::get_instance();
		}
	}

	public function activate(): void {
		DatabasePostType::register();
		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
		flush_rewrite_rules();
	}

	public function deactivate(): void {
		DatabasePostType::unregister();
		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
		flush_rewrite_rules();
	}

	public function plugins_loaded(): void {
		load_plugin_textdomain( 'database-cpt', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	public function init(): void {
		add_image_size( 'database-thumbnail', 350, 350, true );

		add_action( 'rest_api_init', [ REST::class, 'get_instance' ] );
		DatabasePostType::register();
	}
}
