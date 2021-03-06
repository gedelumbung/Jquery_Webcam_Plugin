<style>
body{
font-family:Arial;
font-size:12px;
text-align:center;
}
a{
text-decoration:none;
color:#FF9900;
}
a:hover{
text-decoration:overline;
color:#FF3300;
}
ul{
list-style:none;
}
</style>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="jquery.webcam.js"></script>
<div id="webcam">

</div>
<p style="width:100%;text-align:center; "><a href="javascript:webcam.capture(3);changeFilter();void(0);">Take a picture after 3 seconds</a> | <a href="javascript:webcam.capture();changeFilter();void(0);">Take a picture instantly</a></p>



<p><canvas id="canvas" height="240" width="320"></canvas></p>
<p id="cams"></p>
<p id="status" style="height:22px; color:#c00;font-weight:bold;"></p>

<script type="text/javascript">



var pos = 0;

var ctx = null;

var cam = null;

var image = null;



var filter_on = false;

var filter_id = 0;



function changeFilter() {

	if (filter_on) {

		filter_id = (filter_id + 1) & 7;

	}

}


jQuery("#webcam").webcam({



	width: 320,

	height: 240,

	mode: "callback",

	swffile: "jscam_canvas_only.swf",



	onTick: function(remain) {



		if (0 == remain) {

			jQuery("#status").text("Cheese!");

		} else {

			jQuery("#status").text(remain + " seconds remaining...");

		}

	},



	onSave: function(data) {



		var col = data.split(";");

		var img = image;



		if (false == filter_on) {



			for(var i = 0; i < 320; i++) {

				var tmp = parseInt(col[i]);

				img.data[pos + 0] = (tmp >> 16) & 0xff;

				img.data[pos + 1] = (tmp >> 8) & 0xff;

				img.data[pos + 2] = tmp & 0xff;

				img.data[pos + 3] = 0xff;

				pos+= 4;

			}



		} else {



			var id = filter_id;

			var r,g,b;

			var r1 = Math.floor(Math.random() * 255);

			var r2 = Math.floor(Math.random() * 255);

			var r3 = Math.floor(Math.random() * 255);



			for(var i = 0; i < 320; i++) {

				var tmp = parseInt(col[i]);



				/* Copied some xcolor methods here to be faster than calling all methods inside of xcolor and to not serve complete library with every req */



				if (id == 0) {

					r = (tmp >> 16) & 0xff;

					g = 0xff;

					b = 0xff;

				} else if (id == 1) {

					r = 0xff;

					g = (tmp >> 8) & 0xff;

					b = 0xff;

				} else if (id == 2) {

					r = 0xff;

					g = 0xff;

					b = tmp & 0xff;

				} else if (id == 3) {

					r = 0xff ^ ((tmp >> 16) & 0xff);

					g = 0xff ^ ((tmp >> 8) & 0xff);

					b = 0xff ^ (tmp & 0xff);

				} else if (id == 4) {



					r = (tmp >> 16) & 0xff;

					g = (tmp >> 8) & 0xff;

					b = tmp & 0xff;

					var v = Math.min(Math.floor(.35 + 13 * (r + g + b) / 60), 255);

					r = v;

					g = v;

					b = v;

				} else if (id == 5) {

					r = (tmp >> 16) & 0xff;

					g = (tmp >> 8) & 0xff;

					b = tmp & 0xff;

					if ((r+= 32) < 0) r = 0;

					if ((g+= 32) < 0) g = 0;

					if ((b+= 32) < 0) b = 0;

				} else if (id == 6) {

					r = (tmp >> 16) & 0xff;

					g = (tmp >> 8) & 0xff;

					b = tmp & 0xff;

					if ((r-= 32) < 0) r = 0;

					if ((g-= 32) < 0) g = 0;

					if ((b-= 32) < 0) b = 0;

				} else if (id == 7) {

					r = (tmp >> 16) & 0xff;

					g = (tmp >> 8) & 0xff;

					b = tmp & 0xff;

					r = Math.floor(r / 255 * r1);

					g = Math.floor(g / 255 * r2);

					b = Math.floor(b / 255 * r3);

				}



				img.data[pos + 0] = r;

				img.data[pos + 1] = g;

				img.data[pos + 2] = b;

				img.data[pos + 3] = 0xff;

				pos+= 4;

			}

		}



		if (pos >= 0x4B000) {

			ctx.putImageData(img, 0, 0);

			pos = 0;

		}

	},



	onCapture: function () {

		webcam.save('/upload.php');



		jQuery("#flash").css("display", "block");

		jQuery("#flash").fadeOut(100, function () {

			jQuery("#flash").css("opacity", 1);

		});

	},



	debug: function (type, string) {

		jQuery("#status").html(type + ": " + string);

	},



	onLoad: function () {



		var cams = webcam.getCameraList();

		for(var i in cams) {

			jQuery("#cams").append("<li>" + cams[i] + "</li>");

		}

	}

});



function getPageSize() {



	var xScroll, yScroll;



	if (window.innerHeight && window.scrollMaxY) {

		xScroll = window.innerWidth + window.scrollMaxX;

		yScroll = window.innerHeight + window.scrollMaxY;

	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac

		xScroll = document.body.scrollWidth;

		yScroll = document.body.scrollHeight;

	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari

		xScroll = document.body.offsetWidth;

		yScroll = document.body.offsetHeight;

	}



	var windowWidth, windowHeight;



	if (self.innerHeight) { // all except Explorer

		if(document.documentElement.clientWidth){

			windowWidth = document.documentElement.clientWidth;

		} else {

			windowWidth = self.innerWidth;

		}

		windowHeight = self.innerHeight;

	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode

		windowWidth = document.documentElement.clientWidth;

		windowHeight = document.documentElement.clientHeight;

	} else if (document.body) { // other Explorers

		windowWidth = document.body.clientWidth;

		windowHeight = document.body.clientHeight;

	}



	// for small pages with total height less then height of the viewport

	if(yScroll < windowHeight){

		pageHeight = windowHeight;

	} else {

		pageHeight = yScroll;

	}



	// for small pages with total width less then width of the viewport

	if(xScroll < windowWidth){

		pageWidth = xScroll;

	} else {

		pageWidth = windowWidth;

	}



	return [pageWidth, pageHeight];

}



window.addEventListener("load", function() {



	jQuery("body").append("<div id=\"flash\"></div>");



	var canvas = document.getElementById("canvas");



	if (canvas.getContext) {

		ctx = document.getElementById("canvas").getContext("2d");

		ctx.clearRect(0, 0, 320, 240);



		var img = new Image();

		img.src = "/static/logo.gif";

		img.onload = function() {

			ctx.drawImage(img, 129, 89);

		}

		image = ctx.getImageData(0, 0, 320, 240);

	}

	

	var pageSize = getPageSize();

	jQuery("#flash").css({ height: pageSize[1] + "px" });



}, false);



window.addEventListener("resize", function() {



	var pageSize = getPageSize();

	jQuery("#flash").css({ height: pageSize[1] + "px" });



}, false);



</script>