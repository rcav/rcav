<?php
/**
 * Class that builds our Entries detail page
 *
 * @since 1.4
 */
class VisualFormBuilder_Pro_Entries_Detail{
	public function __construct(){
		global $wpdb;

		// Setup global database table names
		$this->field_table_name   = $wpdb->prefix . 'vfb_pro_fields';
		$this->form_table_name    = $wpdb->prefix . 'vfb_pro_forms';
		$this->entries_table_name = $wpdb->prefix . 'vfb_pro_entries';

		add_action( 'admin_init', array( &$this, 'entries_detail' ) );
	}

	public function entries_detail(){
		global $wpdb;

		// Either 'view' or 'edit'
		$view = esc_html( $_GET['action'] );

		// Entry ID
		$entry_id = absint( $_REQUEST['entry'] );

		$entry = $wpdb->get_row( $wpdb->prepare( "SELECT forms.form_title, entries.* FROM $this->form_table_name AS forms INNER JOIN $this->entries_table_name AS entries ON entries.form_id = forms.form_id WHERE entries.entries_id  = %d", $entry_id ) );

		echo sprintf( '<p><a href="?page=%1$s" class="view-entry">&laquo; %2$s</a></p>', esc_html( $_REQUEST['page'] ), __( 'Back to Entries', 'visual-form-builder-pro' ) );

		// Get the date/time format that is saved in the options table
		$date_format = get_option('date_format');
		$time_format = get_option('time_format');

		$data = unserialize( $entry->data );
?>
		<form id="entry-edit" method="post" action="">
			<input name="action" type="hidden" value="update_entry" />
			<input name="entry_id" type="hidden" value="<?php echo $entry_id; ?>" />
			<?php wp_nonce_field( 'update-entry-' . $entry_id ); ?>
			<h3><span>
				<?php echo stripslashes( $entry->form_title ); ?> :
				<?php _e( 'Entry' , 'visual-form-builder-pro'); ?> #
				<?php echo $entry->entries_id; ?>
			</span></h3>
            <div id="vfb-poststuff" class="metabox-holder has-right-sidebar">

			<div id="vfb-entries-body-content">
	        <?php
	        if ( 'edit' == $view && current_user_can( 'vfb_edit_entries' ) )
	        	$this->entries_edit( $data );
	        elseif ( 'view' == $view && current_user_can( 'vfb_view_entries' ) )
	        	$this->entries_display( $data );
			?>
			</table>
		</div> <!-- #vfb-entries-body-content -->
		<div id="side-info-column" class="inner-sidebar">
			<div id="side-sortables">
				<div id="submitdiv" class="postbox">
					<h3><span><?php _e( 'Details' , 'visual-form-builder-pro'); ?></span></h3>
					<div class="inside">
					<div id="submitbox" class="submitbox">
						<div id="minor-publishing">
							<div id="misc-publishing-actions">
								<div class="misc-pub-section">
									<span><strong><?php _e( 'Form Title' , 'visual-form-builder-pro'); ?>: </strong><?php echo stripslashes( $entry->form_title ); ?></span>
								</div>
								<div class="misc-pub-section">
									<span><strong><?php _e( 'Date Submitted' , 'visual-form-builder-pro'); ?>: </strong><?php echo date( "$date_format $time_format", strtotime( $entry->date_submitted ) ); ?></span>
								</div>
								<div class="misc-pub-section">
									<span><strong><?php _e( 'IP Address' , 'visual-form-builder-pro'); ?>: </strong><?php echo $entry->ip_address; ?></span>
								</div>
								<div class="misc-pub-section">
									<span><strong><?php _e( 'Email Subject' , 'visual-form-builder-pro'); ?>: </strong><?php echo stripslashes( $entry->subject ); ?></span>
								</div>
								<div class="misc-pub-section">
									<span><strong><?php _e( 'Sender Name' , 'visual-form-builder-pro'); ?>: </strong><?php echo stripslashes( $entry->sender_name ); ?></span>
								</div>
								<div class="misc-pub-section">
									<span><strong><?php _e( 'Sender Email' , 'visual-form-builder-pro'); ?>: </strong><a href="mailto:<?php echo stripslashes( $entry->sender_email ); ?>"><?php echo stripslashes( $entry->sender_email ); ?></a></span>
								</div>
								<div class="misc-pub-section">
									<span><strong><?php _e( 'Emailed To' , 'visual-form-builder-pro'); ?>: </strong><?php echo preg_replace('/\b([A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4})\b/i', '<a href="mailto:$1">$1</a>', implode( ',', unserialize( stripslashes( $entry->emails_to ) ) ) ); ?></span>
								</div>
								<div class="misc-pub-section misc-pub-section-last">
									<span><strong><?php _e( 'Notes' , 'visual-form-builder-pro'); ?>: </strong></span>
									<br>
									<textarea name="entries-notes" style="width:100%;height:7em;"><?php echo stripslashes( $entry->notes ); ?></textarea>
								</div>
								<div class="clear"></div>
							</div> <!--#misc-publishing-actions -->
						</div> <!-- #minor-publishing -->

						<div id="major-publishing-actions">
							<?php if ( current_user_can( 'vfb_delete_entries' ) ) : ?>
								<div id="delete-action">
								<?php echo sprintf( '<a class="submitdelete deletion entry-delete" href="?page=%2$s&action=%3$s&entry=%4$d">%1$s</a>', __( 'Move to Trash', 'visual-form-builder-pro' ), $_REQUEST['page'], 'trash', $entry_id ); ?>
								</div>
							<?php endif; ?>

							<div id="publishing-action">
							<?php submit_button( __( 'Print', 'visual-form-builder-pro' ), 'secondary', 'submit', false, array( 'onclick' => 'window.print();return false;' ) ); ?>
                            <?php
                            	if ( current_user_can( 'vfb_edit_entries' ) ) :
                                	if ( is_array( $data[0] ) && 'edit' == $_GET['action'] )
                                		submit_button( __( 'Update', 'visual-form-builder-pro' ), 'primary', 'submit', false );
                                endif;
                            ?>
							</div>
							<div class="clear"></div>
						</div> <!-- #major-publishing-actions -->
					</div> <!-- #submitbox -->
					</div> <!-- .inside -->
				</div> <!-- #submitdiv -->
			</div> <!-- #side-sortables -->
		</div> <!-- #side-info-column -->
		<br class="clear">
		</div> <!-- #vfb-poststuff -->
		</form>
	<?php
	}

