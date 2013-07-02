<?php
/**
 * Class that builds our Import page
 *
 * @since 1.7
 */
class VisualFormBuilder_Pro_Import {

	protected $id, $version,
			  $max_version = '2.3',
			  $existing_forms = array(),
			  $forms = array(),
			  $fields = array(),
			  $entries = array();

	public function __construct(){
		global $wpdb;

		// Setup global database table names
		$this->field_table_name 	= $wpdb->prefix . 'vfb_pro_fields';
		$this->form_table_name 		= $wpdb->prefix . 'vfb_pro_forms';
		$this->entries_table_name 	= $wpdb->prefix . 'vfb_pro_entries';
		$this->design_table_name 	= $wpdb->prefix . 'vfb_pro_form_design';
		$this->payment_table_name 	= $wpdb->prefix . 'vfb_pro_payments';
	}

	/**
	 * Display the import form
	 *
	 * @since 1.7
	 *
	 */
	public function display(){
		$this->dispatch();

        wp_import_upload_form( 'admin.php?page=vfb-import&amp;import=vfb&amp;step=1' );
	}

	/**
	 * Manages the separate stages of the XML import process
	 *
	 * @since 1.7
	 *
	 */
	public function dispatch() {

		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];

		switch ( $step ) :
			case 0:
				echo sprintf( '<p>%s</p>', __( 'Select a Visual Form Builder Pro backup file (.xml), then click Upload file and import.', 'visual-form-builder-pro' ) );
			break;

