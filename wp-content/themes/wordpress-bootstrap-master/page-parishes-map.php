<?php
/*
Template Name: Parishes Map
*/
?>

<?php get_header(); ?>
			
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyCUri8tB1QrGgyv5fVTJHAtpXYeBhr4AlY" type="text/javascript"></script>

			<div id="content" class="clearfix row-fluid">
			
				<div id="main" class="span12 clearfix" role="main">
						
				<div class="page-visualbreak"></div>

						<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header class="clearfix parishes-map-toggles">
							
							<section class="span12 row-fluid">
							<div class="page-header"><h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1></div>
							
							<strong>Add/Remove markers from the map by clicking on these controls:</strong><br /><br />
							<div class="pull-left span5">
							    <ul>
							    <li><input type="checkbox" id="Parishesbox" onclick="boxclick(this,'Parishes')" name="C1" value="ON" /> Parishes</li>
							    <li><input type="checkbox" id="Eventsbox" onclick="boxclick(this,'Events')" name="C3" value="ON" /> Events</li>
							    <li><input type="checkbox" id="Organizationsbox" onclick="boxclick(this,'Organizations')" name="C2" value="ON" /> Organizations</li>
							    <li><input type="checkbox" id="Youngbox" onclick="boxclick(this,'Young')" name="C4" value="ON" /> Young Adults</li>
							</ul>
							</div>

						    <div class="pull-left span5">
						    <ul>
								<li><input type="checkbox" id="Elementarybox" onclick="boxclick(this,'Elementary')" name="C5" value="ON" /> Elementary </li>
							    <li><input type="checkbox" id="Secondarybox" onclick="boxclick(this,'Secondary')" name="C6" value="ON" /> Secondary </li>
							    <li><input type="checkbox" id="Collegesbox" onclick="boxclick(this,'Colleges')" name="C7" value="ON" /> Colleges/Other</li>
							  </ul>
							</div>
						</section>
						</header> <!-- end article header -->

					<br />

           <div id="map" class="span8" style="height:580px;"><!-- map draws here --> </div>
        


           <div id="side_bar" class="span3 parishes-map-sidebar" style="overflow:scroll; height:580px;"></div>

       </article>

	</div>
</div>


<footer class="clearfix row-fluid">
		<div class="span12">
		<ul class="parishes-map-legend">
			<h4>Legend:</h4>
			<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker4.png" /> Parishes</li>
			<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker6.png"/> Organizations</li> 
			<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker5.png"/> Events</li>
			<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker_y.png"/> Young Adults</li>
			<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker_E.png"/> Elementary</li>
			<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker_S.png"/> Secondary</li>
			<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker_C.png"/> Colleges/Other</li>
	</div>
</footer>




    <noscript><b>JavaScript must be enabled in order for you to use Google Maps.</b> 
      However, it seems JavaScript is either disabled or not supported by your browser. 
      To view Google Maps, enable JavaScript by changing your browser options, and then 
      try again.
    </noscript>


    <script type="text/javascript">
    //<![CDATA[

    if (GBrowserIsCompatible()) {
      var gmarkers = [];
      var gicons = [];

      gicons["Parishes"] = new GIcon(G_DEFAULT_ICON,"<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker4.png");
      gicons["Organizations"] = new GIcon(G_DEFAULT_ICON,"<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker5.png");
      gicons["Events"] = new GIcon(G_DEFAULT_ICON,"<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker6.png");
      gicons["Young"] = new GIcon(G_DEFAULT_ICON,"<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker_y.png");
      gicons["Elementary"] = new GIcon(G_DEFAULT_ICON,"<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker_e.png");
      gicons["Secondary"] = new GIcon(G_DEFAULT_ICON,"<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker_s.png");
      gicons["Colleges"] = new GIcon(G_DEFAULT_ICON,"<?php echo get_stylesheet_directory_uri(); ?>/images/assets/icons/marker_C.png");

      // A function to create the marker and set up the event window
      function createMarker(point,name,html,category) {
        var marker = new GMarker(point,gicons[category]);
        // === Store the category and name info as a marker properties ===
        marker.mycategory = category;                                 
        marker.myname = name;
        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml(html);
        });
        gmarkers.push(marker);
        return marker;
      }

      // == shows all markers of a particular category, and ensures the checkbox is checked ==
      function show(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].show();
          }
        }
        // == check the checkbox ==
        document.getElementById(category+"box").checked = true;
      }

      // == hides all markers of a particular category, and ensures the checkbox is cleared ==
      function hide(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].hide();
          }
        }
        // == clear the checkbox ==
        document.getElementById(category+"box").checked = false;
        // == close the info window, in case its open on a marker that we just hid
        map.closeInfoWindow();
      }

      // == a checkbox has been clicked ==
      function boxclick(box,category) {
        if (box.checked) {
          show(category);
        } else {
          hide(category);
        }
        // == rebuild the side bar
        makeSidebar();
      }

      function myclick(i) {
        GEvent.trigger(gmarkers[i],"click");
      }


      // == rebuilds the sidebar to match the markers currently displayed ==
      function makeSidebar() {
        var html = "";
        for (var i=0; i<gmarkers.length; i++) {
          if (!gmarkers[i].isHidden()) {
            html += '<a href="javascript:myclick(' + i + ')">' + gmarkers[i].myname + '</a><br />';
          }
        }
        document.getElementById("side_bar").innerHTML = html;
      }


      // create the map
      var map = new GMap2(document.getElementById("map"));
      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());
      map.setCenter(new GLatLng(49.23989,-122.894349), 10);


      // Read the data
      GDownloadUrl("<?php echo home_url(); ?>/xml-data/category.xml", function(doc) {
        var xmlDoc = GXml.parse(doc);
        var markers = xmlDoc.documentElement.getElementsByTagName("marker");
          
        for (var i = 0; i < markers.length; i++) {
          // obtain the attribues of each marker
          var lat = parseFloat(markers[i].getAttribute("lat"));
          var lng = parseFloat(markers[i].getAttribute("lng"));
          var point = new GLatLng(lat,lng);
          var address = markers[i].getAttribute("address");
          var name = markers[i].getAttribute("name");
          var html = "<b>"+name+"</b><p>"+address;
          var category = markers[i].getAttribute("category");
          // create the marker
          var marker = createMarker(point,name,html,category);
          map.addOverlay(marker);
        }

        // == show or hide the categories initially ==
        show("Parishes");
        hide("Organizations");
        hide("Events");
        hide("Young");
        hide("Elementary");
        hide("Secondary");
        hide("Colleges");
        // == create the initial sidebar ==
        makeSidebar();
      });
    }

    else {
      alert("Sorry, the Google Maps API is not compatible with this browser");
    }
    // This Javascript is based on code provided by the
    // Blackpool Community Church Javascript Team
    // http://www.commchurch.freeserve.co.uk/   
    // http://econym.googlepages.com/index.htm

    //]]>
    </script>




<?php get_footer(); ?>