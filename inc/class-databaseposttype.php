<?php

namespace RusAggression\DatabaseCPT;

final class DatabasePostType {
	public static function register(): void {
		if ( post_type_exists( 'database' ) ) {
			return;
		}

		$labels = [
			'name'               => _x( 'Databases', 'post type general name', 'database-cpt' ),
			'singular_name'      => _x( 'Database', 'post type singular name', 'database-cpt' ),
			'menu_name'          => _x( 'Databases', 'admin menu', 'database-cpt' ),
			'add_new'            => _x( 'Add New', 'database', 'database-cpt' ),
			'add_new_item'       => __( 'Add New Database', 'database-cpt' ),
			'edit_item'          => __( 'Edit Database', 'database-cpt' ),
			'new_item'           => __( 'New Database', 'database-cpt' ),
			'view_item'          => __( 'View Database', 'database-cpt' ),
			'search_items'       => __( 'Search Databases', 'database-cpt' ),
			'not_found'          => __( 'No databases found', 'database-cpt' ),
			'not_found_in_trash' => __( 'No databases found in trash', 'database-cpt' ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'has_archive'        => true,
			'publicly_queryable' => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'database' ],
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'supports'           => [ 'title', 'thumbnail' ],
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-database',
			'show_in_rest'       => true,
			'rest_base'          => 'databases',
		];

		register_post_type( 'database', $args );
	}

	public static function unregister(): void {
		unregister_post_type( 'database' );
	}
}
