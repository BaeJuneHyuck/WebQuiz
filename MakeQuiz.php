<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title> Make Quiz</title>
    <link rel ="stylesheet" type = "text/css" href="Quiz.css" >
  </head>
  <BODY oncontextmenu="return false" ondragstart="return false" onselectstart="return false">
    <?php
      $question = $_POST['question'];
      $answer1 = $_POST['answer1'];
      $answer2 = $_POST['answer2'];
      $answer3 = $_POST['answer3'];
      $answer4 = $_POST['answer4'];
      if ($answer1){// 객관식 보기는 &를통해서 하나의 문자열로 저장합니다
        $answers = $answer1."&".$answer2."&".$answer3."&".$answer4;
      }else{  // 주관식일경우 객관식답은 비워두고 correct를 사용합니다
        $answers = NULL;
      }
      $correct = $_POST['correct'];
      $query = "INSERT INTO questions " .
        "( question, answers, correct ) " .
        "VALUES ( '$question', '$answers', '$correct')";

      if(!($database = mysql_connect("164.125.36.45:3306", "201424465", "3118")))
        die("could not connect to database </body></html>");
      if(!mysql_select_db("201424465", $database))
        die("could not open products database </body></html>");
      if(!($result = mysql_query($query, $database))){
        print("<p>could not execute query!</p>");
        die(mysql_error()."</body><html>");
      }

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

    <p style="text-align:center; font-size:30px;"> 문제 목록 </p>
    <table id = "quizlist">
        <th> No</th>
        <th> question</th>
        <th> answers</th>
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

    <br>

    <button onclick="location.href='MakeQuiz.html'" type="button" class="Button">
    계속 추가하기</button>
    <button onclick="location.href='main.html'" type="button" class="Button">
    메인화면으로</button>
    </body>
    </html>
