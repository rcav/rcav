<div id="sidebar4" class="fluid-sidebar sidebar span3" role="complementary">
				
		<?php
		if ( ! acf_Widget::dynamic_widgets( 'sidebar4' ) ) {

		   //fallback to default function if you like
		   dynamic_sidebar( 'sidebar4' );
		}
		?>
</div>