			<footer role="contentinfo" class="hidden-phone">
			
				<div id="inner-footer" class="clearfix">

		          <div  class="clearfix row-fluid">
		            	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer1') ) : ?>
		            	<?php endif; ?>
		        	</div>

		        	<div  class="clearfix row-fluid">
		            	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer2') ) : ?>
		            	<?php endif; ?>	
		          </div>
					
					<nav class="clearfix">
						<?php bones_footer_links(); // Adjust using Menus in Wordpress Admin ?>
					</nav>
			
					<!--
					<div class="utilitybar pull-right">
						<a href="/legal" class="btn btn-small">Legal</a> <a href="https://secure.rcav.bc.ca/owa" class="btn btn-small">Webmail</a><a href="Priests" class="btn btn-small">Priests</a>
					</div>
					-->


					<p class="attribution">
						<?php bloginfo('description'); ?><br />
						&copy; <?php echo date("Y")?> <?php bloginfo('name'); ?> <br />
					</p>
				
				</div> <!-- end #inner-footer -->
				
			</footer> <!-- end footer -->
		
		</div> <!-- end #container -->
				
		<!--[if lt IE 7 ]>
  			<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
  			<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->
		
		<?php wp_footer(); // js scripts are inserted using this function ?>


	</body>

</html>