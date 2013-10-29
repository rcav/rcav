<?php
/**
 * The Template for displaying all single posts of custom post type 'parish'.
**/
?>

<?php get_header(); 
// get the parish ID from custom field parish_id
$current_pid  = get_field('parish_id');
$root_path = $_SERVER['DOCUMENT_ROOT'];
?>

<style type="text/css">
	iframe { width: 350px; height:300px;}
</style>

			<div id="content" class="clearfix row-fluid">
					<div id="main" class="span9 clearfix" role="main">

						<div class="page-visualbreak"></div>

						<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header>
							<?php the_post_thumbnail( 'wpbs-featured' ); ?>
							
							<div class="page-header"><h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1></div>
							
						</header> <!-- end article header -->		




									 	<div class="span5">
									    <h4>Location &amp; Contact</h4>
									    	<div>
											<?php 
												$path = $root_path . '/xml-data/parish_addresses_sql.xml';
												$s = simplexml_load_file($path);
												foreach($s->children() as $child):
													if($child->pid == $current_pid ) {  
														if($child->address1) {
															echo $child->address1;
															if(get_field('city')) {
										    					echo '<br />' . get_field('city');	
										    				}
										    				if(get_field('province')) {
										    					echo ', ' . get_field('province');	
										    				}
										    				if(get_field('postal')) {
										    					echo ', ' .  get_field('postal');	
										    				}

														}
														if(!empty($child->address2)) {
															echo '<br /><br />';
															echo 'Mailing Address:<br />';
															echo $child->address2;
														}
													}
												endforeach; ?>	

										   </div>

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

															case "Fax":
															echo 'Fax: ' . $child->contact_value . '<br />';
															break;

															case "Website":
															//echo $child->contact_value;
															$website_url = $child->contact_value;
															$website_url_prefix = substr($website_url,0,4);
															
															if($website_url_prefix == 'http') {
																echo 'Website: ' . ' <a target="_blank" href="' . $website_url . '">' . rtrim($website_url).'</a><br />';
															} else {
																echo 'Website: ' . ' <a target="_blank" href="http://' . $website_url . '">' . rtrim($website_url).'</a><br />';
															}
		
 															
															
															
															break;

														}
													}
												endforeach; ?>



										<br />
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

									 	<?php if(get_field('yearest')) 

									 		{ 
									 			echo '<br /><br /><strong>More Information:</strong>';
									 			echo '<p>Established:' . get_field('yearest') . '</p>';
									 		}
									 	?>

									 	<?php if(get_field('primarylanguage')) 
									 		{ 
									 			echo '<p>Language:' . get_field('primarylanguage') . '</p>';
									 		}
									 	?>


									 	<?php if(get_field('wheelchair')) 
									 		{ 
									 			echo '<div class="icon-wheelchair"><span>Wheelchair Accessible</span></div>';
									 		}
									 	?>

									 	</div>


										<div class="span4" id="gmap-embed">
											<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo get_field('gmap_link') . '&output=embed'; ?>"></iframe>
										</div>


									<div class="clearfix row-fluid">

										<section class="span12">

									<a id="mass-times"></a>
									<div class="tabbable" id="parish-times"> <!-- Only required for left/right tabs -->
									  <ul class="nav nav-pills">
									    <li class="active"><a href="#tab1" data-toggle="tab"><i class="icon-time"></i> Mass Times</a></li>
									    <li><a href="#tab2" data-toggle="tab"><i class="icon-time"></i> Special Mass / Service Times</a></li>
									    <li><a href="#tab3" data-toggle="tab"><i class="icon-time"></i> Confession Times</a></li>
									    <li><a href="#tab4" data-toggle="tab"><i class="icon-time"></i> Devotion Times</a></li>
									  </ul>
									  <div class="tab-content">

									    <div class="tab-pane active" id="tab1">
											<?php 
												$path = $root_path . '/xml-data/mass_times_sql.xml';
												$s = simplexml_load_file($path);
												$i = 0;
												foreach($s->children() as $child):
													if($child->pid == $current_pid ) { // replace this meta_value
														 if($child->language != 'English') {
															echo $child->language;
														}
														echo '<div class="event-date">' . $child->days .  '</div>';
														echo '<div class="start-endtimes">' . date("g:i A", strtotime($child->time))  .'</div>';
														echo '<br />';
														$i++;

													}
												endforeach; ?>
									    </div>
									    <div class="tab-pane" id="tab2">
											<?php 
												$path = $root_path . '/xml-data/special_mass_times_sql.xml';
												$s = simplexml_load_file($path);
												foreach($s->children() as $child):
													if($child->pid == $current_pid ) { // replace this meta_value 
														echo '<div class="event-date">' . $child->days .'</div>';
														echo '<div class="event-type">' . $child->special .'</div>';
														echo '<div class="start-endtimes">' . date("g:i A", strtotime($child->Expr1)) .'</div>';
														echo '<br />';
													}
												endforeach; ?>
									    </div>    
									    <div class="tab-pane" id="tab3">
											<?php 
												$path = $root_path . '/xml-data/confession_times_sql.xml';
												$c = simplexml_load_file($path);
												foreach($c->children() as $child):
													if($child->pid == $current_pid ) { // replace this meta_value 
														echo '<div class="event-date">' . $child->days .'</div>';
														echo '<div class="start-endtimes">' . date("g:i A", strtotime($child->time)) . ' to ' . date("g:i A", strtotime($child->endtime)) .'</div>';
														echo '<br />';
													}
												endforeach; ?>
									    </div>

									    <div class="tab-pane" id="tab4">
											<?php 
												$path = $root_path . '/xml-data/devotion_times_sql.xml';
												$c = simplexml_load_file($path);
												foreach($c->children() as $child):
													if($child->pid == $current_pid ) { 
														echo '<div class="event-date">' . $child->days .'</div>';
														echo '<div class="event-type>' . $child->dtype . '</div>';
														echo '<div class="start-endtimes">' . date("g:i A", strtotime($child->time)) .'</div>';
														echo '<br />';
													}
												endforeach; ?>
									    </div>


									

									  </div> <!-- end tab-content -->
									</div> <!-- end tabbable -->
									</section>
							</article>
						</div> <!-- end main -->
					<?php 
				
						echo '<div class="sidebar-responsive-wrapper hidden-phone right-sidebar">';
						get_sidebar('sidebar1'); 
						echo '</div>';
					 ?>

			</div> <!-- end content -->

<?php get_footer(); ?>