<!doctype html>  

<!--[if IEMobile 7 ]> <html <?php language_attributes(); ?>class="no-js iem7"> <![endif]-->
<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!--><html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
	
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		
		<title><?php wp_title( '|', true, 'right' ); ?></title>
				
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
			
		<script src="https://maps.googleapis.com/maps/api/js?v=3&sensor=false"></script>
			

		<!-- html5.js -->
		<!--[if lt IE 9]>
			<script src="http://cdnjs.cloudflare.com/ajax/libs/html5shiv/r29/html5.min.js"></script>
			<script src="http://cdnjs.cloudflare.com/ajax/libs/respond.js/1.1.0/respond.min.js"></script>
			<script src="http://cdnjs.cloudflare.com/ajax/libs/css3pie/1.0.0/PIE.js"></script>
		<![endif]-->
		
  		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<!-- wordpress head functions -->
		<?php wp_head(); ?>
		<!-- end of wordpress head -->

		<!-- theme options from options panel -->
		<?php get_wpbs_theme_options(); ?>

		<!-- typeahead plugin - if top nav search bar enabled -->
		<?php require_once('library/typeahead.php'); ?>


      	
				
	</head>
	
	<body <?php body_class(); ?>>

	<div class="container-fluid nostyles">
				
	<?php if (is_front_page()) { ?>
		<div class="container-fluid nostyles pull-left" >	

				<a class="brand" id="logo" title="<?php echo get_bloginfo('description'); ?>" href="<?php echo home_url(); ?>">
							<?php if(of_get_option('branding_logo','')!='') { ?>
								<img src="<?php echo of_get_option('branding_logo'); ?>" alt="<?php echo get_bloginfo('description'); ?>">
							<?php }
				if(of_get_option('site_name','1')) bloginfo('name'); ?></a>
		</div>
	<?php } ?>

				<div id="utilitybar" class="pull-right">	
					<form action="<?php echo home_url( '/' ); ?>" method="get" class="form-stacked navbar-search pull-right hidden-phone" id="searchform">
					    <fieldset>
							<div class="clearfix">
								<div class="input-append input-prepend">
									<span class="add-on"><i class="icon-search"></i></span><input type="text" name="s" id="search" placeholder="Search" value="" class="placeholder"><button type="submit" class="btn btn-primary">Search</button>
								</div>
					        </div>
					    </fieldset>
					</form>

						<section class="social-media pull-left">
						<a href="https://www.facebook.com/archdioceseofvancouver" class="icon-fb"></a>
						<a href="https://twitter.com/rcav" class="icon-twitter"></a>
						<a href="https://plus.google.com/u/0/108087862432048382768/posts" class="icon-googleplus"></a>
						<a href="http://www.youtube.com/RCAVonline" class="icon-youtube"></a>
						<a href="http://pinterest.com/rcavorg/pins/" class="icon-pinterest"></a>
						</section>
						<?php if (!is_front_page()) { ?><a href="<?php echo home_url(); ?>">Home</a><?php } ?> 
						<a href="<?php echo home_url();?>/donate">Donate</a>
						<a href="<?php echo home_url();?>/contact-us">Contact</a>
						<?php if(of_get_option('search_bar', '1')) {?>
						<?php } ?>
				</div> <!-- end utilitybar -->
		</div>	<!-- end container-fluid -->

		<div class="container-fluid" id="master-container">				

		<header id="main-header" role="banner">

			<div id="inner-header" class="clearfix">

							<?php if (!is_front_page()) { ?>	
								<a class="brand" id="logo" title="<?php echo get_bloginfo('description'); ?>" href="<?php echo home_url(); ?>">
										<img src="<?php bloginfo('template_directory'); ?>/images/assets/logo-inside.png" alt="<?php echo get_bloginfo('description'); ?>">
							<?php } ?>	

				<div class="navbar navbar-fixed-top">
					<!-- <div class="navbar-inner"> -->
						<div class="nav-container"> <!-- log: removed container-fluid class -->
							<nav role="navigation">

								<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">	
							        <span class="icon-bar"></span>
							        <span class="icon-bar"></span>
							        <span class="icon-bar"></span>
								</a>
								
								<div class="nav-collapse">
									<?php bones_main_nav(); // Adjust using Menus in Wordpress Admin ?>
								</div>
								
							</nav>
							

							
						</div> <!-- end .nav-container -->
					<!--</div>  end .navbar-inner -->
				</div> <!-- end .navbar -->
			
			</div> <!-- end #inner-header -->
		
		</header> <!-- end header -->
		

