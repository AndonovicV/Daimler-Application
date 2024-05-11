<!DOCTYPE HTML>
<!--
	Dimension by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>

<head>
	<!--TEMPLATE-->
	<title>DOMM</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="template/css/main.css" />

	<!--ONLINE LIBRARY-->
	<!--Link to Bootstrap CSS <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
	<!--Link to Bootstrap JS <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
	<!--Link to jQuery-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

	<!--LOCAL PLUGINS-->
	<!--Link to Bootstrap CSS-->
	<link href="plugins\bootstrap-5.3.3-dist\css\bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<!--Link to Bootstrap JS-->
	<script src="plugins\bootstrap-5.3.3-dist\js\bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>


	<!--Link to Virtual select Plugin CSS-->
	<link rel="stylesheet" href="plugins\virtual_select\virtual-select.min.css">
	<!--Link to Virtual select Plugin JS-->
	<script src="plugins/virtual_select/virtual-select.min.js"></script>

	<!--Link to Tooltip CSS-->
	<link rel="stylesheet" href="plugins\tooltip\tooltip.min.css" />
	<!--Link to Tooltip JS-->
	<script src="plugins\tooltip\tooltip.min.js"></script>


	<!--CUSTOM FILES  Make sure to load CSS & JS AFTER Bootstrap and jQuery-->
	<!--Link to CSS-->
	<link rel="stylesheet" href="custom_css\style.css" />
	<!--Link to JS-->
	<script src="custom_js\datatable.js"></script>
</head>

