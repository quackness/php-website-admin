<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title>Administrative Book Search</title>
</head>
<body>
<h3>Book Search Results</h3>
<hr>
<?php

# This version uses PDO (not mysqli)
# This is the administrative book search --
# It includes links to check books out and in

# Get data from form
$searchtitle = trim($_POST['searchtitle']);
$searchauthor = trim($_POST['searchauthor']);

if (!$searchtitle && !$searchauthor) {
  printf ("You must specify either a title or an author");
  exit();
}

$searchtitle = addslashes($searchtitle);
$searchauthor = addslashes($searchauthor);

# Open the database
try {
  $db = new PDO("mysql:host=localhost;dbname=library", "assistant", "assistantpw");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
  printf("Unable to open database: %s\n", $e->getMessage());
}

# Build the query. You are allowed to search on title, author, or both

$query = " select * from books";
if ($searchtitle && !$searchauthor) { // Title search only
  $query = $query . " where title like '%" . $searchtitle . "%'"; 
}
if (!$searchtitle && $searchauthor) { // Author search only
  $query = $query . " where author like '%" . $searchauthor . "%'";
}
if ($searchtitle && $searchauthor) { // Title and Author search
  $query = $query . " where title like '%" . $searchtitle . "%' and author like '%" . $searchauthor . "%'"; 
}

// printf ("Debug: running the query %s <br>", $query);

try {
  $sth = $db->query($query);
  $bookcount = $sth->rowCount(); # Only works for MySQL
  if ($bookcount == 0) {
    printf("Sorry, we did not find any matching books");
    printf("<br> <a href=index.php>Back to home page</a>");
    exit;
  }

  printf('<table bgcolor="%s" cellpadding="6">', "#dddddd");
  printf('<tr><b><td>Title</td> <td>Author</td> <td>Check Out</td> <td> Check In </td></b> </tr>');
  while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    // We add links on each row to allow the assistant to check the book out or in
    $checkoutanchor = "-";
    $checkinanchor  = "-";
    if (! $row["onloan"])
      $checkoutanchor = '<a href="checkout.php?bookid=' . urlencode($row["bookid"]) . '">Check Out</a>';
    else
      $checkinanchor  = '<a href="checkin.php?bookid=' . urlencode($row["bookid"]) . '">Check In</a>';
    printf("<tr> <td> %s </td> <td> %s </td> <td> %s </td> <td> %s </td> </tr>",
           htmlentities($row["title"]),
           htmlentities($row["author"]),
           $checkoutanchor,
           $checkinanchor);
  }
}
catch (PDOException $e) {
  printf("We had a problem: %s\n", $e->getMessage());
}
printf("</table>");
printf("<br> We found %s matching books", $bookcount);
printf("<br> <a href=index.php>Back to home page</a>");
?>
</body>
</html>
