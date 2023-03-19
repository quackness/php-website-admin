<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title>Check Book Out</title>
</head>
<body>
<?php
  if (isset($_GET['borrower'])) {
    // We know the borrower so go ahead and check the book out

    # Get data from form
    $bookid = trim($_GET['bookid']);      // From the hidden field
    $borrower = trim($_GET['borrower']);  // Entered by the user

    // Ideally should also verify that borrower exists
    if (!$borrower) {
      printf ("You must specify a valid borrower");
      printf ("<br><a href=index.php>Return to home page </a>");
      exit();
    }

    $bookid = addslashes($bookid);
    $borrower = addslashes($borrower);

    # Open the database using the "assistant" account
    try {
      $db = new PDO("mysql:host=localhost;dbname=library", "assistant", "assistantpw");
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
      printf("Unable to open database: %s\n", $e->getMessage());
      printf ("<br><a href=index.php>Return to home page </a>");
    }

    // Prepare an update statement and execute it
    date_default_timezone_set("UTC");
    $due = time() + 14 * 24 * 60 * 60;  // Book due back two weeks from now
    $due = date("Y-m-d", $due);
    $stmt = $db->prepare("update books set onloan=1, duedate=?, borrowerid=? where bookid = ?");
    $stmt->execute(array("$due", "$borrower", "$bookid"));
    printf ("<br>Book Checked Out!");
    printf ("<br><a href=index.php>Return to home page </a>");
    exit;
  }

// We don't have a borrower id yet so present a form to get one,
// then post back using a hidden field to pass through the bookid
// which came from the hand-crafted URL query string on the book
// search page
?>

<h3>Specify Borrower</h3>
<hr>
<form action="checkout.php" method="GET">
      Enter borrower ID:
      <INPUT type="text" name="borrower">
      <?php
      $bookid = trim($_GET['bookid']);
      echo '<INPUT type="hidden" name="bookid" value=' . $bookid . '>';
      ?>
      <INPUT type="submit" name="submit" value="Continue">
</form>
</body>
