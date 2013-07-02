<?php
global $wpdb;

$field_where = ( isset( $field_id ) && !is_null( $field_id ) ) ? "AND field_id = $field_id" : '';
$fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->field_table_name WHERE form_id = %d $field_where ORDER BY field_sequence ASC", $form_nav_selected_id ) );

$depth = 1;
$parent = $last = 0;
ob_start();

// Loop through each field and display
foreach ( $fields as $field ) :

	// If we are at the root level
	if ( !$field->field_parent && $depth > 1 ) {
		// If we've been down a level, close out the list
		while ( $depth > 1 ) {
			echo '</li></ul>';
			$depth--;
		}

		// Close out the root item
		echo '</li>';
	}
	// first item of <ul>, so move down a level
	elseif ( $field->field_parent && $field->field_parent == $last ) {
		echo '<ul class="parent">';
		$depth++;
	}
	// Close up a <ul> and move up a level
	elseif ( $field->field_parent && $field->field_parent != $parent ) {
		echo '</li></ul></li>';
		$depth--;
	}
	// Same level so close list item
	elseif ( $field->field_parent && $field->field_parent == $parent )
		echo '</li>';

	// Store item ID and parent ID to test for nesting
	$last = $field->field_id;
	$parent = $field->field_parent;
?>
<li id="form_item_<?php echo $field->field_id; ?>" class="form-item<?php echo ( in_array( $field->field_type, array( 'submit', 'secret', 'verification' ) ) ) ? ' ui-state-disabled' : ''; ?><?php echo ( !in_array( $field->field_type, array( 'fieldset', 'section', 'verification' ) ) ) ? ' mjs-nestedSortable-no-nesting' : ''; ?>">
<dl class="menu-item-bar vfb-menu-item-inactive">
<dt class="vfb-menu-item-handle vfb-menu-item-type-<?php echo esc_attr( $field->field_type ); ?>">
	<span class="item-title"><?php echo stripslashes( esc_attr( $field->field_name ) ); ?><?php echo ( $field->field_required == 'yes' ) ? ' <span class="is-field-required">*</span>' : ''; ?></span>
    <span class="item-controls">
    	<?php echo ( 1 == $field->field_rule_setting ) ? '<span class="item-conditional-icon"></span>' : '' ?>
		<span class="item-type"><?php echo strtoupper( str_replace( '-', ' ', $field->field_type ) ); ?></span>
		<a href="#" title="<?php esc_attr_e( 'Edit Field Item' , 'visual-form-builder-pro'); ?>" id="edit-<?php echo $field->field_id; ?>" class="item-edit"><?php _e( 'Edit Field Item' , 'visual-form-builder-pro'); ?></a>
	</span>
</dt>
</dl>

<div id="form-item-settings-<?php echo $field->field_id; ?>" class="menu-item-settings field-type-<?php echo $field->field_type; ?>" style="display: none;">
<?php if ( in_array( $field->field_type, array( 'fieldset', 'section', 'page-break', 'verification' ) ) ) : ?>
	<!-- Legend/Name -->
	<p class="description description-wide">
		<label for="edit-form-item-name-<?php echo $field->field_id; ?>"><?php echo ( in_array( $field->field_type, array( 'fieldset', 'verification' ) ) ) ? 'Legend' : 'Name'; ?>
        	<span class="vfb-tooltip" rel="<?php esc_attr_e( 'For Fieldsets, a Legend is simply the name of that group. Use general terms that describe the fields included in this Fieldset.', 'visual-form-builder-pro' ); ?>" title="<?php esc_attr_e( 'About Legend', 'visual-form-builder-pro' ); ?>">(?)</span>
            <br />
			<input type="text" value="<?php echo stripslashes( esc_attr( $field->field_name ) ); ?>" name="field_name-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-name-<?php echo $field->field_id; ?>" maxlength="255" />
		</label>

	</p>

	<!-- CSS Classes -->
    <p class="description description-wide">
        <label for="edit-form-item-css-<?php echo $field->field_id; ?>">
            <?php _e( 'CSS Classes' , 'visual-form-builder-pro'); ?>
            <span class="vfb-tooltip" rel="<?php esc_attr_e( 'For each field, you can insert your own CSS class names which can be used in your own stylesheets.', 'visual-form-builder-pro' ); ?>" title="<?php esc_attr_e( 'About CSS Classes', 'visual-form-builder-pro' ); ?>">(?)</span>
            <br />
            <input type="text" value="<?php echo stripslashes( esc_attr( $field->field_css ) ); ?>" name="field_css-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-css-<?php echo $field->field_id; ?>" />
        </label>
    </p>

