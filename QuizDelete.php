<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title> Quiz Delete</title>
  </head>
  <BODY oncontextmenu="return false" ondragstart="return false" onselectstart="return false">
    <?php
      $no = $_POST['no'];
      $question = $_POST['question'];
      $answers = $_POST['answers'];
      $correct = $_POST['correct'];
      $query = "DELETE FROM quiz WHERE id = $no";

      if(!($database = mysql_connect("164.125.36.45:3306", "201424465", "3118")))
        die("could not connect to database </body></html>");
      if(!mysql_select_db("201424465", $database))
        die("could not open Quiz database </body></html>");
      if(!($result = mysql_query($query, $database))){
        print("<p>could not execute query!</p>");
        die(mysql_error()."</body><html>");
      }
      print("You success to Delete data!");
      mysql_close($database);
    ?>

    </body>
  </html>
