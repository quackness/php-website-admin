<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title>Add New Borrower</title>
  <meta name="GENERATOR" content="Quanta Plus">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
  if (isset($_POST['newborrowername'])) {
    // This is the postback so add the book to the database

    # Get data from form
    $newborrowername = trim($_POST['newborrowername']);
    $newborroweraddress = trim($_POST['newborroweraddress']);

    if (!$newborrowername || !$newborroweraddress) {
      printf ("You must specify both a name and an address");
      printf ("<br><a href=index.php>Return to home page </a>");
      exit();
    }

    $newborrowername = addslashes($newborrowername);
    $newborroweraddress = addslashes($newborroweraddress);

    # Open the database using the "librarian" account
    try {
      $db = new PDO("mysql:host=localhost;dbname=library", "librarian", "librarianpw");
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
      printf("Unable to open database: %s\n", $e->getMessage());
      printf ("<br><a href=index.php>Return to home page </a>");
    }

    // Prepare an insert statement and execute it
    $stmt = $db->prepare("insert into borrowers values (null, ?, ?)");
    $stmt->execute(array("$newborrowername", "$newborroweraddress"));
    printf ("<br>Borrower Added!");
    printf ("<br><a href=index.php>Return to home page </a>");
    exit;
  }

// Not a postback, so present the borrower entry form
?>

<h3>Add a new borrower</h3>
<hr>
You must enter both a name and an address
<form action="addborrower.php" method="POST">
<table bgcolor="#bdc0ff" cellpadding="6">
  <tbody>
    <tr>
      <td>Name:</td>
      <td><INPUT type="text" name="newborrowername"></td>
    </tr>
    <tr>
      <td>Address:</td>
      <td><INPUT type="text" name="newborroweraddress"></td>
    </tr>
    <tr>
      <td></td>
      <td><INPUT type="submit" name="submit" value="Add Borrower"></td>
    </tr>
  </tbody>
</table>
</form>
</body>
