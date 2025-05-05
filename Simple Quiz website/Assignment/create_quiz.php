<?php include 'session.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    .right-text {
        text-align: right;
        padding-right: 350px;
    }

    .flex-box {
        border-radius: 8px;
        width: 700px;
        height: auto;        
        padding: 10px;       
        border: 1px solid #000;   
        margin: 30px auto;
    }

    button {
        padding: 10px 20px;
        min-width: 130px;
        font-size: 1rem;
        border: none;
        border-radius: 20px;
        background-color: #2a83ff;
        color: #fff;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0056b3;
    }

    select {
        margin: 20px 0;
    }

    .flex-box input {
        margin: 10px 0;
    }
    </style>
    <script>
        function generateQuestions() {
            const numQuestions = document.getElementById('num_questions').value;
            const questionsContainer = document.getElementById('questions_container');
            questionsContainer.innerHTML = '';

            for (let i = 0; i < numQuestions; i++) {
                const questionDiv = document.createElement('div');
                questionDiv.innerHTML = `
                    <hr>
                    <h3>Question ${i + 1}</h3>
                    <textarea name="questions[]" required placeholder="Enter question text"></textarea>
                    <div>
                        <h4>Options</h4>
                        <input type="text" name="options[${i}][]" required placeholder="Option 1">
                        <input type="text" name="options[${i}][]" required placeholder="Option 2">
                        <input type="text" name="options[${i}][]" required placeholder="Option 3">
                        <input type="text" name="options[${i}][]" required placeholder="Option 4">
                        <label>Correct Answer: </label>
                        <select name="correct_answers[${i}]" required>
                            <option value="0">Option 1</option>
                            <option value="1">Option 2</option>
                            <option value="2">Option 3</option>
                            <option value="3">Option 4</option>
                        </select>
                    </div>
                `;
                questionsContainer.appendChild(questionDiv);
            }
        }
    </script>
</head>
<body>
    <?php include 'header.php'; ?>
    <form method="POST">
    <div class = "flex-box">
        <label>Quiz Title:</label>
        <input type="text" name="quiz_title" required>
        <br>

        <label>Number of Questions:</label>
        <input type="number" id="num_questions" name="num_questions" required min="1" onchange="generateQuestions()"><br>
        <br>
        <div id="questions_container"></div><br>
    </div>
    <div class ="right-text">
        <button type="submit">Create Quiz</button>
    </div>
    </form>
    
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ensure the session has the lecturer's ID
        if (!isset($_SESSION['id'])) {
            die('Error: Lecturer not logged in.');
        }

        $lecturer_id = $_SESSION['id']; // Get lecturer ID from session
        $quiz_title = $_POST['quiz_title'];
        $num_questions = intval($_POST['num_questions']);
        $questions = $_POST['questions'];
        $options = $_POST['options'];
        $correct_answers = $_POST['correct_answers'];

        // Establish the database connection
        $db = mysqli_connect('localhost', 'root', '', 'rwdd');
        if (mysqli_connect_errno()) {
            die('Failed to connect to MySQL: ' . mysqli_connect_error());
        }

        // Start transaction
        mysqli_begin_transaction($db);

        try {
            // Insert into quizzes table
            $query = 'INSERT INTO quizzes (title, created_by) VALUES (?, ?)';
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, 'ss', $quiz_title, $lecturer_id);
            mysqli_stmt_execute($stmt);
            $quiz_id = mysqli_insert_id($db);

            // Insert questions and options
            foreach ($questions as $index => $question_text) {
                // Insert question
                $query =
                    'INSERT INTO questions (quiz_id, content) VALUES (?, ?)';
                $stmt = mysqli_prepare($db, $query);
                mysqli_stmt_bind_param($stmt, 'is', $quiz_id, $question_text);
                mysqli_stmt_execute($stmt);
                $question_id = mysqli_insert_id($db);

                // Insert options
                foreach ($options[$index] as $option_index => $option_text) {
                    $is_correct =
                        $correct_answers[$index] == $option_index ? 1 : 0;
                    $query =
                        'INSERT INTO options (question_id, content, is_correct) VALUES (?, ?, ?)';
                    $stmt = mysqli_prepare($db, $query);
                    mysqli_stmt_bind_param(
                        $stmt,
                        'isi',
                        $question_id,
                        $option_text,
                        $is_correct,
                    );
                    mysqli_stmt_execute($stmt);
                }
            }

            // Commit transaction
            mysqli_commit($db);
            echo '<script> alert("Quiz successfully created!"); window.location.href = "lecturer_dashboard.php"; </script>';
        } catch (Exception $e) {
            // Roll back transaction on error
            mysqli_rollback($db);
            echo 'Error: ' . $e->getMessage();
        } finally {
            mysqli_close($db);
        }
    } ?>
</body>
</html>
