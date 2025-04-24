<?php
defined( 'ABSPATH' ) || exit;

/**
 * @psalm-var array{ id: int, description: string, source_link: string } $params
 */

wp_nonce_field( 'database_meta_box', 'database_meta_box_nonce' );
?>
<p>
	<?php
	wp_editor(
		$params['description'],
		'database_description',
		[
			'media_buttons' => false,
			'textarea_rows' => 10,
			'teeny'         => true,
			'quicktags'     => true,
		]
	);
	?>
</p>
<p>
	<label for="database_source_link"><?php _e( 'Source Link:', 'database-cpt' ); ?></label><br/>
	<input type="url" id="database_source_link" name="database_source_link" value="<?= esc_url( $params['source_link'] ); ?>" style="width: 100%;"/>
</p>
