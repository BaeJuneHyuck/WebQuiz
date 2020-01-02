<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title> Quiz List</title>
    <link rel ="stylesheet" type = "text/css" href="Quiz.css" >
  </head>
  <BODY oncontextmenu="return false" ondragstart="return false" onselectstart="return false">

    <?php
      $query = "SELECT *" . " FROM questions";

      if(!($database = mysql_connect("164.125.36.45:3306", "201424465", "3118")))
        die("could not connect to database </body></html>");
      if(!mysql_select_db("201424465", $database))
        die("could not open products database </body></html>");
      if(!($result = mysql_query($query, $database)))
      {
        print("<p>could not execute query!</p>");
        die(mysql_error()."</body><html>");
      }

      mysql_close($database);
    ?>

    <p style="text-align:center; font-size:25px;"> 문제 목록 </p>
    <table id = "quizlist">
        <th> No</th>
        <th> question</th>
        <th> answers(주관식일때 공백)</th>
        <th> correctAnswer</th>
      <?php
        while ($row = mysql_fetch_row($result))
        {
          print("<tr>");

          foreach($row as $value){
            if(is_array($value)){
              echo "<pre>";
               print_r($value);
              echo "</pre>";
            }else{
              print("<td>$value</td>");
            }
          }
          print( "</tr>");
        }
      ?>
    </table>

    </script>
    <br>
    <button onclick="location.href='MakeQuiz.html'" type="button" class="Button">
    새로운 문제 추가</button>
    <button onclick="location.href='main.html'" type="button" class="Button">
    메인화면으로</button>
    </body>
    </html>
