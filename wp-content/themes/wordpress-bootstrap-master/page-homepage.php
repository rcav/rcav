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

							<div class="homepage-main-cta span4" style="margin-left: 0 !important">
								<div class="block-cta">
									<h4 class="primary-cta-title"><?php echo the_field('primary_cta_title'); ?></h4>
									<span class="primary-cta-body"><?php echo the_field('homepage_main_cta'); ?></span>
								</div>
								<div class="block-cta-mass-finder ">
									<a href="<?php echo home_url() .'/mass-finder'?>" class="hidden-phone"></a>
								</div>
							</div>
													
						</section> <!-- end article header -->
						
						<section class="row-fluid post_content" id="editorial-blocks">
						
							<div class="span12">					
								<?php get_sidebar('sidebar2');  ?>						
							</div>
													
						</section> <!-- end article header -->
					
						<section class="row-fluid post_content" id="generated-blocks">
						
							<div class="span12">
								<?php get_sidebar('sidebar3');  ?>						
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
			
			// editorial blocks
			jQuery('#editorial-blocks .widget').eq(0).addClass('well first-block');
			jQuery('#editorial-blocks .widget').eq(1).addClass('well second-block');
			jQuery('#editorial-blocks .widget').eq(2).addClass('well third-block');
			jQuery('#editorial-blocks .widget').eq(3).addClass('well fourth-block');
			jQuery('#editorial-blocks .widget').eq(4).addClass('well fifth-block');
			jQuery('#editorial-blocks .widget').eq(5).addClass('well sixth-block');

			// generated blocks row			
			jQuery('#generated-blocks .widget').eq(0).addClass('well span6');
			jQuery('#generated-blocks .widget').eq(1).addClass('well span4 offset2');
		</script>

<?php get_footer(); ?>