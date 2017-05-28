<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Binary Thoughts</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
  </head>
  <body>
    <!-- If I want to get fancy, I can include: https://github.com/sitexw/FuckAdBlock-->

  <div class='site_header'>
    <h2>Think_Stream</h2>

    <form action="index.php" method="post">
      <input type="text" class="thought_submit" name="thought_text" placeholder="Pen a new thought"/>
    	<input type="submit"/>
    </form>

  </div>

  <!-- Beginning of database connection -->

  <?php

    $db_hostname = 'localhost';
    $db_database = 'thinkbucket'; // assuming this database already exists
    $db_username = 'root';
    $db_password = 'root';

    $db_conn = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);

    if (!$db_conn) {
      echo "Error: Unable to connect to MySQL." . PHP_EOL;
    	echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    	echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    	exit;
    }

    if ( !empty($_POST['thought_text']) ) {

      // user enters keywords that modify all "thoughts" in the database
      // if no keyword is entered, the text is treated as a new "thought" and added

      if ($_POST['thought_text'] == "DELETE") {
        $delete_query = "truncate thoughts";
        mysqli_stmt_execute(mysqli_prepare($db_conn, $delete_query));
        mysqli_stmt_close($mysqli_prepare($db_conn, $delete_query));
      }
      else if ($_POST['thought_text'] == "UNLIKE") {
        $delete_query = "UPDATE thoughts SET like_status='0'";
        mysqli_stmt_execute(mysqli_prepare($db_conn, $delete_query));
        mysqli_stmt_close($mysqli_prepare($db_conn, $delete_query));
      }
      else if ($_POST['thought_text'] == "LIKE") {
        $delete_query = "UPDATE thoughts SET like_status='1'";
        mysqli_stmt_execute(mysqli_prepare($db_conn, $delete_query));
        mysqli_stmt_close($mysqli_prepare($db_conn, $delete_query));
      }
      else {
        $thought_text = ($_POST['thought_text']);
        $like_status = 1;

        $insert_query = "INSERT INTO thoughts (thought_text, like_status) VALUES (?, ?)";
        $prepared_insert = mysqli_prepare($db_conn, $insert_query);

        mysqli_stmt_bind_param($prepared_insert, "si", $thought_text, $like_status);

        $success = mysqli_stmt_execute($prepared_insert);
        $count = mysqli_affected_rows($db_conn);

        // echo '<p>We have ' . $count . ' rows affected<br>
        // Success case: ' . $success . '</p><br>'; // for DEBUGGING

        mysqli_stmt_close($prepared_insert);
      }
    }

  // <!-- Beginning of "news feed" type divs -->

    $get_thoughts = "SELECT * FROM thoughts";

    $thoughts_result = mysqli_query($db_conn, $get_thoughts);

    if (mysqli_num_rows($thoughts_result) > 0) {
			// loop through the results with a while loop
			while ( $row = mysqli_fetch_assoc($thoughts_result)) {
				echo "<div class='text_thought'>";
				// row is an associative array, with the columns acting as the keys
				echo "<p> ${row['thought_text']} </p> ";
        echo "<div><p class='like_button'>Like</p><p class='like_count'>";
        if ($row['like_status'] == 1) {
          echo '&lt;3';
        }
        else {
          echo '&lt;/3';
        }
        echo "</p></div>";
        echo "</div>";
			}
		} else {
      echo "<div class='text_thought'>";
			echo "<p>No results found.</p>";
      echo "</div>";
		}

  ?>

  </body>
</html>
