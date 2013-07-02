<div id="sidebar-homepage" class="fluid-sidebar sidebar span12 rss-feed" role="complementary">
				
		<?php
		if ( ! acf_Widget::dynamic_widgets( 'sidebar3' ) ) {

		   //fallback to default function if you like
		   dynamic_sidebar( 'sidebar3' );
		}
		?>
</div>