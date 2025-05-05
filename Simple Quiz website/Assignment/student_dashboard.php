<?php include 'session.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        #top-container {
            display: flex;
            justify-content: center;
        }

        #container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            width: 100%;
            max-width: 1300px;
            margin: 20px auto;
        }

        #container div {
            background-color: white;
            box-sizing: border-box;
            position: relative;
            width: 400px;
            height: 200px;
            padding: 20px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        #container div:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #container p {
            font-size: 0.9em;
            color: #555;
            font-style: italic;
        }

        body.dark-mode #container div {
            background-color: #191919;
            border: none;
        }

        body.dark-mode #container h2 {
            color: white;
        }

        body.dark-mode #container div:hover {
            background-color: #2B2B2B;
        }

        @media (max-width: 1320px) {
            #container {
                max-width: 900px;
            }
        }

        @media (max-width: 900px) {
            #container {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <?php
    include 'header.php';
    echo "<div id='top-container'>";
    include 'search_bar.php';
    echo '</div>';

    include 'db_conn.php';

    // Search algorithm
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['searchField'])) {
        $sql = "SELECT quizzes.*, users.name, 
            (SELECT COUNT(answers.answer_id) 
             FROM answers
             INNER JOIN questions ON answers.question_id = questions.question_id 
             WHERE questions.quiz_id = quizzes.quiz_id AND answers.student_id = '{$_SESSION['id']}') AS answer_count 
            FROM quizzes 
            INNER JOIN users ON quizzes.created_by = users.id 
            WHERE title LIKE '%{$_POST['searchField']}%' 
            ORDER BY quizzes.quiz_id";
    } else {
        $sql = "SELECT quizzes.*, users.name, 
            (SELECT COUNT(answers.answer_id) 
             FROM answers
             INNER JOIN questions ON answers.question_id = questions.question_id 
             WHERE questions.quiz_id = quizzes.quiz_id AND answers.student_id = '{$_SESSION['id']}') AS answer_count 
            FROM quizzes 
            INNER JOIN users ON quizzes.created_by = users.id 
            ORDER BY quizzes.quiz_id";
    }
    $result = mysqli_query($conn, $sql);

    // Display quizzes
    if (mysqli_num_rows($result) > 0) {
        echo "<div id='container'>";
        while ($row = mysqli_fetch_assoc($result)) {
            $quiz_id = base64_encode($row['quiz_id']);
            $title = $row['title'];
            $name = $row['name'];
            $answered = $row['answer_count'] > 0;

            echo <<<HTML
            <div onclick="checkRetake('$quiz_id', '$answered');" style="cursor: pointer;">
                <h2>$title</h2>
                <p>Lecturer: $name</p>
            </div>
            HTML;
        }
        echo '</div>';
    } else {
        echo <<<HTML
        <div style="text-align: center; margin-top: 50px; color: #555;">
            <h2>No quizzes found</h2>
        </div>
        HTML;
    }

    mysqli_close($conn);
    ?>

    <script>
        function checkRetake(quizID, answered) {
            if (answered == true) {
                alert('Quiz retakes are not available.');
            } else {
                window.location.href = 'answer_quiz.php?quiz_id=' + quizID;
            }
        }
    </script>
</body>
</html>