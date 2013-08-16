<div id="sidebar5" class="fluid-sidebar sidebar span12 oyyam-blocks" role="complementary">
				
		<?php
		if ( ! acf_Widget::dynamic_widgets( 'sidebar5' ) ) {

		   //fallback to default function if you like
		   dynamic_sidebar( 'sidebar5' );
		}
		?>
</div>