<body class="is-preload">
	<!-- Wrapper -->
	<div id="wrapper">
		<!-- Header -->
		<header id="header">
			<!-- <div class="logo">
							<h1>DOMM</h1>
						</div> -->
			<div class="content">
				<div class="inner">
					<h1>DOMM</h1>
					<p>A fully responsive meeting minutes design, integrated inside the <a href="https://www.daimlertruck.com/en">Cockpit</a> and connected<br />
						within the <a href="https://www.daimlertruck.com/en/products">ICM</a> system.</p>
				</div>
			</div>
			<nav>
				<ul>
					<li><a href="mt_agenda.php">MT Agenda</a></li>
					<li><a href="#contact">Protokoll</a></li>
					<li><a href="php-attendance/?page=attendance">Deckblatt</a></li>
					<!-- <li><a href="#list_mm">List MM</a></li> -->
					<li><a href="plugins\calendar\calendar.html">Calendar</a></li>
					<li><a href="#personalTaskModal" data-bs-toggle="modal">Personal Task</a></li>
				</ul>
			</nav>
		</header>
		<!-- Main -->
		<div id="main">
			<!-- Intro -->
			<article id="list_mm">
				<h2 class="major">List MM</h2>
				<div class="input-group rounded">
					<input type="search" class="form-control rounded" placeholder="Search by Title" aria-label="Search" aria-describedby="search-addon" />
				</div>
				<br>
				<div style="display: flex;">
					<a href="#contact" class="button primary">NEW</a>
					<div style="width: auto; padding: 5px; margin-left: auto;">
						<select class="text">
							<option value="" disabled selected>Filter</option>
							<option value="option1">Filter 1</option>
							<option value="option2">Filter 2</option>
							<option value="option3">Filter 3</option>
							<option value="option3">None</option>
						</select>
					</div>
				</div>
				<a href="#mt_agenda" class="box" style="margin-top: 20px; display: block;">
					<h3>Title 1</h3>
					Date & Time:
					</br>
					Location:
				</a>
			</article>

			<article id="Deckblatt">
				<img src="logo.png" alt="Modulteam Logo">
				<h3 class="major">Modulteam - Minutes</h3>
				<div style="display: flex;">
					<div style="width: auto; padding: 5px; margin-left: auto;">
						<select class="text">
							<option vlaue="" disable selected>Department</option>
							<option value="option1">Department 1</option>
							<option value="option2">Department 2</option>
							<option value="option3">Department 3</option>
							<option value="option3">None</option>
						</select>
					</div>
				</div>
				<div class="container">
					<section class="members">
						<h2>Module Team Members</h2>
						<div class="member-list"></div>
						<div class="member">
							<input type="checkbox" id="member1">
							<input type="text" class="member-name" placeholder="Last name, first name" aria-label="Member name">
						</div>
					</section>
					<section class="guests">
						<h2>Guests/Substitutes</h2>
						<div class="guest-list">
							<div class="add-guest">
								<button>Add Guest</button>
							</div>
							<div class="guest">
								<input type="checkbox" id="guest1">
								<input type="text" class="guest-name" placeholder="Last name, first name" aria-label="Guest name">
								<button class="remove-guest">Remove</button>
							</div>
						</div>
					</section>
				</div>
			</article>


			<!-- MT Agenda -->
			<article id="mt_agenda">
				<h3 class="major">MT Agenda</h3>
				<script>
					$(document).ready(function() {
						$('#mt_agenda').load("mt_agenda.php");
					});
				</script>
			</article>
			<!-- I changed the headers but ID's left the same for the sake of the function. Can change later. Voja -->

			<!-- ADD MM -->
			<article id="contact">
				<h2 class="major" style="color: white">New MM</h2>
				<form method="post" action="#">
					<div class="fields">
						<div class="field">
							<label for="name" style="text-align: center;">Title</label>
							<input type="text" name="name" id="name" style="text-align: center; color: white;">
						</div>
						<div class="field half">
							<label for="email">Attendees</label>
							<!-- <div data-tooltip= "Possible tooltip location" data-tooltip-position="right"> -->
							<select id="multipleSel" multiple name="native-select" data-search="false" data-silent-initial-value-set="true">
								<option value="1">Jonas</option>
								<option value="2">Amanda</option>
								<option value="3">Christof</option>
								<option value="3">Erwin</option>
								<option value="3">Joachim</option>
								<option value="3">Marcus</option>
								<option value="3">Hannes</option>
								<option value="3">Svenja</option>
								<option value="3">Sebastian</option>
							</select>
							<!-- </div> -->
							<script>
								VirtualSelect.init({
									ele: '#multipleSel'
								});
							</script>
						</div>
						<div class="field">
							<label for="message">Agenda</label>
							<textarea name="message" id="message" rows="1" class="text"></textarea>
						</div>
						<div class="field third">
							<label for="action">Action</label>
							<textarea name="action" id="action" rows="1" class="text"></textarea>
						</div>
						<div class="field third">
							<label for="responsible">Responsible</label>
							<textarea name="responsible" id="responsible" rows="1" class="text"></textarea>
						</div>
						<div class="field third">
							<label for="deadline">Deadline</label>
							<textarea name="deadline" id="deadline" rows="1" class="text"></textarea>
						</div>
						<div class="field">
							<label for="summary">Summary</label>
							<textarea name="summary" id="summary" rows="4" class="text"></textarea>
						</div>
					</div>
					<ul class="actions">
						<a href="#list_mm" class="button primary">Post MM</a>
						<a style="margin-left: 1.5em;" href="#" class="button secondary ">Cancle</a>
					</ul>
				</form>
				<ul class="icons">
					<i class="fa-brands fa-microsoft"></i>
					<li><a href="#" class="icon brands fa-twitter"><span class="label">Twitter</span></a></li>
					<li><a href="#" class="icon brands fa-facebook-f"><span class="label">Facebook</span></a></li>
					<li><a href="#" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
					<li><a href="#" class="icon brands fa-github"><span class="label">GitHub</span></a></li>
				</ul>
			</article>


			<!-- Calendar -->
			<article id="about">
			</article>

			<div class="modal fade" id="personalTaskModal" tabindex="-1" aria-labelledby="personalTaskLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<!-- Display the selected agenda ID in the modal title -->
							<h1 class="modal-title fs-5" id="personalTaskLabel">Selected Agenda ID</h1>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body"> </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary">Save changes</button>
						</div>
					</div>
				</div>
			</div>
			<!--Select Agenda For Personal Task Comment -->
			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<h3>Select or Create Agenda:</h3>
						<select id="agendaSelect" data-search="true" style="margin-left: 5em;">
							<option value="">Select Agenda...</option>
							<?php
							include 'conn.php';
							// Fetching data from mt_agenda_list table
							$sql = "SELECT * FROM mt_agenda_list";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									echo "<option value='" . $row["agenda_id"] . "'>" . $row["agenda_name"] . "</option>";
								}
							}
							?>
						</select>
						<script> VirtualSelect.init({ele: '#agendaSelect'}); </script>
					</div>
				</div>
			</div>
		</div>

		<!-- Footer -->
		<footer id="footer">
			<p class="copyright">&copy; Untitled. Design: <a href="https://html5up.net">HTML5 UP</a>.</p>
		</footer>
	</div>

	<!-- Background -->
	<div id="bg"></div>

	<!-- Template Scripts --> <!-- I don't know why they don't work inside head section, so just leave them here -->
	<script src="template/js/browser.min.js"></script>
	<script src="template/js/breakpoints.min.js"></script>
	<script src="template/js/util.js"></script>
	<script src="template/js/main.js"></script>
</body>

</html>