	public function entries_display( $data ) {
		$count = 0;
		$open_fieldset = $open_section = false;

		foreach ( $data as $k => $v ) :
			if ( !is_array( $v ) ) :
				if ( $count == 0 )
					echo '<div class="postbox"><div class="inside">';

				echo sprintf( '<h4>%1$s</h4>%2$s', ucwords( $k ), $v );

				$count++;
			else :
				// Cast each array as an object
				$obj = (object) $v;

				if ( $obj->type == 'fieldset' ) :
					// Close each fieldset
					if ( $open_fieldset == true )
						echo '</table>';

					echo sprintf( '<h3>%s</h3><table class="form-table">', stripslashes( $obj->name ) );

					$open_fieldset = true;
				endif;


				switch ( $obj->type ) :
					case 'fieldset' :
					case 'section' :
					case 'submit' :
					case 'page-break' :
					case 'verification' :
					case 'secret' :
					break;

					case 'file-upload' :
						?>
						<tr valign="top">
							<th scope="row"><label for="field[<?php echo $obj->id; ?>]"><?php echo stripslashes( $obj->name ); ?></label></th>
							<td style="background:#eee;border:1px solid #ddd"><a href="<?php esc_attr_e( $obj->value ); ?>" target="_blank"><?php echo stripslashes( esc_html( $obj->value ) ); ?></a></td>
						</tr>
                    	<?php
					break;

					default :
						?>
						<tr valign="top">
							<th scope="row"><label for="field[<?php echo $obj->id; ?>]"><?php echo stripslashes( $obj->name ); ?></label></th>
							<td style="background:#eee;border:1px solid #ddd"><?php echo stripslashes( wp_specialchars_decode( esc_html( $obj->value ) ) ); ?></td>
						</tr>
                    	<?php
					break;
				endswitch;
			endif;
		endforeach;

		if ( $count > 0 )
			echo '</div></div>';
	}