<?php elseif( $field->field_type == 'instructions' ) : ?>
	<!-- Instructions -->
	<p class="description description-wide">
		<label for="edit-form-item-name-<?php echo $field->field_id; ?>">
				<?php _e( 'Name' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Name', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( "A field's name is the most visible and direct way to describe what that field is for.", 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
				<input type="text" value="<?php echo stripslashes( esc_attr( $field->field_name ) ); ?>" name="field_name-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-name-<?php echo $field->field_id; ?>" maxlength="255" />
		</label>
	</p>

	<!-- Description -->
	<p class="description description-wide">
		<label for="edit-form-item-description-<?php echo $field->field_id; ?>">
			<?php _e( 'Description (HTML tags allowed)', 'visual-form-builder-pro' ); ?>
        	<span class="vfb-tooltip" title="<?php esc_attr_e( 'About Instructions Description', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'The Instructions field allows for long form explanations, typically seen at the beginning of Fieldsets or Sections. HTML tags are allowed.', 'visual-form-builder-pro' ); ?>">(?)</span>
            <br />
			<textarea name="field_description-<?php echo $field->field_id; ?>" class="widefat edit-menu-item-description" cols="20" rows="3" id="edit-form-item-description-<?php echo $field->field_id; ?>"><?php echo stripslashes( $field->field_description ); ?></textarea>
		</label>
	</p>

<?php elseif( $field->field_type == 'hidden' ) : ?>
	<!-- Hidden -->
	<p class="description description-wide">
		<label for="edit-form-item-name-<?php echo $field->field_id; ?>">
				<?php _e( 'Name' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Name', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( "A field's name is the most visible and direct way to describe what that field is for.", 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
				<input type="text" value="<?php echo stripslashes( esc_attr( $field->field_name ) ); ?>" name="field_name-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-name-<?php echo $field->field_id; ?>" maxlength="255" />
		</label>
	</p>

    <!-- Dynamic Variable -->
    <p class="description description-wide">
        <label for="edit-form-item-dynamicvar">
            <?php _e( 'Dynamic Variable' , 'visual-form-builder-pro'); ?>
            <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Dynamic Variable', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'A Dynamic Variable will use a pre-populated value that is determined either by the form, the user, or the post/page viewed.', 'visual-form-builder-pro' ); ?>">(?)</span>
           	<br />
            <?php
            $opts_vals = array();
			// If the options field isn't empty, unserialize and build array
			if ( !empty( $field->field_options ) ) {
				if ( is_serialized( $field->field_options ) )
					$opts_vals = unserialize( $field->field_options );
			}
			?>
           <select name="field_options-<?php echo $field->field_id; ?>[]" class="widefat hidden-option" id="edit-form-item-dynamicvar-<?php echo $field->field_id; ?>">
                <option value="" <?php selected( $opts_vals[0], '' ); ?>><?php _e( 'Select a Variable or Custom to create your own' , 'visual-form-builder-pro'); ?></option>
                <option value="form_id" <?php selected( $opts_vals[0], 'form_id' ); ?>><?php _e( 'Form ID' , 'visual-form-builder-pro'); ?></option>
                <option value="form_title" <?php selected( $opts_vals[0], 'form_title' ); ?>><?php _e( 'Form Title' , 'visual-form-builder-pro'); ?></option>
                <option value="ip" <?php selected( $opts_vals[0], 'ip' ); ?>><?php _e( 'IP Address' , 'visual-form-builder-pro'); ?></option>
                <option value="uid" <?php selected( $opts_vals[0], 'uid' ); ?>><?php _e( 'Unique ID' , 'visual-form-builder-pro'); ?></option>
                <option value="post_id" <?php selected( $opts_vals[0], 'post_id' ); ?>><?php _e( 'Post/Page ID' , 'visual-form-builder-pro'); ?></option>
                <option value="post_title" <?php selected( $opts_vals[0], 'post_title' ); ?>><?php _e( 'Post/Page Title' , 'visual-form-builder-pro'); ?></option>
                <option value="post_url" <?php selected( $opts_vals[0], 'post_url' ); ?>><?php _e( 'Post/Page URL' , 'visual-form-builder-pro'); ?></option>
                <option value="custom" <?php selected( $opts_vals[0], 'custom' ); ?>><?php _e( 'Custom' , 'visual-form-builder-pro'); ?></option>
            </select>
        </label>
    </p>

    <!-- Static Variable -->
    <p class="description description-wide static-vars-<?php echo ( $opts_vals[0] == 'custom' ) ? 'active' : 'inactive'; ?>" id="static-var-<?php echo $field->field_id; ?>">
		<label for="edit-form-item-staticvar-<?php echo $field->field_id; ?>">
			<?php _e( 'Static Variable' , 'visual-form-builder-pro'); ?>
            <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Static Variable', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'A Static Variable will always use the value that you enter.', 'visual-form-builder-pro' ); ?>">(?)</span>
           	<br />
			<input type="text" value="<?php echo stripslashes( esc_attr( $opts_vals[1] ) ); ?>" name="field_options-<?php echo $field->field_id; ?>[]" class="widefat" id="edit-form-item-staticvar-<?php echo $field->field_id; ?>"<?php echo ( $opts_vals[0] !== 'custom' ) ? ' disabled="disabled"' : ''; ?> />
		</label>
	</p>
    <?php unset( $opts_vals ); ?>

<?php else: ?>

	<!-- Name -->
	<p class="description description-wide">
		<label for="edit-form-item-name-<?php echo $field->field_id; ?>">
			<?php _e( 'Name' , 'visual-form-builder-pro'); ?>
            <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Name', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( "A field's name is the most visible and direct way to describe what that field is for.", 'visual-form-builder-pro' ); ?>">(?)</span>
            <br />
			<input type="text" value="<?php echo stripslashes( esc_attr( $field->field_name ) ); ?>" name="field_name-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-name-<?php echo $field->field_id; ?>" maxlength="255" />
		</label>
	</p>

	<?php if ( $field->field_type == 'submit' ) : ?>
		<!-- CSS Classes -->
        <p class="description description-wide">
            <label for="edit-form-item-css-<?php echo $field->field_id; ?>">
                <?php _e( 'CSS Classes' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" rel="<?php esc_attr_e( 'For each field, you can insert your own CSS class names which can be used in your own stylesheets.', 'visual-form-builder-pro' ); ?>" title="<?php esc_attr_e( 'About CSS Classes', 'visual-form-builder-pro' ); ?>">(?)</span>
                <br />
                <input type="text" value="<?php echo stripslashes( esc_attr( $field->field_css ) ); ?>" name="field_css-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-css-<?php echo $field->field_id; ?>" />
            </label>
        </p>

	<?php elseif ( $field->field_type !== 'submit' ) : ?>
		<!-- Description -->
		<p class="description description-wide">
			<label for="edit-form-item-description-<?php echo $field->field_id; ?>">
				<?php _e( 'Description' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Description', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'A description is an optional piece of text that further explains the meaning of this field. Descriptions are displayed below the field. HTML tags are allowed.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
            	<textarea name="field_description-<?php echo $field->field_id; ?>" class="widefat edit-menu-item-description" cols="20" rows="3" id="edit-form-item-description-<?php echo $field->field_id; ?>"><?php echo stripslashes( $field->field_description ); ?></textarea>
			</label>
		</p>

		<?php
			// Display the Options input only for radio, checkbox, select, and autocomplete fields
			if ( in_array( $field->field_type, array( 'radio', 'checkbox', 'select', 'autocomplete' ) ) ) :
		?>
			<!-- Options -->
			<p class="description description-wide">
				<?php _e( 'Options' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Options', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'This property allows you to set predefined options to be selected by the user.  Use the plus and minus buttons to add and delete options.  At least one option must exist.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
			<?php
				// If the options field isn't empty, unserialize and build array
				if ( !empty( $field->field_options ) ) {
					if ( is_serialized( $field->field_options ) )
						$opts_vals = ( is_array( unserialize( $field->field_options ) ) ) ? unserialize( $field->field_options ) : explode( ',', unserialize( $field->field_options ) );
				}
				// Otherwise, present some default options
				else
					$opts_vals = array( 'Option 1', 'Option 2', 'Option 3' );

				// Basic count to keep track of multiple options
				$count = 1;

				// Loop through the options
				foreach ( $opts_vals as $options ) {
			?>
			<div id="clone-<?php echo $field->field_id . '-' . $count; ?>" class="option">
					<input type="radio" value="<?php echo esc_attr( $count ); ?>" name="field_default-<?php echo $field->field_id; ?>" <?php checked( $field->field_default, $count ); ?> <?php echo ( $field->field_type == 'autocomplete' ) ? 'disabled="disabled"' : ''; ?> />
<label for="edit-form-item-options-<?php echo $field->field_id . "-$count"; ?>" class="clonedOption">
					<input type="text" value="<?php echo stripslashes( esc_attr( $options ) ); ?>" name="field_options-<?php echo $field->field_id; ?>[]" class="widefat" id="edit-form-item-options-<?php echo $field->field_id . "-$count"; ?>" />
				</label>

				<a href="#" class="addOption" title="<?php esc_attr_e( 'Add an Option', 'visual-form-builder-pro' ); ?>"><?php _e( 'Add', 'visual-form-builder-pro' ); ?></a> <a href="#" class="deleteOption" title="<?php esc_attr_e( 'Delete Option', 'visual-form-builder-pro' ); ?>"><?php _e( 'Delete', 'visual-form-builder-pro' ); ?></a>
			</div>
			   <?php
					$count++;
				}
				?>
			<div class="clear"></div>
			<a href="<?php echo add_query_arg( array( 'action' => 'visual_form_builder_bulk_add', 'field_id' => $field->field_id, 'width' => '640' ), admin_url( 'admin-ajax.php' ) ); ?>" class="thickbox vfb-bulking" title="Bulk Add Options"><?php _e( 'Bulk Add Options', 'visual-form-builder-pro' ); ?></a>
			</p>
		<?php
			// Unset the options for any following radio, checkboxes, or selects
			unset( $opts_vals );
			endif;
		?>

        <?php if ( in_array( $field->field_type, array( 'radio' ) ) ) : ?>
			<!-- Allow Other -->
			<p class="description description-wide">
			<?php $field_options_other = maybe_unserialize( $field->field_options_other ); ?>
				<label for="edit-form-item-options-other-<?php echo $field->field_id; ?>">
					<span class="vfb-tooltip" title="<?php esc_attr_e( 'About Allow Other', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Select this option if you want the last option to be a text field.', 'visual-form-builder-pro' ); ?>">(?)</span>

					<input type="checkbox" value="1" name="field_options_other-<?php echo $field->field_id; ?>[setting]" class="vfb-options-other" id="edit-form-item-options-other-<?php echo $field->field_id; ?>"<?php echo ( isset( $field_options_other['setting'] ) && 1 == $field_options_other['setting'] ) ? 'checked="checked"' : ''; ?> /> <?php _e( 'Allow Other', 'visual-form-builder-pro' ); ?>
				</label>
				<?php
					$other_display 	= ( isset( $field_options_other['setting'] ) && 1 == $field_options_other['setting'] ) ? 'show' : 'hide';
					$other_value	= ( isset( $field_options_other['other'] ) && !empty( $field_options_other['other'] ) ) ? $field_options_other['other'] : 'Other';
				?>
						<div id="options-other-<?php echo $field->field_id; ?>" class="options-other-<?php echo $other_display; ?>">
						<input type="radio" value="<?php echo esc_attr( $count ); ?>" name="field_default-<?php echo $field->field_id; ?>" <?php checked( $field->field_default, $count ); ?> />
<label for="edit-form-item-other-options-<?php echo $field->field_id; ?>">
						<input type="text" value="<?php echo stripslashes( esc_attr( $other_value ) ); ?>" name="field_options_other-<?php echo $field->field_id; ?>[other]" class="widefat" id="edit-form-item-other-options-<?php echo $field->field_id; ?>" />
					</label>
				</div>
			</p>
		<?php endif; ?>

        <?php if ( in_array( $field->field_type, array( 'textarea' ) ) ) : ?>
        	<!-- Textarea word count -->
			<p class="description description-thin">
				<?php
					$opts_vals = maybe_unserialize( $field->field_options );
					$min = ( isset( $opts_vals['min'] ) ) ? $opts_vals['min'] : '';
					$max = ( isset( $opts_vals['max'] ) ) ? $opts_vals['max'] : '';
				?>
				<label for="edit-form-item-textarea-min-<?php echo $field->field_id; ?>">
					<?php _e( 'Minimum Words', 'visual-form-builder-pro' ); ?>
					<span class="vfb-tooltip" title="<?php esc_attr_e( 'About Minimum Word Count', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Set a minimum number of words allowed in this field. For an unlimited number, leave blank or set to zero.', 'visual-form-builder-pro' ); ?>">(?)</span>
					<br />
					<input type="text" value="<?php echo esc_attr( $min ); ?>" name="field_options-<?php echo $field->field_id; ?>[min]" class="widefat" id="edit-form-item-textarea-min-<?php echo $field->field_id; ?>" />
				</label>
            </p>
            <p class="description description-thin">
            	<label for="edit-form-item-textarea-max-<?php echo $field->field_id; ?>">
            		<?php _e( 'Maximum Words', 'visual-form-builder-pro' ); ?>
            		<span class="vfb-tooltip" title="<?php esc_attr_e( 'About Maximum Word Count', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Set a maximum number of words allowed in this field. For an unlimited number, leave blank or set to zero.', 'visual-form-builder-pro' ); ?>">(?)</span>
            		<br />
					<input type="text" value="<?php echo esc_attr( $max ); ?>" name="field_options-<?php echo $field->field_id; ?>[max]" class="widefat" id="edit-form-item-textarea-max-<?php echo $field->field_id; ?>" />
				</label>
            </p>
        <?php
			// Unset the options for any following radio, checkboxes, or selects
			unset( $opts_vals );
			endif;
		?>

        <?php if ( in_array( $field->field_type, array( 'min', 'max', 'range' ) ) ) : ?>
        	<!-- Min, Max, and Range -->
			<p class="description description-wide">
                <?php
				if ( 'min' == $field->field_type )
					_e( 'Minimum Value' , 'visual-form-builder-pro');
				elseif ( 'max' == $field->field_type )
					_e( 'Maximum Value' , 'visual-form-builder-pro');

				// If the options field isn't empty, unserialize and build array
				if ( !empty( $field->field_options ) ) {
					if ( is_serialized( $field->field_options ) )
						$opts_vals = ( is_array( unserialize( $field->field_options ) ) ) ? unserialize( $field->field_options ) : explode( ',', unserialize( $field->field_options ) );
				}
				else
					$opts_vals = ( in_array( $field->field_type, array( 'min', 'max' ) ) ) ? array( '10' ) : array( '1', '10' );

				$ranged = false;
				// Loop through the options
				foreach ( $opts_vals as $options ) {
					if ( 'range' == $field->field_type ) {
						if ( !$ranged )
							_e( 'Minimum Value' , 'visual-form-builder-pro');
						else
							_e( 'Maximum Value' , 'visual-form-builder-pro');

						$ranged = true;
					}
			?>
            	<span class="vfb-tooltip" title="<?php esc_attr_e( 'About Minimum/Maxium Value', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Set a minimum and/or maximum value users must enter in order to successfully complete the field.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
				<label for="edit-form-item-options-<?php echo $field->field_id; ?>">
					<input type="text" value="<?php echo stripslashes( esc_attr( $options ) ); ?>" name="field_options-<?php echo $field->field_id; ?>[]" class="widefat" id="edit-form-item-options-<?php echo $field->field_id . "-$count"; ?>" />
				</label>
			   <?php
				}
				?>
            </p>
        <?php
			// Unset the options for any following radio, checkboxes, or selects
			unset( $opts_vals );
			endif;
		?>

        <?php if ( in_array( $field->field_type, array( 'file-upload' ) ) ) : ?>
        	<!-- File Upload Accepts -->
			<p class="description description-wide">
            	<?php _e( 'Accepted File Extensions' , 'visual-form-builder-pro'); ?>
                <?php
				$opts_vals = array( '' );

				// If the options field isn't empty, unserialize and build array
				if ( !empty( $field->field_options ) ) {
					if ( is_serialized( $field->field_options ) )
						$opts_vals = ( is_array( unserialize( $field->field_options ) ) ) ? unserialize( $field->field_options ) : unserialize( $field->field_options );
				}

				// Loop through the options
				foreach ( $opts_vals as $options ) {
			?>
            	<span class="vfb-tooltip" title="<?php esc_attr_e( 'About Accepted File Extensions', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Control the types of files allowed.  Enter file extentsions separated by commas.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
				<label for="edit-form-item-options-<?php echo $field->field_id; ?>">
					<input type="text" value="<?php echo stripslashes( esc_attr( $options ) ); ?>" name="field_options-<?php echo $field->field_id; ?>[]" class="widefat" id="edit-form-item-options-<?php echo $field->field_id; ?>" />
				</label>
            </p>
        <?php
				}
			// Unset the options for any following radio, checkboxes, or selects
			unset( $opts_vals );
			endif;
		?>

		<?php if ( in_array( $field->field_type, array( 'name' ) ) ) : ?>
        	<!-- Name -->
			<p class="description description-wide">
				<label for="edit-form-item-options">
            		<?php _e( 'Name Format' , 'visual-form-builder-pro'); ?>
            		<?php
            			$opts_vals = maybe_unserialize( $field->field_options );
            		?>
            		<span class="vfb-tooltip" title="<?php esc_attr_e( 'About Name Format', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Choose from either a simple name format with only a First and Last Name or a more complex format that adds a Title and Suffix.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
            	<select name="field_options-<?php echo $field->field_id; ?>[]" class="widefat" id="edit-form-item-options-<?php echo $field->field_id; ?>">
            		<option value="normal" <?php selected( $opts_vals[0], 'normal' ); ?>><?php _e( 'Normal' , 'visual-form-builder-pro'); ?></option>
					<option value="extra" <?php selected( $opts_vals[0], 'extra' ); ?>><?php _e( 'Extra' , 'visual-form-builder-pro'); ?></option>
            	</select>
				</label>
			</p>
		<?php
			// Unset the options for any following radio, checkboxes, or selects
			unset( $opts_vals );
			endif;
		?>

		<?php if ( in_array( $field->field_type, array( 'date' ) ) ) : ?>
        	<!-- Date Format -->
			<p class="description description-wide">
				<?php
					$opts_vals = maybe_unserialize( $field->field_options );
					$dateFormat = ( isset( $opts_vals['dateFormat'] ) ) ? $opts_vals['dateFormat'] : 'mm/dd/yy';
					//$max = ( isset( $opts_vals['max'] ) ) ? $opts_vals['max'] : '';
				?>
				<label for="edit-form-item-date-dateFormat-<?php echo $field->field_id; ?>">
					<?php _e( 'Date Format', 'visual-form-builder-pro' ); ?>
					<span class="vfb-tooltip" title="<?php esc_attr_e( 'About Date Format', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Set the date format for each date picker.', 'visual-form-builder-pro' ); ?>">(?)</span>
					<br />
					<input type="text" value="<?php echo esc_attr( $dateFormat ); ?>" name="field_options-<?php echo $field->field_id; ?>[dateFormat]" class="widefat" id="edit-form-item-date-dateFormat-<?php echo $field->field_id; ?>" />
				</label>
            </p>
		<?php
			// Unset the options for any following radio, checkboxes, or selects
			unset( $opts_vals );
			endif;
		?>

		<?php if ( class_exists( 'VFB_Pro_Create_Post' ) && in_array( $field->field_type, array( 'custom-field' ) ) ) : ?>
        	<!-- Create Post - Custom Field -->
			<p class="description description-wide">
            	<?php _e( 'Custom Field Key' , 'visual-form-builder-pro'); ?>
                <?php
				$opts_vals = maybe_unserialize( $field->field_options );

				// Loop through the options
				foreach ( $opts_vals as $options ) {
			?>
            	<span class="vfb-tooltip" title="<?php esc_attr_e( 'About Custom Field Key', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Enter a new or existing meta key. Data entered will be inserted as a Custom Field into the post created.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
				<label for="edit-form-item-options-<?php echo $field->field_id; ?>">
					<input type="text" value="<?php echo stripslashes( esc_attr( $options ) ); ?>" name="field_options-<?php echo $field->field_id; ?>[]" class="widefat" id="edit-form-item-options-<?php echo $field->field_id; ?>" />
				</label>
            </p>
        <?php
				}
			// Unset the options for any following radio, checkboxes, or selects
			unset( $opts_vals );
			endif;
		?>

		<!-- Validation -->
		<p class="description description-thin">
			<label for="edit-form-item-validation">
				<?php _e( 'Validation' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Validation', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Ensures user-entered data is formatted properly. For more information on Validation, refer to the Help tab at the top of this page.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
			   <?php if ( in_array( $field->field_type, array( 'text', 'time', 'ip-address', 'min', 'max', 'range', 'number' ) ) ) : ?>
			   		<select name="field_validation-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-validation-<?php echo $field->field_id; ?>">
				   		<?php if ( $field->field_type == 'time' ) : ?>
						<option value="time-12" <?php selected( $field->field_validation, 'time-12' ); ?>><?php _e( '12 Hour Format' , 'visual-form-builder-pro'); ?></option>
						<option value="time-24" <?php selected( $field->field_validation, 'time-24' ); ?>><?php _e( '24 Hour Format' , 'visual-form-builder-pro'); ?></option>
						<?php elseif ( $field->field_type == 'ip-address' ) : ?>
                        <option value="ipv4" <?php selected( $field->field_validation, 'ipv4' ); ?>><?php _e( 'IPv4' , 'visual-form-builder-pro'); ?></option>
                        <option value="ipv6" <?php selected( $field->field_validation, 'ipv6' ); ?>><?php _e( 'IPv6' , 'visual-form-builder-pro'); ?></option>
						<?php elseif ( in_array( $field->field_type, array( 'min', 'max', 'range', 'number' ) ) ) : ?>
                        <option value="number" <?php selected( $field->field_validation, 'number' ); ?>><?php _e( 'Number' , 'visual-form-builder-pro'); ?></option>
						<option value="digits" <?php selected( $field->field_validation, 'digits' ); ?>><?php _e( 'Digits' , 'visual-form-builder-pro'); ?></option>
						<?php else : ?>
						<option value="" <?php selected( $field->field_validation, '' ); ?>><?php _e( 'None' , 'visual-form-builder-pro'); ?></option>
						<option value="email" <?php selected( $field->field_validation, 'email' ); ?>><?php _e( 'Email' , 'visual-form-builder-pro'); ?></option>
						<option value="url" <?php selected( $field->field_validation, 'url' ); ?>><?php _e( 'URL' , 'visual-form-builder-pro'); ?></option>
						<option value="date" <?php selected( $field->field_validation, 'date' ); ?>><?php _e( 'Date' , 'visual-form-builder-pro'); ?></option>
						<option value="number" <?php selected( $field->field_validation, 'number' ); ?>><?php _e( 'Number' , 'visual-form-builder-pro'); ?></option>
						<option value="digits" <?php selected( $field->field_validation, 'digits' ); ?>><?php _e( 'Digits' , 'visual-form-builder-pro'); ?></option>
						<option value="phone" <?php selected( $field->field_validation, 'phone' ); ?>><?php _e( 'Phone' , 'visual-form-builder-pro'); ?></option>
						<?php endif; ?>
			   		</select>
			   <?php else :
				   $field_validation = '';

				   switch ( $field->field_type ) {
					    case 'email' :
						case 'url' :
						case 'phone' :
							$field_validation = $field->field_type;
						break;

						case 'currency' :
							$field_validation = 'number';
						break;

						case 'number' :
							$field_validation = 'digits';
						break;
				   }

			   ?>
			   <input type="text" class="widefat" name="field_validation-<?php echo $field->field_id; ?>" value="<?php echo $field_validation; ?>" readonly="readonly" />
			   <?php endif; ?>
			</label>
		</p>

		<!-- Required -->
		<p class="field-link-target description description-thin">
			<label for="edit-form-item-required">
				<?php _e( 'Required' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Required', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Requires the field to be completed before the form is submitted. By default, all fields are set to No.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
				<select name="field_required-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-required-<?php echo $field->field_id; ?>">
					<option value="no" <?php selected( $field->field_required, 'no' ); ?>><?php _e( 'No' , 'visual-form-builder-pro'); ?></option>
					<option value="yes" <?php selected( $field->field_required, 'yes' ); ?>><?php _e( 'Yes' , 'visual-form-builder-pro'); ?></option>
				</select>
			</label>
		</p>

		<?php if ( !in_array( $field->field_type, array( 'radio', 'checkbox', 'time' ) ) ) : ?>
			<!-- Size -->
			<p class="description description-thin">
				<label for="edit-form-item-size">
					<?php _e( 'Size' , 'visual-form-builder-pro'); ?>
                    <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Size', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Control the size of the field.  By default, all fields are set to Medium.', 'visual-form-builder-pro' ); ?>">(?)</span>
            		<br />
					<select name="field_size-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-size-<?php echo $field->field_id; ?>">
						<option value="small" <?php selected( $field->field_size, 'small' ); ?>><?php _e( 'Small' , 'visual-form-builder-pro'); ?></option>
                        <option value="medium" <?php selected( $field->field_size, 'medium' ); ?>><?php _e( 'Medium' , 'visual-form-builder-pro'); ?></option>
						<option value="large" <?php selected( $field->field_size, 'large' ); ?>><?php _e( 'Large' , 'visual-form-builder-pro'); ?></option>
						<?php apply_filters( 'vfb_admin_field_size', $field->field_size ); ?>
					</select>
				</label>
			</p>
        <?php elseif ( in_array( $field->field_type, array( 'radio', 'checkbox', 'time' ) ) ) : ?>
			<!-- Options Layout -->
			<p class="description description-thin">
				<label for="edit-form-item-size">
					<?php _e( 'Options Layout' , 'visual-form-builder-pro'); ?>
                    <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Options Layout', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Control the layout of radio buttons or checkboxes.  By default, options are arranged in One Column.', 'visual-form-builder-pro' ); ?>">(?)</span>
            		<br />
					<select name="field_size-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-size-<?php echo $field->field_id; ?>"<?php echo ( $field->field_type == 'time' ) ? ' disabled="disabled"' : ''; ?>>
						<option value="" <?php selected( $field->field_size, '' ); ?>><?php _e( 'One Column' , 'visual-form-builder-pro'); ?></option>
                        <option value="two-column" <?php selected( $field->field_size, 'two-column' ); ?>><?php _e( 'Two Columns' , 'visual-form-builder-pro'); ?></option>
						<option value="three-column" <?php selected( $field->field_size, 'three-column' ); ?>><?php _e( 'Three Columns' , 'visual-form-builder-pro'); ?></option>
                        <option value="auto-column" <?php selected( $field->field_size, 'auto-column' ); ?>><?php _e( 'Auto Width' , 'visual-form-builder-pro'); ?></option>
					</select>
				</label>
			</p>
		<?php endif; ?>

			<!-- Field Layout -->
			<p class="description description-thin">
				<label for="edit-form-item-layout">
					<?php _e( 'Field Layout' , 'visual-form-builder-pro'); ?>
                    <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Field Layout', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Used to create advanced layouts. Align fields side by side in various configurations.', 'visual-form-builder-pro' ); ?>">(?)</span>
                <br />
					<select name="field_layout-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-layout-<?php echo $field->field_id; ?>">

						<option value="" <?php selected( $field->field_layout, '' ); ?>><?php _e( 'Default' , 'visual-form-builder-pro'); ?></option>
                        <optgroup label="------------">
                        <option value="left-half" <?php selected( $field->field_layout, 'left-half' ); ?>><?php _e( 'Left Half' , 'visual-form-builder-pro'); ?></option>
                        <option value="right-half" <?php selected( $field->field_layout, 'right-half' ); ?>><?php _e( 'Right Half' , 'visual-form-builder-pro'); ?></option>
                        </optgroup>
                        <optgroup label="------------">
						<option value="left-third" <?php selected( $field->field_layout, 'left-third' ); ?>><?php _e( 'Left Third' , 'visual-form-builder-pro'); ?></option>
                        <option value="middle-third" <?php selected( $field->field_layout, 'middle-third' ); ?>><?php _e( 'Middle Third' , 'visual-form-builder-pro'); ?></option>
                        <option value="right-third" <?php selected( $field->field_layout, 'right-third' ); ?>><?php _e( 'Right Third' , 'visual-form-builder-pro'); ?></option>
                        </optgroup>
                        <optgroup label="------------">
                        <option value="left-two-thirds" <?php selected( $field->field_layout, 'left-two-thirds' ); ?>><?php _e( 'Left Two Thirds' , 'visual-form-builder-pro'); ?></option>
                        <option value="right-two-thirds" <?php selected( $field->field_layout, 'right-two-thirds' ); ?>><?php _e( 'Right Two Thirds' , 'visual-form-builder-pro'); ?></option>
                        </optgroup>
                        <?php apply_filters( 'vfb_admin_field_layout', $field->field_layout ); ?>
					</select>
				</label>
			</p>

		<?php if ( !in_array( $field->field_type, array( 'radio', 'select', 'checkbox', 'time', 'address' ) ) ) : ?>
		<!-- Default Value -->
		<p class="description description-wide">
            <label for="edit-form-item-default-<?php echo $field->field_id; ?>">
                <?php _e( 'Default Value' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Default Value', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Set a default value that will be inserted automatically.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
                <input type="text" value="<?php echo stripslashes( esc_attr( $field->field_default ) ); ?>" name="field_default-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-default-<?php echo $field->field_id; ?>" maxlength="255" />
            </label>
		</p>
		<?php elseif( in_array( $field->field_type, array( 'address' ) ) ) : ?>
		<!-- Default Country -->
		<p class="description description-wide">
            <label for="edit-form-item-default-<?php echo $field->field_id; ?>">
                <?php _e( 'Default Country' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About Default Country', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'Select the country you would like to be displayed by default.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
                <select name="field_default-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-default-<?php echo $field->field_id; ?>">
                <?php
                foreach ( $this->countries as $country ) {
					echo '<option value="' . esc_attr( $country ) . '" ' . selected( $field->field_default, $country, 0 ) . '>' . $country . '</option>';
				}
				?>
				</select>
            </label>
		</p>
		<?php endif; ?>
		<!-- CSS Classes -->
		<p class="description description-wide">
            <label for="edit-form-item-css-<?php echo $field->field_id; ?>">
                <?php _e( 'CSS Classes' , 'visual-form-builder-pro'); ?>
                <span class="vfb-tooltip" title="<?php esc_attr_e( 'About CSS Classes', 'visual-form-builder-pro' ); ?>" rel="<?php esc_attr_e( 'For each field, you can insert your own CSS class names which can be used in your own stylesheets.', 'visual-form-builder-pro' ); ?>">(?)</span>
            	<br />
                <input type="text" value="<?php echo stripslashes( esc_attr( $field->field_css ) ); ?>" name="field_css-<?php echo $field->field_id; ?>" class="widefat" id="edit-form-item-css-<?php echo $field->field_id; ?>" maxlength="255" />
            </label>
		</p>
	<?php endif; ?>
<?php endif; ?>

<?php if ( !in_array( $field->field_type, array( 'fieldset', 'section', 'page-break', 'verification' ) ) ) : ?>
	<!-- Merge Tag -->
	<div class="vfb-item-merge-tag description description-wide">
		<p id="edit-form-item-merge-tag-<?php echo $field->field_id; ?>" class="vfb-merge-tag">Merge Tag: <code>{vfb-<?php echo $field->field_id; ?>}</code></p>
	</div>
<?php endif; ?>

<?php if ( !in_array( $field->field_type, array( 'verification', 'secret', 'submit' ) ) ) : ?>
	<div class="vfb-item-actions">
		<!-- Delete link -->
		<a href="<?php echo esc_url( wp_nonce_url( admin_url('admin.php?page=visual-form-builder-pro&amp;action=delete_field&amp;form=' . $form_nav_selected_id . '&amp;field=' . $field->field_id ), 'delete-field-' . $form_nav_selected_id ) ); ?>" class="vfb-button vfb-delete item-delete submitdelete deletion"><?php _e( 'Delete' , 'visual-form-builder-pro'); ?><span class="button-icon delete"></span></a>

		<?php if ( !in_array( $field->field_type, array( 'fieldset', 'section' ) ) ) { ?>
		<!-- Duplicate Field link -->
		<a href="<?php echo esc_url( wp_nonce_url( admin_url('admin.php?page=visual-form-builder-pro&amp;action=duplicate_field&amp;form=' . $form_nav_selected_id . '&amp;field=' . $field->field_id ), 'duplicate-field-' . $form_nav_selected_id ) ); ?>" class="vfb-button vfb-duplicate vfb-duplicate-field" title="Duplicate Field"><?php _e( 'Duplicate' , 'visual-form-builder-pro'); ?><span class="button-icon plus"></span></a>
		<?php } ?>

		<!-- Conditional Logic link -->
		<a href="<?php echo add_query_arg( array( 'action' => 'visual_form_builder_conditional_fields', 'field_id' => $field->field_id, 'form_id' => $form_nav_selected_id, 'width' => '640' ), admin_url( 'admin-ajax.php' ) ); ?>" class="vfb-button thickbox vfb-conditional-fields" title="Add Conditions"><?php _e( 'Conditional Logic' , 'visual-form-builder-pro'); ?><span class="button-icon conditional"></span></a>
	</div>
<?php endif; ?>

<input type="hidden" name="field_id[<?php echo $field->field_id; ?>]" value="<?php echo $field->field_id; ?>" />
</div>
<?php
endforeach;

// This assures all of the <ul> and <li> are closed
if ( $depth > 1 ) {
	while( $depth > 1 ) {
		echo '</li></ul>';
		$depth--;
	}
}

// Close out last item
echo '</li>';
echo ob_get_clean();