<?php

namespace RusAggression\DatabaseCPT;

final class REST {
	/** @var self|null */
	private static $instance;

	public static function get_instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->rest_api_init();
	}

	public function rest_api_init(): void {
		register_rest_field( 'database', 'content', [
			'get_callback' => [ $this, 'content_callback' ],
		] );

		register_rest_field( 'database', 'source_link', [
			'get_callback' => [ $this, 'source_link_callback' ],
		] );
	}

	public function content_callback( array $post ): string {
		return (string) apply_filters( 'the_content', $post['content']['raw'] );
	}

	public function source_link_callback( array $post ): string {
		return (string) get_post_meta( $post['id'], '_database_source_link', true );
	}
}
