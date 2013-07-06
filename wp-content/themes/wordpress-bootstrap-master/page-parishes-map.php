<?php
/*
Template Name: Parishes Map
*/
?>

<?php get_header(); 
$root_path = $_SERVER['DOCUMENT_ROOT'];
?>

  <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBbxtYRe2frX3hQVIc1W3Mt1xEuAKFPWBs&sensor=false">
    </script>

<script type="text/javascript"> 
//<![CDATA[
      // this variable will collect the html which will eventually be placed in the side_bar 
      var side_bar_html = ""; 
    
      // arrays to hold copies of the markers and html used by the side_bar 
      // because the function closure trick doesnt work there 
      var gmarkers = []; 

     // global "map" variable
      var map = null;

      // info window variable
      var infowindow = new google.maps.InfoWindow(
        { 
          size: new google.maps.Size(150,50),

        });


        // A function to create the marker and set up the event window function 
        function createMarker(point, name, html, category, image) {
            var contentString = html;
            var image = ("<?php echo get_stylesheet_directory_uri(); ?>/images/assets/markers/marker_" + category + ".png").toLowerCase();
           // console.log(image);
            var marker = new google.maps.Marker({
                position: point,
                map: map,
                icon: image,
                //zIndex: Math.round(latlng.lat()*-100000)<<5
                });

              marker.mycategory = category;
              marker.myname = name;

            google.maps.event.addListener(marker, 'click', function() {
                infowindow.setContent(contentString); 
                infowindow.open(map,marker);
                });
            // save the info we need to use later for the side_bar
            gmarkers.push(marker);
            // add a line to the side_bar html
            side_bar_html += '<a href="javascript:myclick(' + (gmarkers.length-1) + ')">' + name + '<\/a><br>';
        }
         
        // This function picks up the click and opens the corresponding info window
        function myclick(i) {
          google.maps.event.trigger(gmarkers[i], "click");
        }

     function show(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
      if (!gmarkers[i].getMap()) gmarkers[i].setMap(map); 
            gmarkers[i].setVisible(true);
          }
        }
        // == check the checkbox ==
         document.getElementById(category+"box").checked = true;
      }

      // == hides all markers of a particular category, and ensures the checkbox is cleared ==
        function hide(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].setVisible(false);
          }
        }
        // == clear the checkbox ==
         document.getElementById(category+"box").checked = false;
        // == close the info window, in case its open on a marker that we just hid
         infowindow.close();
      }

      // == a checkbox has been clicked ==
       function boxclick(box,category) {
        if (box.checked) {
          show(category);
        } else {
          hide(category);
        }
        //updateSidebar();
      }

      // == rebuilds the sidebar to match the markers currently displayed ==
      function updateSidebar() {
        var html = "";
        for (var i=0; i<gmarkers.length; i++) {
            if (!gmarkers[i].isVisible()) {
           html += '<a href="javascript:myclick(' + i + ')">' + gmarkers[i].myname + '</a><br />';
          }
        }
        document.getElementById("side_bar").innerHTML = html;
      }

      function initialize() {
        // create the map
        var myOptions = {
          zoom: 10,
          center: new google.maps.LatLng(49.23989,-122.894349),
          mapTypeControl: true,
          mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
          navigationControl: true,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById("map-canvas"),
                                      myOptions);
       
        google.maps.event.addListener(map, 'click', function() {
              infowindow.close();
              });
            
            downloadUrl("<?php echo home_url();?>/xml-data/category.xml", function(doc) {
              var xmlDoc = xmlParse(doc);
              var markers = xmlDoc.documentElement.getElementsByTagName("marker");
              for (var i = 0; i < markers.length; i++) {
                // obtain the attribues of each marker
                var lat = parseFloat(markers[i].getAttribute("lat"));
                var lng = parseFloat(markers[i].getAttribute("lng"));
                var point = new google.maps.LatLng(lat,lng);
                var html = markers[i].getAttribute("html");
                var label = markers[i].getAttribute("label");
                var category = markers[i].getAttribute("category");
                // create the marker
                var marker = createMarker(point,label,html,category);
              }

                      // == show or hide the categories initially ==
              show("Parishes");
              hide("Organizations");
              hide("Events");
              hide("Young");
              hide("Elementary");
              hide("Secondary");
              hide("Colleges");

              // put the assembled side_bar_html contents into the side_bar div
              document.getElementById("side_bar").innerHTML = '<option>' + side_bar_html + '</option>';
            });
          }
     google.maps.event.addDomListener(window, 'load', initialize);
//]]>

</script>


    <!--- GMAP -->

			<div id="content" class="clearfix row-fluid">
			
				<div id="main" class="span12 clearfix" role="main">
						
				<div class="page-visualbreak"></div>

						<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
                
					

            <header class="clearfix row-fluid parishes-map-toggles">
              
              <div class="page-header"><h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1></div>



    <div class="row-fluid parishes-map-legend">


    <div class="pull-left span6 well">
      <ul>
      <li><img class="checked" src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/markers/marker_parishes.png" /> Parishes
      <input type="checkbox" id="Parishesbox" onclick="boxclick(this,'Parishes')" name="C1" value="ON" /> </li>
      <li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/markers/marker_events.png"/> Events
      <input type="checkbox" id="Eventsbox" onclick="boxclick(this,'Events')" name="C3" value="ON" /> </li> 
      <li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/markers/marker_organizations.png"/> Organizations
      <input type="checkbox" id="Organizationsbox" onclick="boxclick(this,'Organizations')" name="C2" value="ON" /> </li>
      <li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/markers/marker_young.png"/>  Young Adults
      <input type="checkbox" id="Youngbox" onclick="boxclick(this,'Young')" name="C4" value="ON" /></li>
      </ul>
    </div>

     <div class="pull-left span6 well">
          <ul>
      <li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/markers/marker_elementary.png"/>  Elementary
      <input type="checkbox" id="Elementarybox" onclick="boxclick(this,'Elementary')" name="C5" value="ON" /> </li>
      <li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/markers/marker_secondary.png"/> Secondary
      <input type="checkbox" id="Secondarybox" onclick="boxclick(this,'Secondary')" name="C6" value="ON" />  </li>
      <li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/assets/markers/marker_colleges.png"/> Colleges/Other
      <input type="checkbox" id="Collegesbox" onclick="boxclick(this,'Colleges')" name="C7" value="ON" /> </li>
  
                </ul>
              </div>
  </div>

            </header> <!-- end article header -->


            <div class="row-fluid">

                <div class="span12" id="gmap-wrapper">
                  <div id="container"> 

                      <div id="dummy"></div>
                      <div id="element">
                        <div id="map-canvas">


                        </div>
                        </div>

                </div> <!-- container -->

               <!--<div class="span3" id="side_bar"></div>-->
            </div>


            </article>

          </div> <!-- end main -->

      </div> <!-- end content -->





<?php get_footer(); ?>