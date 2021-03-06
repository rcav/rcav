<?php
/**
 * The Template for displaying all single posts of custom post type 'article'.
 **/

 get_header(); ?>
			

<?php 

$selected_template = get_field('template_select');
if($selected_template =='') {
	$selected_template = 'rsidebar';

}
?>	


			<div id="content" class="clearfix row-fluid">

    				
					<?php 
					// check if left sidebar
					if(is_array($selected_template) && in_array("lsidebar", $selected_template) || $selected_template =='lsidebar') {
						echo '<div class="sidebar-responsive-wrapper hidden-phone left-sidebar">';
						get_sidebar('sidebar4'); 
						echo '</div>';
					} ?>
					

				<?php 
					// check if full width
					if (is_array($selected_template) && in_array("fullwidth", $selected_template)) { ?>
 						<div id="main" class="span12 clearfix" role="main">
					<?php } else if (in_array("lsidebar", $selected_template) && in_array("rsidebar", $selected_template)) { ?>
						<div id="main" class="span6 clearfix" role="main">
					<?php } else { ?>
						<div id="main" class="span9 clearfix" role="main">
					<?php } ?>


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
    

					<?php 
					// check if right sidebar
					if(is_array($selected_template) && in_array("rsidebar", $selected_template) || $selected_template =='rsidebar') {
						echo '<div class="sidebar-responsive-wrapper hidden-phone right-sidebar">';
						get_sidebar('sidebar1'); 
						echo '</div>';
					} ?>

					
				
    
			</div> <!-- end #content -->

<?php get_footer(); ?>