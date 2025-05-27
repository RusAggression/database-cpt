<?php

namespace RusAggression\DatabaseCPT;

use WP_Post;

final class Admin {
	/** @var self|null */
	private static $instance;

	public static function get_instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->init();
	}

	public function init(): void {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function admin_init(): void {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_filter( 'wp_insert_post_data', [ $this, 'wp_insert_post_data' ] );
		add_action( 'save_post', [ $this, 'save_post' ] );
		add_filter( 'manage_database_posts_columns', [ $this, 'manage_database_posts_columns' ] );
		add_action( 'manage_database_posts_custom_column', [ $this, 'manage_database_posts_custom_column' ], 10, 2 );
	}

	public function add_meta_boxes(): void {
		add_meta_box(
			'database_details',
			__( 'Database Details', 'database-cpt' ),
			[ $this, 'database_meta_box_callback' ],
			'database',
			'normal',
			'high'
		);
	}

	public function database_meta_box_callback( WP_Post $post ): void {
		$params = [
			'id'          => $post->ID,
			'description' => get_post_field( 'post_content', $post->ID, 'edit' ),
			'source_link' => sanitize_url( (string) get_post_meta( $post->ID, '_database_source_link', true ) ),
		];

		self::render( 'database-metabox', $params );
	}

	public function wp_insert_post_data( array $data ): array {
		if ( ! isset( $_POST['database_meta_box_nonce'] ) || ! is_string( $_POST['database_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['database_meta_box_nonce'] ), 'database_meta_box' ) ) {
			return $data;
		}

		if ( isset( $_POST['database_description'] ) && is_string( $_POST['database_description'] ) ) {
			$data['post_content'] = wp_kses_post( $_POST['database_description'] );
		}

		return $data;
	}

	public function save_post( int $post_id ): void {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
			! current_user_can( 'edit_post', $post_id ) ||
			! isset( $_POST['database_meta_box_nonce'] ) ||
			! is_string( $_POST['database_meta_box_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( $_POST['database_meta_box_nonce'] ), 'database_meta_box' )
		) {
			return;
		}

		if ( isset( $_POST['database_source_link'] ) && is_string( $_POST['database_source_link'] ) ) {
			update_post_meta( $post_id, '_database_source_link', sanitize_url( $_POST['database_source_link'] ) );
		}
	}

	public function manage_database_posts_columns( array $columns ): array {
		return [
			'cb'          => $columns['cb'],
			'title'       => $columns['title'],
			'description' => __( 'Description', 'database-cpt' ),
			'source_link' => __( 'Source Link', 'database-cpt' ),
			'date'        => $columns['date'],
		];
	}

	public function manage_database_posts_custom_column( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'description':
				echo wp_kses_post( get_post_field( 'post_content', $post_id ) );
				break;

			case 'source_link':
				$link = (string) get_post_meta( $post_id, '_database_source_link', true );
				if ( $link ) {
					printf( '<a href="%s" target="_blank" rel="noreferer noopener">%s</a>', esc_url( $link ), esc_html( $link ) );
				}

				break;
		}
	}

	/**
	 * @psalm-suppress UnusedParam
	 */
	private static function render( string $template, array $params = [] ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		/** @psalm-suppress UnresolvableInclude */
		require __DIR__ . '/../views/' . basename( $template ) . '.php'; // NOSONAR
	}
}
