<?php include 'session.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
        }

        .question {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f0f4f8;
            border-radius: 8px;
        }

        .question p {
            font-size: 1.2em;
            font-weight: bold;
            color: #555;
        }

        .options {
            margin-left: 20px;
            margin-top: 10px;
        }

        .options label {
            font-size: 1em;
            display: block;
            margin-bottom: 10px;
            padding: 8px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        
        .options label:hover {
            background-color: #e3f2fd;
        }

        .submit-button {
            display: block;
            width: 100%;
            text-align: center;
            background-color: #4285F4;
            color: white;
            padding: 15px;
            font-size: 1em;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .submit-button:hover {
            background-color: #357ae8;
        }

        .thank-you-message {
            text-align: center;
            font-size: 1.2em;
            color: #28a745;
            margin-top: 20px;
        }

        body.dark-mode p, div {
            color: black;
        }
    </style>
    <script>
        function validateQuiz() {
            const questions = document.querySelectorAll('.question');
            for (let i = 0; i < questions.length; i++) {
                const options = questions[i].querySelectorAll('input[type="radio"]');
                const isChecked = Array.from(options).some(option => option.checked);
                if (!isChecked) {
                    alert(`Please answer question ${i + 1}.`);
                    return false; // Prevent form submission
                }
            }
            return true; // Allow form submission
        }
    </script>
</head>
<body>
<?php include 'header.php'; ?>

    <div class="container">
        <h1>Quiz Questions</h1>
        <form method="post" action="" onsubmit="return validateQuiz();">
            <?php
            include 'db_conn.php';

            $quizid = base64_decode($_GET['quiz_id']);

            // Query to get all questions
            $sql = "SELECT question_id, content FROM questions WHERE quiz_id = '{$quizid}'";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                // Output data of each row
                $question_number = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='question'>";
                    echo '<p>' .
                        $question_number .
                        '. ' .
                        htmlspecialchars($row['content']) .
                        '</p>';

                    // Query to get options for the current question
                    $question_id = $row['question_id'];
                    $option_sql =
                        'SELECT option_id, content FROM options WHERE question_id = ' .
                        $question_id;
                    $option_result = $conn->query($option_sql);

                    if ($option_result && $option_result->num_rows > 0) {
                        echo "<div class='options'>";
                        while ($option_row = $option_result->fetch_assoc()) {
                            echo "<label><input type='radio' name='question_$question_id' value='" .
                                htmlspecialchars($option_row['option_id']) .
                                "'> " .
                                htmlspecialchars($option_row['content']) .
                                '</label>';
                        }
                        echo '</div>';
                    }

                    echo '</div>';
                    $question_number++;
                }
            } else {
                echo '<p>No questions available.</p>';
            }
            ?>
            <button type="submit" name="submit" class="submit-button">Submit</button>
        </form>
        <?php
        if (isset($_POST['submit'])) {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'question_') === 0) {
                    $question_id = str_replace('question_', '', $key);
                    $option_id = $value;
                    $user_id = $_SESSION['id'];

                    // Insert answer into the database
                    $insert_sql = "INSERT INTO answers (student_id, question_id, option_id) VALUES ('$user_id', '$question_id', '$option_id')";
                    $conn->query($insert_sql);
                }
            }
            echo "<script> window.location.href = 'results.php?quiz_id={$quizid}'; </script>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
