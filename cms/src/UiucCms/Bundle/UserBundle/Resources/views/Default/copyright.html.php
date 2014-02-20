     <!--Footer Copyright-->

    <div class="full-width-wrapper" id="footer-extra-wrapper" >
		<div class="fixed-width-wrapper" style="position:relative; top:-13px;">
          <div id="copyright">
            <ul>
                <li style="color:#bbb; font-size:14px">UIUC RailTEC (C) <?php echo date("Y"); ?> All rights reserved </li>
                <li style="color:#bbb; font-size:14px"><a href="http://ict.uiuc.edu/railroad/contactus.php" title="">Contact Us</a></li>
				<li style="color:#bbb; font-size:14px"><a href="http://ict.uiuc.edu/railroad/RailTECsitemap.php" title="">Site Map</a></li>
                <li style="color:#bbb; font-size:14px"><a href="mailto:lbfrye@illinois.edu">Webmaster</a></li>
            </ul>
            <p style="color:#bbb; font-size:14px"><a href="#" class="back-to-top sprite" title="Back to top">Back to Top</a></p>
            <ul>
              <li style="color:#bbb; font-size:14px; text-align:center"><a href="http://ict.uiuc.edu/railroad/">RailTEC</a> is part of the <a href="http://cee.illinois.edu/">Department of Civil and Environmental Engineering</a> at the <a href="http://www.illinois.edu" target="_blank">University of Illinois at Urbana-Champaign</a> </li>
            </ul>
          </div>
            
    	</div>
    </div>
    <!--/Footer Copyright-->
	
	
	
	<script type="text/javascript">

    function getWindowHeight() {
      var windowHeight = 0;
      if( typeof(window.innerHeight) == 'number' ) {
        windowHeight = window.innerHeight;
      }
      else {
        if( document.documentElement && document.documentElement.clientHeight ) {
          windowHeight = document.documentElement.clientHeight;
        }
        else {
          if( document.body && document.body.clientHeight ) {
            windowHeight = document.body.clientHeight;
          }
        }
      }
      return windowHeight;
    }
    function setFooter() {
      if( document.getElementById ) {
        var windowHeight = getWindowHeight();
        if( windowHeight > 0 ) {
          var contentHeight = document.getElementsByTagName( 'body' )[0].offsetHeight;
          var footerElement = document.getElementById( 'footer-extra-wrapper' );
          var footerHeight = footerElement.offsetHeight;
          if( windowHeight - (contentHeight) >= 0 ) {
            footerElement.style.position = 'absolute';
            footerElement.style.top = (windowHeight - footerHeight) + 'px';
          }
          else {
            footerElement.style.position = 'static';
          }
        }
      }
    }
    

  	window.onload = function () {
      setFooter();
    }
    window.onresize = function () {
      setFooter();
    }
	
	$('.m-simple-toggle').click(function(){
	  setTimeout('setFooter()',1000);
	    
	});
</script>