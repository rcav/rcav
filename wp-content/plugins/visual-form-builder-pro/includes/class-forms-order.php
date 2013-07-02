<?php
/**
 * Class that builds our Entries table
 *
 * @since 1.2
 */
class VisualFormBuilder_Pro_Forms_Order extends WP_List_Table {

	function __construct(){
		global $wpdb;

		// Setup global database table names
		$this->field_table_name   = $wpdb->prefix . 'vfb_pro_fields';
		$this->form_table_name    = $wpdb->prefix . 'vfb_pro_forms';
		$this->entries_table_name = $wpdb->prefix . 'vfb_pro_entries';
	}

	/**
	 * A custom function to get the entries and sort them
	 *
	 * @since 1.2
	 * @returns array() $cols SQL results
	 */
	function get_forms( $orderby = 'form_id', $order = 'ASC', $search = '' ){
		global $wpdb, $current_user;

		get_currentuserinfo();

		// Save current user ID
		$user_id = $current_user->ID;

		// Get the Form Order type settings, if any
		$user_form_order_type = get_user_meta( $user_id, 'vfb-form-order-type', true );

		// Get the Form Order settings, if any
		$user_form_order = get_user_meta( $user_id, 'vfb-form-order' );
		foreach ( $user_form_order as $form_order ) {
			$form_order = implode( ',', $form_order );
		}

		$sql_order = sanitize_sql_orderby( "$orderby $order" );

		if ( in_array( $user_form_order_type, array( 'order', '' ) ) )
			$sql_order = ( isset( $form_order ) ) ? "FIELD( form_id, $form_order )" : sanitize_sql_orderby( 'form_id DESC' );
		else
			$sql_order = sanitize_sql_orderby( 'form_title ASC' );

		$cols = $wpdb->get_results( "SELECT form_id, form_title FROM $this->form_table_name WHERE 1=1 $search ORDER BY $sql_order" );

		return $cols;
	}

	/**
	 * Get the number of entries for use with entry statuses
	 *
	 * @since 2.1
	 * @returns array $stats Counts of different entry types
	 */
	function get_entries_count( $form_id ) {
		global $wpdb;

		$entries = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( * ) FROM $this->entries_table_name AS entries WHERE entries.entry_approved = 1 AND form_id = %d", $form_id ) );

		return $entries;
	}

	/**
	 * Get the number of entries for use with entry statuses
	 *
	 * @since 2.1
	 * @returns array $stats Counts of different entry types
	 */
	function get_entries_today_count( $form_id ) {
		global $wpdb;

		$entries = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( * ) FROM $this->entries_table_name AS entries WHERE entries.entry_approved = 1 AND form_id = %d AND date_submitted >= curdate()", $form_id ) );

		return $entries;
	}

	/**
	 * Get the number of forms
	 *
	 * @since 2.2.7
	 * @returns int $count Form count
	 */
	function get_forms_count() {
		global $wpdb;

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $this->form_table_name" );

		return $count;
	}

	/**
	 * Display Search box
	 *
	 * @since 1.4
	 * @returns html Search Form
	 */
	function search_box( $text, $input_id ) {
	    parent::search_box( $text, $input_id );
	}

