<!---
* CATEGORY TEMPLATE
-->

<?php get_header(); ?>
			
			<div id="content" class="clearfix row-fluid">

						<div id="main" class="span9 clearfix" role="main">


				<div class="page-visualbreak"></div>

							<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

							<header>
								<div class="page-header"><h1 class="single-title" itemprop="headline"><?php single_cat_title(); ?></h1></div>
								
							</header>

									<section class="post_content clearfix" itemprop="articleBody">

									<?php
									// get current category ID
									$category = get_category( get_query_var( 'cat' ) );
									$cat_id = $category->cat_ID;	

									// return posts in this category and sort alphabetically
									$args = array
										(
										'cat' => $cat_id,
										'post_parent' => '0',
										'posts_per_page' => '50',
										'orderby' => 'title',
										'order' => 'ASC'
										
										);		

									// build the new query and run the loop

									// echo '<div class="well">' . category_description( $cat_id ) . '</div>';

									  $category_posts = new WP_Query($args);
									   if($category_posts->have_posts()) : while($category_posts->have_posts()) : $category_posts->the_post();

									?>
										<div class="row-fluid">
											<div class="post-excerpt span12">
													<?php 
													// only show edit button if user has permission to edit posts
													if( $user_level > 0 ) { 
													?>
													<div class="pull-right">
														<i class="icon-pencil"></i> <a href="<?php echo get_edit_post_link(); ?>"><?php _e("Edit ","bonestheme"); ?></a>
													</div>
													<?php } ?>
													<div class="span2"><a href="<?php the_permalink() ?>"><?php the_post_thumbnail( 'wpbs-category-thumb' ); ?></div>
													<div class="span9">
													<strong><a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
													<?php 
													$a = the_excerpt();
													echo strip_tags($a);
													?>

												</div>
											</div>
																		
										</div>

																				


									<?php endwhile; ?>			
									
									
									</section> <!-- end article section -->

							</article> <!-- end article -->

					<?php if (function_exists('page_navi')) { // if expirimental feature is active ?>
						
						<?php page_navi(); // use the page navi function ?>

					<?php } else { // if it is disabled, display regular wp prev & next links ?>
						<nav class="wp-prev-next">
							<ul class="clearfix">
								<li class="prev-link"><?php next_posts_link(_e('&laquo; Older Entries', "bonestheme")) ?></li>
								<li class="next-link"><?php previous_posts_link(_e('Newer Entries &raquo;', "bonestheme")) ?></li>
							</ul>
						</nav>
					<?php } ?>

					</article>

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
						<?php get_sidebar('sidebar1'); ?>
				</div>

			</div> <!-- end #content -->

<?php get_footer(); ?>

