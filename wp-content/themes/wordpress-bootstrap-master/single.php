<?php
/**
 * The Template for displaying all single posts of custom post type 'article'.
 **/

 get_header(); ?>
			

<?php 

$selected_template = get_field('template_select');
if($selected_template == NULL) {
	$selected_template = 'rsidebar';

}
?>	


			<div id="content" class="clearfix row-fluid">

    				
					<?php 
					// check if left sidebar or 3 columns
					if($selected_template =='lsidebar' || $selected_template =='threecols'  ) {
						echo '<div class="sidebar-responsive-wrapper hidden-phone left-sidebar">';
						get_sidebar('sidebar4'); 
						echo '</div>';
					} 
					?>
					

				<?php 
					// check if full width
					if ($selected_template =='fullwidth') { ?>
 						<div id="main" class="span12 clearfix" role="main">


				<?php } else if ($selected_template =='threecols') { 
						// left sidebar
					?>
						<div id="main" class="span6 clearfix" role="main">
					
					<?php } else { 
						// right sidebar
						?>
						
						<div id="main" class="span9 clearfix" role="main">
					<?php } ?>


				<div class="page-visualbreak"></div>
				<div class="page-context-banner"></div> <!-- An empty div hook for page specific stuff -->
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
									
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
							
							<div class="page-header"><h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1></div>
							


						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">

<div class="breadcrumbs">
    <?php if(function_exists('bcn_display'))
    {
        bcn_display();
    }?>
</div>

							<?php the_post_thumbnail( 'wpbs-featured-post', array('class'=>"img-rounded featured-post-image")); ?>

							<?php the_content(); ?>
					
							<?php wp_link_pages(); ?>
					
						</section> <!-- end article section -->
						
							<section class="clearfix post_content">
								<?php if(get_field('primary_post_links')): ?>
										<?php while(has_sub_field('primary_post_links')): ?>

											<?php 
												$a = get_sub_field('custom_link_title'); 
												$b = get_sub_field('custom_link_description');
												$c = get_sub_field('custom_post_link');
											?>

											<div class="post-link-item">
											<h2><a href="<?php echo get_permalink($c->ID); ?>">
											<?php 
											if($a) { 
												echo $a; 
											} else { 
												echo get_the_title($c->ID); 
											}
											?></a></h2>
											<?php echo $b; ?>
										</div>

										<?php endwhile; ?>

								<?php endif; ?>

								<?php if(get_field('secondary_post_links')): ?>
							
									<h4>Links:</h4>
											<ul>							
										<?php while(has_sub_field('secondary_post_links')): ?>
											<?php $post_objects = get_sub_field('links_to'); ?>
											    <li>
													<a href="<?php echo get_permalink($post_objects->ID); ?>"><?php echo get_the_title($post_objects->ID); ?></a>

												</li>
										<?php endwhile; ?>
									</ul>
						
								<?php endif; ?>



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
					if($selected_template =='rsidebar' || $selected_template =='threecols') {
						echo '<div class="sidebar-responsive-wrapper hidden-phone right-sidebar">';
						get_sidebar('sidebar1'); 
						echo '</div>';
					} ?>

					
				
    
			</div> <!-- end #content -->

<?php get_footer(); ?>
