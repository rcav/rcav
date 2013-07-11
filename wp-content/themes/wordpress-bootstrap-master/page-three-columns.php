<?php
/*
Template Name: Three-Columns Page
*/
?>
 get_header(); ?>



			<div id="content" class="clearfix row-fluid">

						<div class="sidebar-responsive-wrapper hidden-phone left-sidebar">
							<?php echo get_sidebar('sidebar4'); ?>
						</div>

    				
				<div id="main" class="span6 clearfix" role="main">


				<div class="page-visualbreak"></div>
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
									
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						

						<header>
							<?php the_post_thumbnail( 'wpbs-featured' ); ?>
							
							<div class="page-header"><h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1></div>
							
						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">

							<?php the_content(); ?>
							
							<?php wp_link_pages(); ?>
					
						</section> <!-- end article section -->
						
	
							<?php 
							// only show edit button if user has permission to edit posts
							if( $user_level > 0 ) { 
							?>
							<a href="<?php echo get_edit_post_link(); ?>" class="btn btn-success edit-post"><i class="icon-pencil icon-white"></i> <?php _e("Edit post","bonestheme"); ?></a>
							<?php } ?>
							
					
					</article> <!-- end article -->
					
					<?php comments_template('',true); ?>
					
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
    

						<div class="sidebar-responsive-wrapper hidden-phone right-sidebar">
							<?php echo get_sidebar('sidebar1'); ?>
						</div>

					
				
    
			</div> <!-- end #content -->

<?php get_footer(); ?>