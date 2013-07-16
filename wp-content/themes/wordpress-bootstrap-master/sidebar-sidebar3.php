<div class="sidebar-homepage fluid-sidebar sidebar" role="complementary">
				
		<?php
		if ( ! acf_Widget::dynamic_widgets( 'sidebar3' ) ) {

		   //fallback to default function if you like
		   dynamic_sidebar( 'sidebar3' );
		}
		?>
</div>