<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Module Team - Minutes</title>
<link rel="stylesheet" href="custom_css/deckblatt.css">
</head>
<body>
<div class="container">
  <header class="header">
    <img src="logo.png" alt="Modulteam Logo">
    <h1>Modulteam - Minutes</h1>
    <p>Mechatronics TE Application<br>03.04.2024</p>
  </header>

  <section class="members">
    <h2>Module Team Members</h2>
    <div class="memberList">
      <!-- Repeat this block for each member -->

      <?php
        #include "conn.php";
        #include "custom_css/deckblatt.css";
        $sql="SELECT * FROM mdt_members";
        $result=$conn->query($sql);
        if ($result->num_rows > 0) {
          // Output data of each row
          while ($row = $result->fetch_assoc()) {
              // Output table row
              echo '<input type="checkbox" id="member1">';
              echo '<span class="member-name">'.$row['name'].'</span>';
      }} else {
          echo "0 results";
      }
      ?>
      <!-- ... -->
    </div>
  </section>

  <section class="guests">
    <h2>Guests/Substitutes</h2>
    <div class="guestlist">
      <div class="add-guest">
        <button>Add Guest</button>
      </div>
      <!-- Repeat this block for each guest -->
      <div class="guest">
        <input type="checkbox" id="guest1">
        <input type="text" class="guest-name" placeholder="Last name, first name" aria-label="Guest name">
        <button class="remove-guest" aria-label="remove-guest" onclick="alert('Guest removed.')" type="button">X</button>
      </div>
      <!-- ... -->
    </div>
  </section>
</div>
</body>
</html>
