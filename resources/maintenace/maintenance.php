<!doctype html>
<html>
	<head>
	<meta charset="utf-8">
	<title>Maintenance</title>
	<link rel="stylesheet" href="/resources/css1.3/bstyle.css">
	<script type="text/javascript">
	window.onload = function() {
		var wrapper = document.getElementById("wrapper");
		var loading = document.getElementById("loading");
		wrapper.style.visibility = "visible";
		loading.style.display = "none";

		var strUA = navigator.userAgent.toLowerCase();

		if(strUA.indexOf("iphone") >= 0 || strUA.indexOf("ipad") >= 0) {
			var engineIF = function() {};
			engineIF.prototype.cmd = function(keyString) {
				location.href = "native://" + keyString;
			}
		}

		var eng = new engineIF();
		if (eng === undefined || eng === null) {
			eng = {
				cmd : function() {
					//noop
				}
			}
		}

		if(strUA.indexOf("iphone") >= 0) {
			document.write('<meta name="viewport" content="width=880px, minimum-scale=0.45, maximum-scale=0.45, user-scalable=no" />');
		} else if (strUA.indexOf("ipad") >= 0) {
			document.write('<meta name="viewport" content="width=1024px, minimum-scale=0.9, maximum-scale=0.9, user-scalable=no" />');
		} else {
			document.write('<meta name="viewport" content="width=880px, minimum-scale=0.4, maximum-scale=0.4, user-scalable=no" />');
		}
	}
	</script>
	</head>
	<body>
		<div id="wrapper">
			<div class="title">
				<img src="/resources/img/help/bg01_maint.png" width="95%">
			</div>
			<div class="content">
				<div class="note">
					The game is currently undergoing maintenance. <br />
					<br />
					<!--
					The game is undergoing an update. <br />
					<br />
					Maintenance Period: <br />
					Tuesday November 5, 2013 from 3:00 PM to 4:00 PM. (ET) <br />
					-->
					<br />
					We appreciate your understanding and apologize for any inconvenience caused by this maintenance. <br />
					<br />
				</div>
			</div>
			<div class="footer">
				<img src="/resources/img/help/bg03.png" width="95%">
			</div>
		</div>
	</body>
</html>