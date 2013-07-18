<?php get_header(); ?>
			
			<div id="content" class="clearfix row-fluid">
			
				<div id="main" class="span12 clearfix" role="main">

					<article id="post-not-found" class="clearfix">
						
						<header>

							<div class="hero-unit">
						
								<p><?php _e("The page you are looking for cannot be found, please try using the search below.","bonestheme"); ?></p>
								<?php get_search_form(); ?>
															
							</div>
													
						</header> <!-- end article header -->
					
						
						<footer>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
			
				</div> <!-- end #main -->
    
			</div> <!-- end #content -->

<?php get_footer(); ?>