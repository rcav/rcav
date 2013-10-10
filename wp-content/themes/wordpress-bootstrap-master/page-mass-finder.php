<?php
/*
Template Name: Mass Finder
*/
?>

<?php get_header();
$root_path = $_SERVER['DOCUMENT_ROOT'];
?>

			<div id="content" class="clearfix row-fluid">
					<div id="main" class="span9 clearfix" role="main">

						<div class="page-visualbreak"></div>

						<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header>
						
							
							<div class="page-header"><h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1></div>

								<div class="alert alert-info">
								 <small><i class="icon-info-sign"></i> Select a city from the list below to find Parishes near you or visit these links:</small><br />
								 <small><i class="icon-list"></i> <a href="/parishes-list">View all Parishes by name</a> or <i class="icon-map-marker"></i>  <a href="/parishes-map">View All Parishes on a map</a></small> <br />
				
								</div>
																

								<form method="POST" id="massfinder" name="massfinder" action="">
										<select type="select" name="city" id="city" value=""/>
											<option value="">Select a City</option>
											
											<option value="Abbotsford">Abbotsford</option>
											<option value="Agassiz">Agassiz</option>
											<option value="Aldergrove">Aldergrove</option>
											<option value="Anahim Lake">Anahim Lake</option>
											<option value="Bella Coola">Bella Coola</option>
											<option value="Boston Bar">Boston Bar</option>
											<option value="Bowen Island">Bowen Island</option>
											<option value="Burnaby">Burnaby</option>
											<option value="Cheam">Cheam</option>
											<option value="Chilliwack">Chilliwack</option>
											<option value="Coquitlam">Coquitlam</option>
											<option value="Crescent Beach">Crescent Beach</option>
											<option value="Delta">Delta</option>
											<option value="Garibaldi Highlands">Garibaldi Highlands</option>
											<option value="Gibsons">Gibsons</option>
											<option value="Hope">Hope</option>
											<option value="Langley">Langley</option>
											<option value="Maple Ridge">Maple Ridge</option>
											<option value="Mission">Mission</option>
											<option value="New Westminster">New Westminster</option>
											<option value="North Vancouver">North Vancouver</option>
											<option value="Port Coquitlam">Port Coquitlam</option>
											<option value="Port Moody">Port Moody</option>
											<option value="Powell River">Powell River</option>
											<option value="Richmond">Richmond</option>
											<option value="Sechelt">Sechelt</option>
											<option value="Surrey">Surrey</option>
											<option value="Squamish">Squamish</option>
											<option value="Vancouver">Vancouver</option>
											<option value="West Vancouver">West Vancouver</option>
											<option value="Whistler">Whistler</option>
											<option value="Whiterock">White Rock</option>
										</select>

										<input type="submit" value="Search" class="btn btn-primary"/>
								</form>


								<?php 
									$submitted_check = $_SERVER['REQUEST_METHOD'] == "POST";
									if($submitted_check)  {
										// set $city variable
										$city =$_POST['city']; 
										//$language = $_POST['primarylanguage'];

										// build our query $args array
										$args = array
										(
										'post_type' =>'parish',
										'meta_key' => 'city',
										'meta_value' => $city,
										'orderby' => 'title',
										'order' => 'ASC',
										'posts_per_page' => -1
										);	
					/*
					//add language query support
					'meta_query' => array(
						array(
							'key' => 'city',
							'value' => 'bilaspur'
						),
							array(
								'key' => 'type',
								'value' => 'plots'
						),
					);
					*/
									  $parish_posts = new WP_Query($args);
									  $parish_count = $parish_posts->post_count;
									  
									  //print_r($parish_posts);

									}

									if(isset($city)) {
										echo '<h4>Parishes in <strong>' . $_POST['city'] .' (' . $parish_count  .  ')</strong></h4>';
									};


									   if($parish_posts->have_posts()) : while($parish_posts->have_posts()) : $parish_posts->the_post();
								?>

										 	<div class="mass-finder-result">

										 	<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
										    <?php the_title(); ?></a></h4>



											<?php 
												$current_pid = get_field('parish_id');
												$path = $root_path . '/xml-data/parish_addresses_sql.xml';
												$s = simplexml_load_file($path);
												
												foreach($s->children() as $child):
													if($child->pid == $current_pid ) {  
														echo $child->address1;
														if($child->address2) {
															echo '<br />';
															echo $child->address2;

														} 
													}
												endforeach; ?>	
										   
										    <?php if(get_field('city')) {
										    	echo get_field('city'); 
										    }
										   	?>

										    <?php if(get_field('province')) {
										    	echo get_field('province'); 
										    }
										   	?>

										    <?php if(get_field('postal')) {
										    	echo get_field('postal'); 
										    }
										   	?>
						   					
						   					<br />

											<?php 
												$path = $root_path . '/xml-data/contacts_sql.xml';
												$s = simplexml_load_file($path);
												foreach($s->children() as $child):
													if($child->pid == $current_pid ) {  
														$contact = $child->contact_type;

														switch($contact) {

															case "Email":
															echo 'Email: ' . ' <a href="mailto:' . $child->contact_value . '">' . $child->contact_value.'</a><br />';
															break;

															case "Phone":
															echo 'Phone: ' . $child->contact_value . '<br />';
															break;															


														}
													}
												endforeach; ?>


										 	<?php if(get_field('primarylanguage')) 
										 		{ 
										 			echo '<div> Language: English' . ', ' . get_field('primarylanguage') . '</div>';
										 		}
										 	?>

										<?php 
												$path = $root_path . '/xml-data/reverands_sql.xml';
												$s = simplexml_load_file($path);
												foreach($s->children() as $child):
													if($child->pid == $current_pid ) {  
														if($child->is_primary == 1) {
															echo '<strong>' . $child->reverand_name . '</strong>';
														} else if ($child->is_primary == 0) {
															echo '<br />' . $child->reverand_name;
														}
														
													}
												endforeach; ?>	

											<div style="margin-top:15px;">
										 	<?php if(get_field('gmap_link')) 
												echo '<i class="icon-map-marker"></i> <a href="' . get_field('gmap_link') . '" target="_blank">View Map</a> |';
											?> <i class="icon-time"></i>  <a href="<?php the_permalink() ?>#mass-times" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">Mass and Devotion Times</a>
											</div>




											</div>
								 	<?php 

								 	endwhile; else : ?>

								 		<p>Nothing selected.</p>
								 	<?php 

								  endif;

								 ?>
						</article>
					</div>
					<?php 
				
						echo '<div class="sidebar-responsive-wrapper hidden-phone right-sidebar">';
						get_sidebar('sidebar1'); 
						echo '</div>';
						
					 ?>

			</div>

<?php get_footer(); ?>

<?php get_footer(); ?>