<?php 
 /* @param string $meta Meta name.	 
 * @param array $details Contains the details for the field.	 
 * @param string $value Contains input value;
 * @param string $context Context where the function is used. Depending on it some actions are preformed.;
 * @return string $element input element html string. */
 
$element .= '<textarea name="'. esc_attr( sanitize_title_with_dashes( remove_accents( $details['title'] ) ) ) .'" style="vertical-align:top;width:400px;height:200px" class="mb-textarea mb-field '. esc_attr( sanitize_title_with_dashes( remove_accents( $details['title'] ) ) ) .'">'. esc_html( $value ) .'</textarea>'; 			
$element .= '<script type="text/javascript">jQuery( function(){ wckInitTinyMCE("'. sanitize_title_with_dashes( remove_accents( $details['title'] ) ) .'")});</script>';
?>