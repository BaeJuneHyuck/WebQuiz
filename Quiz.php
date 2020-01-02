<!DOCTYPE html>
<html>
  <head>
    <meta charset = "utf-8">
    <link rel ="stylesheet" type = "text/css" href="Quiz.css" >
    <title>Quiz</title>
  </head>
  <body>

    <BODY oncontextmenu="return false" ondragstart="return false" onselectstart="return false">

    <div id="myProgress">
      <div id="myBar"></div>
    </div>

    <button type="button" id = "startButton">Start Quiz!</button>

    <div id="quiz"></div>
    <button id="next">Next</button>
    <div id="results"></div>

    <button id="submit"  class="mainButton">점수 제출</button>
    <button id="main" onclick="location.href='main.html'" type="button" class="mainButton">
    메인으로</button>
    <audio id="bgm" src="./source/bgm.mp3" preload="auto"loop></audio>

  <script>
  <?php
   // json 한글 깨짐 방지
    function han ($s) { return reset(json_decode('{"s":"'.$s.'"}')); }
    function to_han ($str) { return preg_replace('/(\\\u[a-f0-9]+)+/e','han("$0")',$str); }
  ?>
  <?php

    $query = "SELECT *" . " FROM questions";

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

    $arrr = array();
    $i = 0 ;
    while ($row = mysql_fetch_row($result))
    {
      $arrr[$i++] = $row;
    }
    $json = json_encode($arrr);
  ?>

    var present = 0;
    var progress = 0;
    var startButton;
    var mainButton;
    var progressbar;
    var quizContainer;
    var resultsContainer;
    var nextButton;
    var submitButton;
    var answerSheet = [];
    var myQuestions = [];
    var score = 0;
    var MAXQUIZ;

    function convertToArrayOfObjects(data) {
        var keys = ["no","question","answers","correct"];
        var i = 0, k = 0,
            obj = null,
            output = [];
        for (i = 0; i < data.length; i++) {
            obj = {};

            for (k = 0; k < keys.length; k++) {
                obj[keys[k]] = data[i][k];
            }

            output.push(obj);
        }
        return output;
    }

    function loadQuiz(){
      // 서버의 문제json 데이터를 받아서 한글로 바꾼다음 object의 array로 변경
      myQuestions = <?= to_han(json_encode($json)) ?>;
      myQuestions = JSON.parse(myQuestions);
      myQuestions = convertToArrayOfObjects(myQuestions);
    }

    function buildQuiz(index) {
        const output = [];
        const answers = [];
        var currentQuestion = myQuestions[index];
        if(currentQuestion.answers){ // 객관식이면 true 주관식이면 false
          //객관식일경우 단일 문자열인 answers를 배열로 바꾼뒤 각각 출력합니다.
          currentQuestion.answers = currentQuestion.answers.split('&');
          if(currentQuestion.correct.length == 1){
            // 객관식에 단일정답인 경우
            for (letter in currentQuestion.answers) {
              answers.push(
                `<label>
                  <input type="radio" name="question${index}" value="${letter}">
                  ${String.fromCharCode(letter.charCodeAt(0)+49)} :
                  ${currentQuestion.answers[letter]}
                </label><br>`
              );
            }
            output.push(
              `<div class="question"> ${currentQuestion.question} </div>
                <div class="answers"> ${answers.join("")} </div><br>`
            );
          }else{
            // 객관식에 복수정답인경우
            for (letter in currentQuestion.answers) {
              answers.push(
                `<label>
                  <input type="checkbox" id="question${index}" value="${letter}" class="question">
                  ${String.fromCharCode(letter.charCodeAt(0)+49)} :
                  ${currentQuestion.answers[letter]}
                </label><br>`
              );
            }
            output.push(
              `<div class="question"> ${currentQuestion.question} </div>
                <div class="answers"> ${answers.join("")} </div><br>`
            );
          }
        }else{
          // 주관식인경우
          answers.push(
            `<label>
              <input type="text" name="question${index}">
            </label><br>`
          );
          output.push(
            `<div class="question"> ${currentQuestion.question} </div>
              <div class="answers"> ${answers.join("")} </div><br>`
          );
        }

        // 위에서 작성한 문제, 답을 텍스트로 출력
        var text = output.join('');
        document.getElementById("quiz").innerHTML = text;
    }


    function shuffle(array) {
      var currentIndex = array.length
      var temporaryValue;
      var randomIndex;

      while (0 !== currentIndex) {

        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex -= 1;

        temporaryValue = array[currentIndex];
        array[currentIndex] = array[randomIndex];
        array[randomIndex] = temporaryValue;
      }

      return array;
    }


    function showResult(){
      // make a result table!
      var outputTable = [];
      submitButton.style.display = "block";
      mainButton.style.display = "block";

      outputTable.push(`
        <table>
          <th> Num</th>
          <th> Quiz</th>
          <th> correct answer</th>
          <th> your answer</th>
          `);
      myQuestions.forEach((currentQuestion, questionNumber) => {
        if (questionNumber<MAXQUIZ){
          outputTable.push(`<tr>`);
          var tableText = "<td> " + (questionNumber+1) + "번</td><td>"+ currentQuestion.question + "</td><td>" + currentQuestion.correct;
          if (answerSheet[questionNumber] === currentQuestion.correct) {
              score++;
              tableText += "</td><td style=\"color:blue;\">" + answerSheet[questionNumber] +"</td>";
          }else{
              tableText += "</td><td style=\"color:red ;\">" + answerSheet[questionNumber] +"</td>";
          }
          outputTable.push(tableText);
          outputTable.push(`</tr>`);
        }
      });
      outputTable.push(`</table> <p> 총 점수 : ` + score + '점!');
      var text = outputTable.join(''); // convert array output into a single text;
      document.getElementById("quiz").innerHTML = text;
    }

    function goNext() {
        // 객관식은 눌린 버튼을, 주관식은 문자열을 정답으로 저장
        if (myQuestions[present].answers){  //객관식
            if(myQuestions[present].correct.length == 1){// 객관식 단일정답
              var selector = `input[name=question${present}]:checked`;
              var code = eval((document.querySelector(selector) || {}).value);
              code += 97;
              var userAnswer = String.fromCharCode(code); // make char from unicorde
            }else { // 객관식 복수정답
              var userAnswer = [];
              var inputElements = document.getElementsByClassName('question');
              for(var i=0; inputElements[i]; ++i){
                  if(inputElements[i].checked){
                       userAnswer.push(String.fromCharCode(i + 96));
                  }
              }
              userAnswer = userAnswer.toString();
            }
        }else{// 주관식. 입력된 텍스트 그대로 입력으로
          var selector = `input[name=question${present}]`;
          var userAnswer = (document.querySelector(selector) || {}).value;
        }
        answerSheet.push(userAnswer);

        // 마지막 문제엿으면 문제 지우고 제출버튼 출력, 아니면 다음문제 출력
        present = present+1;
        if(present <MAXQUIZ){
          var id = setInterval(progressbarControl, 40);
          buildQuiz(present);
        }else {
          document.getElementById("quiz").innerHTML = "";
          submitButton.style.display = "block";
          nextButton.style.display = "none";
          progressbar.style.width = 100 + '%';
          showResult();
        }

    }

    function progressbarControl() {
      if(progress < present/MAXQUIZ*100){
        progress = progress + 1;
        progressbar.style.width = progress + '%';
        progressbar.innerHTML = progress * 1 + '%';
      }
      clearInterval(id);
    }

    function getStart(){
      present = 0;
      startButton.disabled = true;
      startButton.style.display = "none";
      progressbar.style.display = "block";
      progressbar2.style.display = "block";
      nextButton.style.display = "block";
      document.getElementById('bgm').play();

      // 문제갯수를 선택후 배열 shuffle을 이용하여 랜덤하게 문제를 제출합니다
      MAXQUIZ = prompt("몇개의 문제를 풀지 입력해주세요(1~10)");
      if (MAXQUIZ >10 ) MAXQUIZ = 10;
      if (MAXQUIZ < 1 ) MAXQUIZ = 1;
      shuffle(myQuestions);
      buildQuiz(present);
    }

    function submit(){
      // 학번을 입력받아 id, 점수를 서버에 post형식으로 from
      // ajax를 사용하지않고 from element를 직접 html에 생성후 submit
      var id = prompt("학번을 입력해주세요");
      document.body.innerHTML += '"<form id="dynForm" action="QuizSubmit.php" method="post"><input type="hidden" name="id" value="'+ id + '"><input type="hidden" name="score" value="'+ score + '"></form> "';
      document.getElementById("dynForm").submit();
    }

    function start(){
      loadQuiz();
      startButton = document.getElementById("startButton");
      startButton.addEventListener("click",getStart,false);
      progressbar = document.getElementById("myBar");
      progressbar2 = document.getElementById("myProgress");

      quizContainer = document.getElementById("quiz");
      resultsContainer = document.getElementById("results");

      nextButton = document.getElementById("next");
      nextButton.addEventListener("click", goNext);
      submitButton = document.getElementById("submit");
      submitButton.addEventListener("click", submit);
      mainButton = document.getElementById("main");

      progressbar.style.display = "none";
      progressbar2.style.display = "none";
      nextButton.style.display = "none";
      submitButton.style.display = "none";
      mainButton.style.display = "none";
    }

    window.addEventListener("load",start,false);

  </script>
  </body>
</html>
