<?php
/*
Template Name: Homepage
*/
?>

<?php get_header(); ?>
			
			<div id="content" class="clearfix row-fluid">
			
				<div id="main" class="span12 clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">

						<section class="row-fluid post_content top-unit">
						
							<div class="span9" id="homepage-slider">
						
									<?php 
										$post_object = get_field('homepage_selected_slider');
											if( $post_object ): 
												// override $post
												$post = $post_object;
												setup_postdata( $post ); 
													echo do_shortcode('[metaslider id=' . $post->ID . ']');
										 		wp_reset_postdata(); 
									endif; ?>
						
							</div>

							<div class="homepage-main-cta">
								<div class="block-cta">
									<div class="block-cta-inner">
									<h4 class="primary-cta-title"><?php echo the_field('primary_cta_title'); ?></h4>
									<span class="primary-cta-body"><?php echo the_field('homepage_main_cta'); ?></span>
									</div>
								</div>
								<div class="block-cta-mass-finder hidden-phone">
									<a href="<?php echo home_url(); ?>/mass-finder"></a>
								</div>
							</div>
													
						</section> <!-- end article header -->
						
						<section class="row-fluid post_content" id="editorial-blocks">
						
							<div class="span12">					
								<?php get_sidebar('sidebar2');  ?>						
							</div>
													
						</section> <!-- end article header -->
					
						<section class="row-fluid post_content" id="generated-blocks">
						
							<div class="span9">
								<?php get_sidebar('sidebar3');  ?>						
							</div>

							<div class="span3">
							
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

		<script type="text/javascript"> 
			//4th widget in a new row has no left margin
			jQuery('.widget').eq(3).addClass('secondrow-first-child');
			jQuery('.widget').eq(5).addClass('secondrow-last-child');
			//jQuery('.widget').eq(3).wrap('<div class="widget-3" />');
			//jQuery('.widget').eq(4).wrap('<div class="widget-4" />');
			//jQuery('.widget').eq(5).wrap('<div class="widget-5" />');
			
		</script>

<?php get_footer(); ?>