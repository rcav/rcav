<?php
/*
Template Name: OYYAM 
*/
?>

<?php get_header(); ?>
			
			<div id="content" class="clearfix row-fluid">
			
				<div id="main" class="span12 clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">

						<section class="row-fluid">
						
							<div class="span9">
						
									<?php 
										$post_object = get_field('oyyam_slideshow_selector');
											if( $post_object ): 
												// override $post
												$post = $post_object;
												setup_postdata( $post ); 
													echo do_shortcode('[metaslider id=' . $post->ID . ']');
										 		wp_reset_postdata(); 
									endif; ?>
						
							</div>

							<div class="oyyam-main-cta">
								<span>
								<?php echo get_field('oyyam_whats_new'); ?>
								</span>
							</div>
													
						</section> <!-- end article header -->
						
						<section class="row-fluid" id="editorial-blocks">
						
							<div class="span12">					
								<?php //get_sidebar('sidebar5');  ?>						
							</div>
													
						</section> <!-- end article header -->
					

					</article> <!-- end article -->
					
					
					<?php endwhile; ?>	
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1><?php _e("Not Found", "bonestheme"); ?></h1>
					    </header>
					    <section class="post_content">
					    	<p><?php _e("Sorry, but the requested resource was not found on this site.", "bonestheme"); ?></p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
    
    
			</div> <!-- end #content -->


<?php get_footer(); ?>