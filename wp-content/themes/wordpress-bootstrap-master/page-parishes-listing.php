<?php
/*
Template Name: Parishes Listing
*/
?>

<?php get_header(); ?>

			<div id="content" class="clearfix row-fluid">
					<div id="main" class="span9 clearfix" role="main">

						<div class="page-visualbreak"></div>

						<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header>
							<?php the_post_thumbnail( 'wpbs-featured' ); ?>
							
							<div class="page-header"><h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1></div>
							
						</header> <!-- end article header -->								



							<div class="alert alert-info">
								 <small><i class="icon-info-sign"></i> Select a Parish name below, or visit these links:<br />
								 <i class="icon-search"></i> <a href="/mass-finder"> Mass Finder</a>
								 <i class="icon-map-marker"></i>  <a href="/parishes-map"> View All Parishes on a map</a>
								 <i class="icon-flag"></i><a href="<?php echo home_url();?>/uploadedFiles/Parish_Listing_by_Deanery.pdf"> Parishes by Deanery</a></small>
								</div>

							<section class="parishes-listing-results">

							<ul class="span6">	
							<?php
							$args=array(
							  'post_type' => 'parish',
							  'post_status' => 'publish',
							  'order' => 'ASC',
							  'orderby' => 'title',
							  'posts_per_page' => -1
							);

							$query = new WP_Query($args);
							// The Loop
						
								$count = 0; 
								$output = round(($query->post_count / 2));
								$max_ouput = $query->post_count;
								?>
								<?php while ( $query->have_posts() ) : $query->the_post(); ?>

									<?php 
										if ($count == $output) {
										echo '</ul><ul class="span6">';
										}	?>


									<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
								    <?php the_title(); ?></a> <small>(<?php echo get_field('city'); ?>)</small></li>


							<?php $count++; ?>
							<?php endwhile; ?>

							<?php wp_reset_postdata(); ?>
						</ul>
						</section>

			</article>
		</div> <!-- end #main -->
					<?php 
				
						echo '<div class="sidebar-responsive-wrapper hidden-phone right-sidebar">';
						get_sidebar('sidebar1'); 
						echo '</div>';
					 ?>
</div>

<?php get_footer(); ?>