	public function entries_edit( $data ) {
		$count = 0;
		$open_fieldset = $open_section = false;

		foreach ( $data as $k => $v ) :
			if ( !is_array( $v ) ) :
				if ( $count == 0 )
					echo '<div class="postbox"><div class="inside">';

				echo sprintf( '<h4>%1$s</h4>%2$s', ucwords( $k ), $v );

				$count++;
			else :
				// Cast each array as an object
				$obj = (object) $v;

				$field_id = absint( $obj->id );

				// Close each fieldset
				if ( $obj->type == 'fieldset' ) :

					if ( $open_fieldset == true )
						echo '</table>';

					echo sprintf( '<h3>%s</h3><table class="form-table">', stripslashes( $obj->name ) );

					$open_fieldset = true;
				endif;

				switch ( $obj->type ) :
					case 'fieldset' :
					case 'section' :
					case 'submit' :
					case 'page-break' :
					case 'verification' :
					case 'secret' :
						?>
                        	<input name="field[<?php echo $field_id; ?>]" type="hidden" value="<?php echo stripslashes( esc_attr( $obj->value ) ); ?>" />
                        <?php
					break;

					case 'textarea' :
					case 'address' :
					case 'html' :
						?>
						<tr valign="top">
							<th scope="row"><label for="field[<?php echo $field_id; ?>]"><?php echo stripslashes( $obj->name ); ?></label></th>
                            <td><textarea id="field[<?php echo $field_id; ?>]" name="field[<?php echo $field_id; ?>]" class="large-text" cols="25" rows="8" type="text" ><?php echo wp_specialchars_decode( stripslashes( $obj->value ), ENT_QUOTES ); ?></textarea></td>
						</tr>
                    	<?php
					break;

					case 'select' :
						?>
						<tr valign="top">
							<th scope="row"><label for="field[<?php echo $field_id; ?>]"><?php echo stripslashes( $obj->name ); ?></label></th>
							<td>
                               <select id="field[<?php echo $field_id; ?>]" name="field[<?php echo $field_id; ?>]">
                                <?php
                                    $options = ( is_array( unserialize( $obj->options ) ) ) ? unserialize( $obj->options ) : explode( ',', unserialize( $obj->options ) );

                                    foreach( $options as $option => $value ) {
                                        echo '<option value="' . stripslashes( esc_attr( $value ) ) . '" ' . selected( $obj->value, $value, 0 ) . '>' . stripslashes( esc_attr( $value ) ) . '</option>';
                                    }
                                ?>
                                </select>
							</td>
						</tr>
                    	<?php
					break;

					case 'radio' :
						?>
						<tr valign="top">
							<th scope="row"><label for="field[<?php echo $field_id; ?>]"><?php echo stripslashes( $obj->name ); ?></label></th>
							<td>
                                <?php
                                    $options = ( is_array( unserialize( $obj->options ) ) ) ? unserialize( $obj->options ) : explode( ',', unserialize( $obj->options ) );
                                    $count = 1;
                                    foreach( $options as $option => $value ) {
                                        echo '<label for="field[' . $field_id . '][' . $count . ']"><input type="radio" id="field[' . $field_id . '][' . $count . ']" name="field[' . $field_id . ']" value="' . stripslashes( esc_attr( $value ) ) . '" ' . checked( $obj->value, stripslashes( $value ), 0) . '> ' . stripslashes( esc_attr( $value ) ) . '</label><br />';
                                        $count++;
                                    }

                                    // Get 'Allow Other'
									$field_options_other = ( isset( $obj->options_other ) ) ? maybe_unserialize( $obj->options_other ) : '';

									// Display 'Other' field
									if ( isset( $field_options_other['setting'] ) && 1 == $field_options_other['setting'] && isset( $field_options_other['selected'] ) ) {
										//$count++;
										echo '<label for="field[' . $field_id . '][' . $count . ']"><input type="radio" name="field[' . $field_id . ']" id="field[' . $field_id . '][' . $count . ']" value="'. stripslashes( esc_attr( $field_options_other['selected'] ) ) . '" ' . checked( $obj->value, $field_options_other['selected'], 0 ) . '> ' . stripslashes( esc_attr( $field_options_other['selected'] ) ) . '</label><br />';
									}
                                ?>
							</td>
						</tr>
                    	<?php
					break;

					case 'checkbox' :
						?>
						<tr valign="top">
							<th scope="row"><label for="field[<?php echo $field_id; ?>]"><?php echo stripslashes( $obj->name ); ?></label></th>
							<td>
                                <?php
                                    $options = ( is_array( unserialize( $obj->options ) ) ) ? unserialize( $obj->options ) : explode( ',', unserialize( $obj->options ) );

									$vals = explode( ', ', $obj->value );
									$count = 1;
                                    foreach( $options as $option => $value ) {
										$checked = ( in_array( $value, $vals ) || ( strpos( $obj->value, $value ) !== false ) ) ? 'checked="checked" ' : '';

                                       	echo '<label for="field[' . $field_id . '][' . $count . ']"><input type="checkbox" id="field[' . $field_id . '][' . $count . ']" name="field[' . $field_id . '][]" value="' . stripslashes( esc_attr( $value ) ) . '" ' . $checked . '> ' . stripslashes( esc_attr( $value ) ) . '</label><br />';
                                       	$count++;
                                    }
                                ?>
							</td>
						</tr>
                    	<?php
					break;

					default :
						?>
						<tr valign="top">
							<th scope="row"><label for="field[<?php echo $field_id; ?>]"><?php echo stripslashes( $obj->name ); ?></label></th>
							<td><input id="field[<?php echo $field_id; ?>]" name="field[<?php echo $field_id; ?>]" class="regular-text" type="text" value="<?php echo stripslashes( $obj->value ); ?>" /></td>
						</tr>
                    	<?php
					break;
				endswitch;
			endif;
		endforeach;

		if ( $count > 0 )
			echo '</div></div>';
	}
}
