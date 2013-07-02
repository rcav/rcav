<?php
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
		exit();

	global $wpdb;

	$form_table = $wpdb->prefix . 'vfb_pro_fields';
	$fields_table = $wpdb->prefix . 'vfb_pro_forms';
	$entries_table = $wpdb->prefix . 'vfb_pro_entries';

	$wpdb->query( "DROP TABLE IF EXISTS $form_table" );
	$wpdb->query( "DROP TABLE IF EXISTS $fields_table" );
	$wpdb->query( "DROP TABLE IF EXISTS $entries_table" );

	delete_option( 'vfb_pro_db_version' );
	delete_option( 'visual-form-builder-screen-options' );
	delete_option( 'vfb_db_upgrade' );
	delete_option( 'vfb_ignore_notice' );
	delete_option( 'vfb_dashboard_widget_options' );

	$wpdb->query( "DELETE FROM " . $wpdb->prefix . "usermeta WHERE meta_key IN ( 'vfb-form-settings', 'vfb_entries_per_page', 'vfb_forms_per_page', 'vfb-form-order', 'managevisual-form-builder-pro_page_vfb-entriescolumnshidden' )" );

	$role = get_role( 'administrator' );

	if ( !empty( $role ) ) {
		$caps = array(
			'administrator' => array(
				'vfb_create_forms',
				'vfb_edit_forms',
				'vfb_copy_forms',
				'vfb_delete_forms',
				'vfb_import_forms',
				'vfb_export_forms',
				'vfb_view_entries',
				'vfb_edit_entries',
				'vfb_delete_entries',
				'vfb_edit_email_design',
				'vfb_view_analytics'
			),
			'editor' => array(
				'vfb_view_entries',
				'vfb_edit_entries',
				'vfb_delete_entries',
				'vfb_view_analytics'
			)
		);

		// Remove caps from the administrator role
		if ( !empty( $role ) ) {
			foreach ( $caps['administrator'] as $cap ) {
				$role->remove_cap( $cap );
			}
		}

		// Remove caps from the editor role
		$role = get_role( 'editor' );
		if ( !empty( $role ) ) {
			foreach ( $caps['editor'] as $cap ) {
				$role->remove_cap( $cap );
			}
		}
	}