			case 1:
				check_admin_referer( 'import-upload' );
				if ( $this->handle_upload() ) :
					$file = get_attached_file( $this->id );
					set_time_limit(0);
					$this->import( $file );
				endif;
			break;
		endswitch;
	}

	/**
	 * The main controller for the actual import stage.
	 *
	 * @since 1.7
	 * @param string $file Path to the XML file for importing
	 */
	public function import( $file ) {
		$this->import_start( $file );

		wp_suspend_cache_invalidation( true );
		$this->process_forms();
		$this->process_fields();
		$this->process_entries();
		$this->process_form_designs();
		$this->process_payments();
		wp_suspend_cache_invalidation( false );

		$this->import_end();
	}

	/**
	 * Parses the XML file and prepares us for the task of processing parsed data
	 *
	 * @since 1.7
	 * @param string $file Path to the XML file for importing
	 */
	public function import_start( $file ) {

		if ( ! is_file($file) ) :
			echo sprintf( '<p><strong>%1$s</strong><br>%2$s</p>',
				__( 'Sorry, there has been an error.', 'visual-form-builder-pro' ),
				__( 'The file does not exist, please try again.', 'visual-form-builder-pro' )
			);

			die();
		endif;

		$import_data = $this->parse( $file );

		if ( is_wp_error( $import_data ) ) :
			echo sprintf( '<p><strong>%1$s</strong><br>%2$s</p>',
				__( 'Sorry, there has been an error.', 'visual-form-builder-pro' ),
				esc_html( $import_data->get_error_message() )
			);

			die();
		endif;

		$this->version 	= $import_data['version'];
		$this->forms 	= $import_data['forms'];
		$this->fields 	= $import_data['fields'];
		$this->entries 	= $import_data['entries'];
		$this->designs	= $import_data['designs'];
		$this->payments	= $import_data['payments'];
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 *
	 * @since 1.7
	 *
	 */
	public function import_end() {
		wp_import_cleanup( $this->id );

		wp_cache_flush();

		echo sprintf( '<p>%1$s<a href="%2$s">%3$s</a></p>',
			__( 'All done.', 'visual-form-builder-pro' ),
			admin_url( 'admin.php?page=visual-form-builder-pro' ),
			__( 'View Forms', 'visual-form-builder-pro' )
		);
	}

	/**
	 * Process the forms from the XML import
	 *
	 * @since 1.7
	 *
	 */
	public function process_forms() {
		global $wpdb;

		if ( empty( $this->forms ) )
			return;

		echo sprintf( '<p>%s</p>', __( 'Importing forms...', 'visual-form-builder-pro' ) );

		foreach ( $this->forms as $form ) :
			$data = array(
				'form_id' 						=> $form['form_id'],
				'form_key' 						=> $form['form_key'],
				'form_title' 					=> $form['form_title'],
				'form_email_subject' 			=> $form['form_email_subject'],
				'form_email_to' 				=> $form['form_email_to'],
				'form_email_from' 				=> $form['form_email_from'],
				'form_email_from_name' 			=> $form['form_email_from_name'],
				'form_email_from_override' 		=> $form['form_email_from_override'],
				'form_email_from_name_override' => $form['form_email_from_name_override'],
				'form_success_type' 			=> $form['form_success_type'],
				'form_success_message' 			=> $form['form_success_message'],
				'form_notification_setting' 	=> $form['form_notification_setting'],
				'form_notification_email_name' 	=> $form['form_notification_email_name'],
				'form_notification_email_from' 	=> $form['form_notification_email_from'],
				'form_notification_email' 		=> $form['form_notification_email'],
				'form_notification_subject' 	=> $form['form_notification_subject'],
				'form_notification_message' 	=> $form['form_notification_message'],
				'form_notification_entry' 		=> $form['form_notification_entry'],
				'form_email_design' 			=> $form['form_email_design'],
				'form_label_alignment' 			=> $form['form_label_alignment'],
				'form_verification' 			=> $form['form_verification'],
				'form_entries_allowed' 			=> $form['form_entries_allowed'],
				'form_entries_schedule' 		=> $form['form_entries_schedule'],
				'form_unique_entry' 			=> $form['form_unique_entry']
			);

			$form_id = $this->form_exists( $form['form_id'] );

			// If the form ID is a duplicate, it can't be used
			if ( $form_id ) :
				$data['form_id'] = '';
				$this->existing_forms[ $form['form_id'] ] = '';

				echo sprintf( '<p><strong>%1$s :</strong> %2$s</p>',
					stripslashes( $form['form_title'] ),
					__( 'Form ID already exists. Assigning a new form ID.', 'visual-form-builder-pro' )
				);
			endif;

			$insert = $wpdb->insert( $this->form_table_name, $data );

			if ( !$insert )
				echo '<p>' . sprintf( __( '<strong>Error: </strong> The %s form could not be imported.', 'visual-form-builder-pro' ), stripslashes( $form['form_title'] ) ) . '</p>';

			// Save the new form_id(s) to update associated fields
			$this->existing_forms[ $form['form_id'] ] = $wpdb->insert_id;
		endforeach;

		echo sprintf( '<p>%s</p>', __( 'Form import process complete.', 'visual-form-builder-pro' ) );

		unset( $this->forms );
	}

	/**
	 * Process the fields from the XML import
	 *
	 * @since 1.7
	 *
	 */
	public function process_fields() {
		global $wpdb;

		if ( empty( $this->fields ) )
			return;

		echo sprintf( '<p>%s</p>', __( 'Importing fields...', 'visual-form-builder-pro' ) );

		foreach ( $this->fields as $field ) :
			$form_id = ( array_key_exists( $field['form_id'], $this->existing_forms ) ) ? $this->existing_forms[ $field['form_id'] ] : $field['form_id'];
			$override = $wpdb->get_var( $wpdb->prepare( "SELECT form_email_from_override, form_email_from_name_override, form_notification_email FROM $this->form_table_name WHERE form_id = %d", $form_id ) );
			$from_name = $wpdb->get_var( null, 1 );
			$notify = $wpdb->get_var( null, 2 );

			$data = array(
				//'field_id' => $field['field_id'],
				'form_id' 			=> $form_id,
				'field_key' 		=> $field['field_key'],
				'field_type' 		=> $field['field_type'],
				'field_options' 	=> $field['field_options'],
				'field_options_other'=> $field['field_options_other'],
				'field_description' => $field['field_description'],
				'field_name' 		=> $field['field_name'],
				'field_sequence' 	=> $field['field_sequence'],
				'field_parent' 		=> $field['field_parent'],
				'field_validation' 	=> $field['field_validation'],
				'field_required' 	=> $field['field_required'],
				'field_size' 		=> $field['field_size'],
				'field_css' 		=> $field['field_css'],
				'field_layout' 		=> $field['field_layout'],
				'field_default' 	=> $field['field_default'],
				'field_rule_setting'=> $field['field_rule_setting'],
				'field_rule'	 	=> $field['field_rule']
			);

			$field_id = $this->field_exists( $field['field_id'] );

			// If the field ID is not a duplicate, it can be used
			if ( !$field_id ) :
				$data['field_id'] 	= $field['field_id'];
				$field_id 			= $field['field_id'];
			endif;

			$insert = $wpdb->insert( $this->field_table_name, $data );

			// Display error message if the insert fails
			if ( !$insert )
				echo '<p>' . sprintf( __( '<strong>Error: </strong> The %s field could not be imported.', 'visual-form-builder-pro' ), stripslashes( $field['field_name'] ) ) . '</p>';

			// Save field IDs so we can update the field rules
			$old_ids[ $field_id ] = $wpdb->insert_id;

			// If a parent field, save the old ID and the new ID to update new parent ID
			if ( in_array( $field['field_type'], array( 'fieldset', 'section', 'verification' ) ) )
				$parents[ $field_id ] = $wpdb->insert_id;

			if ( $override == $field_id )
				$wpdb->update( $this->form_table_name, array( 'form_email_from_override' => $wpdb->insert_id ), array( 'form_id' => $form_id ) );

			if ( $from_name == $field_id )
				$wpdb->update( $this->form_table_name, array( 'form_email_from_name_override' => $wpdb->insert_id ), array( 'form_id' => $form_id ) );

			if ( $notify == $field_id )
				$wpdb->update( $this->form_table_name, array( 'form_notification_email' => $wpdb->insert_id ), array( 'form_id' => $form_id ) );

			// Loop through our parents and update them to their new IDs
			if ( isset( $parents ) ) :
				foreach ( $parents as $k => $v ) {
					$wpdb->update( $this->field_table_name, array( 'field_parent' => $v ), array( 'form_id' => $form_id, 'field_parent' => $k ) );
				}
			endif;

			// Loop through all of the IDs and update the rules if a match is found
			foreach ( $old_ids as $k => $v ) :
				// Get field key
				$field_key = $wpdb->get_var( $wpdb->prepare( "SELECT field_key FROM $this->field_table_name WHERE form_id = %d AND field_id = %d", $form_id, $k ) );

				// Setup search and replace for IDs
				$search = 's:' . strlen( $k ) .':"' . $k . '"';
				$replace = 's:' . strlen( $v ) .':"' . $v . '"';

				$wpdb->query( $wpdb->prepare( "UPDATE $this->field_table_name SET field_rule = REPLACE(field_rule, %s, %s) WHERE form_id = %d", $search, $replace, $form_id ) );

				// Assemble field_id_attr
				$key = 'vfb-' . $field_key . '-';

				// Setup search and replace for field_id_attr
				$search = 's:' . strlen( $key . $k ) .':"' . $key . $k . '"';
				$replace = 's:' . strlen( $key . $v ) .':"' . $key . $v . '"';

				$wpdb->query( $wpdb->prepare( "UPDATE $this->field_table_name SET field_rule = REPLACE(field_rule, %s, %s) WHERE form_id = %d", $search, $replace, $form_id ) );
			endforeach;
		endforeach;

		echo sprintf( '<p>%s</p>', __( 'Field import process complete.', 'visual-form-builder-pro' ) );

		unset( $this->fields );
	}

	/**
	 * Process the entries from the XML import
	 *
	 * @since 1.7
	 *
	 */
	public function process_entries() {
		global $wpdb;

		if ( empty( $this->entries ) )
			return;

		echo sprintf( '<p>%s</p>', __( 'Importing entries...', 'visual-form-builder-pro' ) );

		foreach ( $this->entries as $entry ) :
			$form_id = ( array_key_exists( $entry['form_id'], $this->existing_forms ) ) ? $this->existing_forms[ $entry['form_id'] ] : $entry['form_id'];

			$data = array(
				'entries_id' 		=> $entry['entries_id'],
				'form_id' 			=> $form_id,
				'user_id'			=> $entry['user_id'],
				'data' 				=> $entry['data'],
				'subject' 			=> $entry['subject'],
				'sender_name' 		=> $entry['sender_name'],
				'sender_email' 		=> $entry['sender_email'],
				'emails_to' 		=> $entry['emails_to'],
				'date_submitted'	=> $entry['date_submitted'],
				'ip_address'		=> $entry['ip_address'],
				'notes' 			=> $entry['notes'],
				'akismet'			=> $entry['akismet'],
				'entry_approved'	=> $entry['entry_approved']
			);

			$entry_id = $this->entry_exists( $entry['entries_id'] );

			// If the entry ID is a duplicate, it can't be used
			if ( $entry_id )
				$data['entries_id'] = '';

			$insert = $wpdb->insert( $this->entries_table_name, $data );

			// Display error message if the insert fails
			if ( !$insert )
				echo '<p>' . __( '<strong>Error: </strong> An entry could not be imported.', 'visual-form-builder-pro' ) . '</p>';
		endforeach;

		echo sprintf( '<p>%s</p>', __( 'Entries import process complete.', 'visual-form-builder-pro' ) );

		unset( $this->forms );
	}

	/**
	 * Process the forms from the XML import
	 *
	 * @since 1.7
	 *
	 */
	public function process_form_designs() {
		global $wpdb;

		if ( empty( $this->designs ) )
			return;

		echo sprintf( '<p>%s</p>', __( 'Importing form design settings...', 'visual-form-builder-pro' ) );

		foreach ( $this->designs as $design ) :
			$data = array(
				'design_id' 	=> $design['design_id'],
				'form_id' 		=> $design['form_id'],
				'enable_design' => $design['enable_design'],
				'design_type' 	=> $design['design_type'],
				'design_themes' => $design['design_themes'],
				'design_custom' => $design['design_custom'],
			);

			$design_id = $this->design_exists( $design['design_id'] );

			// If the form ID is a duplicate, it can't be used
			if ( $design_id ) :
				$data['design_id'] = '';

				echo '<p><strong>' . __( 'Form Design ID already exists. Assigning a new ID.', 'visual-form-builder-pro' ) . '</strong></p>';
			endif;

			$insert = $wpdb->insert( $this->design_table_name, $data );

			if ( !$insert )
				echo '<p>' . sprintf( __( '<strong>Error: </strong> Form design settings for form ID %d could not be imported.', 'visual-form-builder-pro' ), $design['form_id'] ) . '</p>';

		endforeach;

		echo sprintf( '<p>%s</p>', __( 'Form Design import process complete.', 'visual-form-builder-pro' ) );

		unset( $this->designs );
	}

	/**
	 * Process the forms from the XML import
	 *
	 * @since 1.7
	 *
	 */
	public function process_payments() {
		global $wpdb;

		if ( empty( $this->payments ) )
			return;

		echo sprintf( '<p>%s</p>', __( 'Importing payment settings...', 'visual-form-builder-pro' ) );

		foreach ( $this->payments as $payment ) :
			$data = array(
				'payment_id' 				=> $payment['payment_id'],
				'form_id' 					=> $payment['form_id'],
				'enable_payment' 			=> $payment['enable_payment'],
				'merchant_type' 			=> $payment['merchant_type'],
				'merchant_details' 			=> $payment['merchant_details'],
				'currency' 					=> $payment['currency'],
				'show_running_total' 		=> $payment['show_running_total'],
				'collect_shipping_address'	=> $payment['collect_shipping_address'],
				'collect_billing_info' 		=> $payment['collect_billing_info'],
				'recurring_payments' 		=> $payment['recurring_payments'],
				'advanced_vars' 			=> $payment['advanced_vars'],
				'price_fields' 				=> $payment['price_fields'],
			);

			$payment_id = $this->payment_exists( $payment['payment_id'] );

			// If the form ID is a duplicate, it can't be used
			if ( $payment_id ) :
				$data['payment_id'] = '';

				echo '<p><strong>' . __( 'Payment ID already exists. Assigning a new ID.', 'visual-form-builder-pro' ) . '</strong></p>';
			endif;

			$insert = $wpdb->insert( $this->payment_table_name, $data );

			if ( !$insert )
				echo '<p>' . sprintf( __( '<strong>Error: </strong> Payments settings for form ID %d could not be imported.', 'visual-form-builder-pro' ), $payment['form_id'] ) . '</p>';

		endforeach;

		echo sprintf( '<p>%s</p>', __( 'Payments import process complete.', 'visual-form-builder-pro' ) );

		unset( $this->payments );
	}

	/**
	 * Check if a form already exists
	 *
	 * @since 1.7
	 *
	 * @param int $form The ID to check
	 * @return mixed Returns 0 or NULL if the form does not exist. Returns the form ID if it exists.
	 */
	public function form_exists( $form ) {
		global $wpdb;

		if ( is_int( $form ) ) :
			if ( 0 == $form )
				return 0;

			return $wpdb->get_var( $wpdb->prepare( "SELECT form_id FROM $this->form_table_name WHERE form_id = %d", $form ) );
		endif;
	}

	/**
	 * Check if a field already exists
	 *
	 * @since 1.7
	 *
	 * @param int $field The ID to check
	 * @return mixed Returns 0 or NULL if the field does not exist. Returns the field ID if it exists.
	 */
	public function field_exists( $field ) {
		global $wpdb;

		if ( is_int( $field ) ) :
			if ( 0 == $field )
				return 0;

			return $wpdb->get_var( $wpdb->prepare( "SELECT field_id FROM $this->field_table_name WHERE field_id = %d", $field ) );
		endif;
	}

	/**
	 * Check if an entry already exists
	 *
	 * @since 1.7
	 *
	 * @param int $entry The ID to check
	 * @return mixed Returns 0 or NULL if the entry does not exist. Returns the entry ID if it exists.
	 */
	public function entry_exists( $entry ) {
		global $wpdb;

		if ( is_int( $entry ) ) :
			if ( 0 == $entry )
				return 0;

			return $wpdb->get_var( $wpdb->prepare( "SELECT entries_id FROM $this->entries_table_name WHERE entries_id = %d", $entry ) );
		endif;
	}

	/**
	 * Check if a form design already exists
	 *
	 * @since 1.7
	 *
	 * @param int $design The ID to check
	 * @return mixed Returns 0 or NULL if the form design does not exist. Returns the design ID if it exists.
	 */
	public function design_exists( $design ) {
		global $wpdb;

		if ( is_int( $design ) ) :
			if ( 0 == $design )
				return 0;

			return $wpdb->get_var( $wpdb->prepare( "SELECT design_id FROM {$this->design_table_name} WHERE design_id = %d", $design ) );
		endif;
	}

	/**
	 * Check if a payment already exists
	 *
	 * @since 1.7
	 *
	 * @param int $payment The ID to check
	 * @return mixed Returns 0 or NULL if the payment does not exist. Returns the payment ID if it exists.
	 */
	public function payment_exists( $payment ) {
		global $wpdb;

		if ( is_int( $payment ) ) :
			if ( 0 == $payment )
				return 0;

			return $wpdb->get_var( $wpdb->prepare( "SELECT payment_id FROM {$this->payment_table_name} WHERE payment_id = %d", $payment ) );
		endif;
	}

	/**
	 * Handles the upload and initial parsing of the file to prepare for
	 *
	 * @since 1.7
	 *
	 * @return bool False if error uploading or invalid file, true otherwise
	 */
	public function handle_upload() {
		$file = wp_import_handle_upload();

		if ( isset( $file['error'] ) ) :
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'visual-form-builder-pro' ) . '</strong><br />';
			echo esc_html( $file['error'] ) . '</p>';

			return false;
		elseif ( ! file_exists( $file['file'] ) ) :
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'visual-form-builder-pro' ) . '</strong><br />';
			printf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'visual-form-builder-pro' ), esc_html( $file['file'] ) );
			echo '</p>';

			return false;
		endif;

		$this->id = (int) $file['id'];
		$import_data = $this->parse( $file['file'] );
		if ( is_wp_error( $import_data ) ) :
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'visual-form-builder-pro' ) . '</strong><br />';
			echo esc_html( $import_data->get_error_message() ) . '</p>';

			return false;
		endif;

		$this->version = $import_data['version'];
		if ( $this->version > $this->max_version ) :
			echo '<div class="error"><p><strong>';
			printf( __( 'This Visual Form Builder Pro export file (version %s) may not be supported by this version of the importer. Please consider updating.', 'visual-form-builder-pro' ), esc_html( $import_data['version'] ) );
			echo '</strong></p></div>';
		endif;

		return true;
	}

	/**
	 * Parse an XML file
	 *
	 * @param string $file Path to XML file for parsing
	 * @return array Information gathered from the XML file
	 */
	public function parse( $file ) {
		if ( extension_loaded( 'simplexml' ) ) :
			$parser = new VFB_Parser_SimpleXML();
			$result = $parser->parse( $file );

			if ( is_wp_error( $result ) ) :
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'visual-form-builder-pro' ) . '</strong><br />';
				echo esc_html( $result->get_error_message() ) . '</p>';

				die();
			endif;

			// If SimpleXML succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) || 'SimpleXML_parse_error' != $result->get_error_code() )
				return $result;
		endif;
	}
}

