<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title> Quiz Results</title>
    <style>
      table {
        border-collapse: collapse;
        width: 100%;
      }
      th, td {
        text-align: left;
        padding: 8px;
      }
      tr:nth-child(even){background-color: pink;
      }
      th {
        background-color: skyblue;
        color: white;
      }

      .mainButton{

          width: 40%;
          height: 50%;

          padding:30px;
          margin:10px;

          background-color: skyblue;
          text-align: center;
          font-size: 35px;
          border:solid 3px white;
          border-radius:10px;
        }

    </style>
  </head>
  <BODY oncontextmenu="return false" ondragstart="return false" onselectstart="return false">

    <?php
      $id = $_POST['id'];
      $score = $_POST['score'];
      $query = "INSERT INTO quiz " .
        "(ID, SCORE) " .
        "VALUES ( '$id', '$score')";

      if ($id != 0){ // 전송된 데이터가 0인경우 (확인만 하는경우)에는 insert 안함
      if(!($database = mysql_connect("164.125.36.45:3306", "201424465", "3118")))
        die("could not connect to database </body></html>");
      if(!mysql_select_db("201424465", $database))
        die("could not open Quiz database </body></html>");
      if(!($result = mysql_query($query, $database)))
      {
        print("<p>could not execute query!</p>");
        die(mysql_error()."</body><html>");
      }
      mysql_close($database);
      }
    ?>

    <?php
      $query = "SELECT *" . " FROM quiz";

      if(!($database = mysql_connect("164.125.36.45:3306", "201424465", "3118")))
        die("could not connect to database </body></html>");
      if(!mysql_select_db("201424465", $database))
        die("could not open Quiz database </body></html>");
      if(!($result = mysql_query($query, $database)))
      {
        print("<p>could not execute query!</p>");
        die(mysql_error()."</body><html>");
      }
      mysql_close($database);
    ?>
    <p style="text-align:center; font-size:30px;"> 시험 결과 <p>
    <table id = "data-table">
        <th> Num</th>
        <th> Student ID</th>
        <th> SCORE</th>
      <?php

        while ($row = mysql_fetch_row($result))
        {
          print("<tr>");

          foreach($row as $value){
            print("<td>$value</td>");
          }
          print( "</tr>");
        }
      ?>
    </table>

    <br>
    <button onclick="location.href='main.html'" type="button" class="mainButton">
    메인화면으로</button>
    </body>
  </html>
