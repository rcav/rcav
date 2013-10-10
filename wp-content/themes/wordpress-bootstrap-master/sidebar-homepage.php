				<div id="sidebar-homepage" class="sidebar span12" role="complementary">
				
<?php
if ( ! acf_Widget::dynamic_widgets( 'sidebar2' ) ) {

   //fallback to default function if you like
   dynamic_sidebar( 'sidebar1' );

}
?>


				</div>