	/**
	 * Prepares our data for display
	 *
	 * @since 1.2
	 */
	function prepare_items() {
		global $wpdb, $current_user;

		// Get entries search terms
		$search_terms = ( !empty( $_REQUEST['s'] ) ) ? explode( ' ', $_REQUEST['s'] ) : array();

		$searchand = $search = '';
		// Loop through search terms and build query
		foreach( $search_terms as $term ) {
			$term = esc_sql( like_escape( $term ) );

			$search .= "{$searchand}((form_title LIKE '%{$term}%') OR (form_key LIKE '%{$term}%') OR (form_email_subject LIKE '%{$term}%'))";
			$searchand = ' AND ';
		}

		$search = ( !empty($search) ) ? " AND ({$search}) " : '';

		// Set our ORDER BY and ASC/DESC to sort the entries
		$orderby  = ( !empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'form_id';
		$order    = ( !empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc';

		// Get the sorted entries
		$forms = $this->get_forms( $orderby, $order, $search );

		$data = array();

		// Loop trough the entries and setup the data to be displayed for each row
		foreach ( $forms as $form ) :

			$entries_counts = array(
				'total' => $this->get_entries_count( $form->form_id ),
				'today' => $this->get_entries_today_count( $form->form_id ),
			);

			$data[] = array(
				'id' 			=> $form->form_id,
				'form_id'		=> $form->form_id,
				'form_title' 	=> stripslashes( $form->form_title ),
				'entries'		=> $entries_counts,
			);
		endforeach;

		// How many forms do we have?
		$total_items = $this->get_forms_count();

		// Register our pagination
		$this->set_pagination_args( array(
			'total_items'	=> $total_items,
		) );

		// Add sorted data to the items property
		$this->items = $data;
	}

	function display() {
		if ( $this->has_items() ) :

			echo '<div class="tablenav top">';
				$this->pagination( 'top' );
			echo '<br class="clear" /></div>';

			$this->display_forms();

			echo '<div class="vfb-empty-container ui-state-disabled"></div>';
		endif;
	}

	function display_forms() {
		foreach ( $this->items as $item )
			$this->single_row( $item );
	}

	function single_row( $item ) {

		$count = $item['entries'];
?>
		<div class="vfb-box form-boxes" id="vfb-form-<?php echo $item['form_id']; ?>">
			<div class="vfb-form-meta-actions">
				<h2 title="<?php esc_attr_e( 'Drag to reorder', 'visual-form-builder-pro' ); ?>" class="form-boxes-title"><?php echo $item['form_title']; ?></h2>

				<div class="vfb-form-meta-entries">
					<ul class="vfb-meta-entries-list">
						<li><a  class="vfb-meta-entries-header" href="<?php echo esc_url( add_query_arg( array( 'form-filter' => $item['form_id'] ), admin_url( 'admin.php?page=vfb-entries' ) ) ); ?>"><?php _e( 'Entries', 'visual-form-builder-pro' ); ?></a></li>
						<li><a class="vfb-meta-entries-total" href="<?php echo esc_url( add_query_arg( array( 'form-filter' => $item['form_id'] ), admin_url( 'admin.php?page=vfb-entries' ) ) ); ?>"><span class="entries-count"><?php echo $count['total']; ?></span></a> <?php _e( 'Total', 'visual-form-builder-pro' ); ?></li>
						<li><a class="vfb-meta-entries-total-today" href="<?php echo esc_url( add_query_arg( array( 'form-filter' => $item['form_id'], 'today' => 1 ), admin_url( 'admin.php?page=vfb-entries' ) ) ); ?>"><span class="entries-count"><?php echo $count['today']; ?></span></a> <?php _e( 'Today', 'visual-form-builder-pro' ); ?></li>
					</ul>
				</div>

				<div class="vfb-form-meta-other">
					<ul>
						<li><a href="<?php echo esc_url( add_query_arg( array( 'form_id' => $item['form_id'] ), admin_url( 'admin.php?page=vfb-email-design' ) ) ); ?>"><?php _e( 'Email Design', 'visual-form-builder-pro' ); ?></a></li>
						<li><a href="<?php echo esc_url( add_query_arg( array( 'form_id' => $item['form_id'] ), admin_url( 'admin.php?page=vfb-reports' ) ) ); ?>"><?php _e( 'Analytics', 'visual-form-builder-pro' ); ?></a></li>
						<?php if ( class_exists( 'VFB_Pro_Payments' ) ) : ?>
						<li><a href="<?php echo esc_url( add_query_arg( array( 'form_id' => $item['form_id'] ), admin_url( 'admin.php?page=vfb-payments' ) ) ); ?>"><?php _e( 'Payments', 'visual-form-builder-pro' ); ?></a></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
			<div class="clear"></div>
			<div class="vfb-publishing-actions">
	            <p>
	            <?php if ( current_user_can( 'vfb_edit_forms' ) ) : ?>
	            	<a class="" href="<?php echo esc_url( add_query_arg( array( 'form' => $item['form_id'] ), admin_url( 'admin.php?page=visual-form-builder-pro' ) ) ); ?>">
	            	<strong><?php _e( 'Edit Form', 'visual-form-builder-pro' ); ?></strong>
	            	</a> |
	            <?php endif; ?>
	            <?php if ( current_user_can( 'vfb_delete_forms' ) ) : ?>
	            	<a class="submitdelete menu-delete" href="<?php echo esc_url( wp_nonce_url( admin_url('admin.php?page=visual-form-builder-pro&amp;action=delete_form&amp;form=' . $item['form_id'] ), 'delete-form-' . $item['form_id'] ) ); ?>" class=""><?php _e( 'Delete' , 'visual-form-builder-pro'); ?></a> |
	            <?php endif; ?>
	            <?php if ( current_user_can( 'vfb_edit_forms' ) ) : ?>
	            	<a href="<?php echo esc_url( add_query_arg( array( 'form' => $item['form_id'], 'preview' => 1 ), plugins_url( 'visual-form-builder-pro/form-preview.php' ) ) ); ?>" class="" target="_blank" title="<?php _e( 'Preview the Form', 'visual-form-builder-pro' ); ?>"><?php _e( 'Preview', 'visual-form-builder-pro' ); ?></a>
	            <?php endif; ?>
	            </p>
			</div> <!-- .vfb-publishing-actions -->
		</div> <!-- .vfb-box -->
<?php
	}

	/**
	 * Display a view switcher
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function view_switcher( $current_mode ) {
		$modes = array(
			'order'	=> __( 'Custom Order', 'visual-form-builder-pro' ),
			'list'	=> __( 'List View', 'visual-form-builder-pro' )
		);
?>
		<input type="hidden" name="mode" value="<?php echo esc_attr( $current_mode ); ?>" />
		<div class="view-switch">
<?php
			foreach ( $modes as $mode => $title ) :
				$class = ( $current_mode == $mode ) ? 'class="current"' : '';

				// Use excerpt to switch the default WP image but keep order as the mode action
				$real_mode = ( 'order' == $mode ) ? 'excerpt' : 'list';

				echo "<a href='" . esc_url( add_query_arg( 'mode', $mode, $_SERVER['REQUEST_URI'] ) ) . "' $class><img id='view-switch-$real_mode' src='" . esc_url( includes_url( 'images/blank.gif' ) ) . "' width='20' height='20' title='$title' alt='$title' /></a>\n";
			endforeach;
		?>
		</div>
<?php
	}

	/**
	 * Display the pagination.
	 * Customize default function to work with months and form drop down filters
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function pagination( $which ) {
		global $current_user;

		if ( empty( $this->_pagination_args ) )
			return;

		extract( $this->_pagination_args, EXTR_SKIP );

		$output = '<span class="displaying-num">' . sprintf( _n( '1 form', '%s forms', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$this->_pagination = "<div class='tablenav-pages'>$output</div>";

		echo $this->_pagination;

		// Current user ID
		$user_id = $current_user->ID;

		// Form order type
		$type = get_user_meta( $user_id, 'vfb-form-order-type', true );

		if ( 'top' == $which )
			$this->view_switcher( $type );
	}

}
