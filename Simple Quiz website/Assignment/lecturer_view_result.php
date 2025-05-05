<?php include 'session.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .title-box {
            width: 500px;
            margin: 20px auto;
            padding: 15px;
            border: 2px solid black;
            border-radius: 8px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            background-color: white;
        }

        .question-btn {
            display: block;
            width: 80%;
            height: 100px;
            margin: 10px auto;
            font-size: 20px;
            font-weight: bold;
            color: black;
            background-color: white;
            border: 2px solid black;
            border-radius: 5px;
            cursor: pointer;
            text-align: left;
            position: relative;
        }

        .question-btn:hover {
            background-color: grey;
        }

        .average-score {
            font-size: 18px;
            font-weight: normal;
            margin-top: 5px;
            color: black;
        }

        .back-btn {
            display: block;
            width: 300px;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: black;
            background-color: white;
            border: 2px solid black;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: grey;
        }

        body.dark-mode .title-box {
             color: #2B2B2B;
        }

        hr {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php
    include 'header.php';
    include 'db_conn.php';

    // Get the quiz_id from the URL and decode it
    $quiz_id = isset($_GET['quiz_id']) ? base64_decode($_GET['quiz_id']) : null;

    if (!$quiz_id) {
        echo "<script>alert('No quiz selected.'); window.location.href='lecturer_dashboard.php';</script>";
        exit();
    }

    // Fetch the title of the selected quiz
    $sql_title = "SELECT title FROM quizzes WHERE quiz_id = '$quiz_id'";
    $title_result = mysqli_query($conn, $sql_title);
    $quiz = mysqli_fetch_assoc($title_result);

    if (!$quiz) {
        echo "<script>alert('Quiz not found.'); window.location.href='lecturer_dashboard.php';</script>";
        exit();
    }

    // Fetch questions for the selected quiz
    $sql_questions = "SELECT question_id, content FROM questions WHERE quiz_id = '$quiz_id'";
    $questions_result = mysqli_query($conn, $sql_questions);

    if (!$questions_result) {
        die('Query failed: ' . mysqli_error($conn));
    }
    ?>
    <div class="title-box"><?= htmlspecialchars($quiz['title']) ?></div>

    <div>
        <?php while ($row = mysqli_fetch_assoc($questions_result)): ?>
            <?php
            $question_id = base64_encode($row['question_id']);

            // Calculate the average score for each question
            $sql_avg = "SELECT 
                            SUM(o.is_correct AND a.option_id = o.option_id) AS correct_answers, 
                            COUNT(a.answer_id) AS total_answers
                        FROM answers a
                        JOIN options o ON a.option_id = o.option_id
                        WHERE a.question_id = '{$row['question_id']}'";
            $avg_result = mysqli_query($conn, $sql_avg);
            $average_data = mysqli_fetch_assoc($avg_result);
            $average_score =
                $average_data['total_answers'] > 0
                    ? ($average_data['correct_answers'] /
                            $average_data['total_answers']) *
                        100
                    : 0;
            ?>
            <button class="question-btn" 
                    onclick="window.location.href='lecturer_view_question_result.php?quiz_id=<?= base64_encode(
                        $quiz_id,
                    ) ?>&question_id=<?= $question_id ?>'">
                <?= htmlspecialchars($row['content']) ?>
                <hr>
                <div class="average-score">Average: <?= number_format(
                    $average_score,
                    2,
                ) ?>%</div>
            </button>
        <?php endwhile; ?>
    </div>

    <a href="lecturer_dashboard.php" class="back-btn">Back</a>
</body>
</html>