/**
 * Parser that makes use of the SimpleXML PHP extension.
 */
class VFB_Parser_SimpleXML {
	/**
	 * Parse the XML file and return
	 *
	 * @since 1.7
	 *
	 * @param string $file The uploaded file
	 * @return mixed Returns and error if there are problems. Returns an array of all forms, fields, and entries.
	 */
	public function parse( $file ) {
		$forms = $fields = $entries = $designs = $payments = array();

		$internal_errors = libxml_use_internal_errors(true);
		$xml = simplexml_load_file( $file );

		// halt if loading produces an error
		if ( ! $xml )
			return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this Visual Form Builder Pro export file', 'visual-form-builder-pro' ), libxml_get_errors() );

		$export_version = $xml->xpath('/rss/channel/vfb:export_version');
		if ( ! $export_version )
			return new WP_Error( 'VFB_parse_error', __( 'This does not appear to be an XML file, missing/invalid Visual Form Builder Pro version number', 'visual-form-builder-pro' ) );

		$export_version = (string) trim( $export_version[0] );
		// confirm that we are dealing with the correct file format
		if ( ! preg_match( '/^\d+\.\d+$/', $export_version ) )
			return new WP_Error( 'VFB_parse_error', __( 'This does not appear to be a XML file, missing/invalid Visual Form Builder Pro version number', 'visual-form-builder-pro' ) );

		$namespaces = $xml->getDocNamespaces();
		if ( ! isset( $namespaces['vfb'] ) )
			$namespaces['vfb'] = 'http://matthewmuro.com/export/1.9/';

		// Forms
		foreach ( $xml->xpath('/rss/channel/vfb:form') as $form_arr ) :
			$a = $form_arr->children( $namespaces['vfb'] );

			$forms[] = array(
				'form_id' 						=> (int) 	$a->form_id,
				'form_key' 						=> (string) $a->form_key,
				'form_title' 					=> (string) $a->form_title,
				'form_email_subject' 			=> (string) $a->form_email_subject,
				'form_email_to' 				=> (string) $a->form_email_to,
				'form_email_from' 				=> (string) $a->form_email_from,
				'form_email_from_name' 			=> (string) $a->form_email_from_name,
				'form_email_from_override' 		=> (string) $a->form_email_from_override,
				'form_email_from_name_override' => (string) $a->form_email_from_name_override,
				'form_success_type' 			=> (string) $a->form_success_type,
				'form_success_message' 			=> (string) $a->form_success_message,
				'form_notification_setting' 	=> (string) $a->form_notification_setting,
				'form_notification_email_name' 	=> (string) $a->form_notification_email_name,
				'form_notification_email_from' 	=> (string) $a->form_notification_email_from,
				'form_notification_email' 		=> (string) $a->form_notification_email,
				'form_notification_subject' 	=> (string) $a->form_notification_subject,
				'form_notification_message' 	=> (string) $a->form_notification_message,
				'form_notification_entry' 		=> (string) $a->form_notification_entry,
				'form_email_design' 			=> (string) $a->form_email_design,
				'form_paypal_setting' 			=> (string) $a->form_paypal_setting,
				'form_paypal_email' 			=> (string) $a->form_paypal_email,
				'form_paypal_currency' 			=> (string) $a->form_paypal_currency,
				'form_paypal_shipping' 			=> (string) $a->form_paypal_shipping,
				'form_paypal_tax' 				=> (string) $a->form_paypal_tax,
				'form_paypal_field_price' 		=> (string) $a->form_paypal_field_price,
				'form_paypal_item_name' 		=> (string) $a->form_paypal_item_name,
				'form_label_alignment' 			=> (string) $a->form_label_alignment,
				'form_verification' 			=> (int) 	$a->form_verification,
				'form_entries_allowed' 			=> (int) 	$a->form_entries_allowed,
				'form_entries_schedule' 		=> (string) $a->form_entries_schedule,
				'form_unique_entry' 			=> (int) 	$a->form_unique_entry
			);
		endforeach;

		// Fields
		foreach ( $xml->xpath('/rss/channel/vfb:field') as $field_arr ) :
			$a = $field_arr->children( $namespaces['vfb'] );

			$fields[] = array(
				'field_id' 			=> (int) 	$a->field_id,
				'form_id' 			=> (int) 	$a->form_id,
				'field_key' 		=> (string) $a->field_key,
				'field_type' 		=> (string) $a->field_type,
				'field_options' 	=> (string) $a->field_options,
				'field_options_other'=> (string) $a->field_options_other,
				'field_description' => (string) $a->field_description,
				'field_name' 		=> (string) $a->field_name,
				'field_sequence' 	=> (int) 	$a->field_sequence,
				'field_parent' 		=> (int) 	$a->field_parent,
				'field_validation' 	=> (string) $a->field_validation,
				'field_required' 	=> (string) $a->field_required,
				'field_size' 		=> (string) $a->field_size,
				'field_css' 		=> (string) $a->field_css,
				'field_layout' 		=> (string) $a->field_layout,
				'field_default' 	=> (string) $a->field_default,
				'field_rule_setting'=> (int) 	$a->field_rule_setting,
				'field_rule'		=> (string) $a->field_rule
			);
		endforeach;

		// Entries
		foreach ( $xml->xpath('/rss/channel/vfb:entry') as $entry_arr ) :
			$a = $entry_arr->children( $namespaces['vfb'] );

			$entries[] = array(
				'entries_id' 		=> (int) 	$a->entries_id,
				'form_id' 			=> (int) 	$a->form_id,
				'user_id' 			=> (int) 	$a->user_id,
				'data' 				=> (string) $a->data,
				'subject' 			=> (string) $a->subject,
				'sender_name' 		=> (string) $a->sender_name,
				'sender_email' 		=> (string) $a->sender_email,
				'emails_to' 		=> (string) $a->emails_to,
				'date_submitted' 	=> (string) $a->date_submitted,
				'ip_address' 		=> (string) $a->ip_address,
				'notes' 			=> (string) $a->notes,
				'akismet'			=> (string) $a->akismet,
				'entry_approved'	=> (int)	$a->entry_approved
			);
		endforeach;

		// Form Design add-on
		foreach ( $xml->xpath('/rss/channel/vfb:form_design') as $design_arr ) :
			$a = $design_arr->children( $namespaces['vfb'] );

			$designs[] = array(
				'design_id' 	=> (int) 	$a->design_id,
				'form_id' 		=> (int) 	$a->form_id,
				'enable_design' => (int) 	$a->enable_design,
				'design_type' 	=> (string) $a->design_type,
				'design_themes' => (string) $a->design_themes,
				'design_custom' => (string) $a->design_custom,
			);
		endforeach;

		// Payment add-on
		foreach ( $xml->xpath('/rss/channel/vfb:payment') as $payment_arr ) :
			$a = $payment_arr->children( $namespaces['vfb'] );

			$payments[] = array(
				'payment_id' 				=> (int) 	$a->payment_id,
				'form_id' 					=> (int) 	$a->form_id,
				'enable_payment' 			=> (int) 	$a->enable_payment,
				'merchant_type' 			=> (string) $a->merchant_type,
				'merchant_details' 			=> (string) $a->merchant_details,
				'currency' 					=> (string) $a->currency,
				'show_running_total' 		=> (int) $a->show_running_total,
				'collect_shipping_address' 	=> (int) $a->collect_shipping_address,
				'collect_billing_info' 		=> (string) $a->collect_billing_info,
				'recurring_payments' 		=> (string) $a->recurring_payments,
				'advanced_vars' 			=> (string) $a->advanced_vars,
				'price_fields' 				=> (string) $a->price_fields,
			);
		endforeach;

		return array(
			'forms' 	=> $forms,
			'fields' 	=> $fields,
			'entries' 	=> $entries,
			'designs'	=> $designs,
			'payments'	=> $payments,
			'version' 	=> $export_version
		);
	}
}
