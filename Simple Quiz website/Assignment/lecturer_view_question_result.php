<?php include 'session.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .title-box {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }

        .result-container {
            margin: 20px auto;
            padding: 10px;
            border: 1px solid black;
            border-radius: 5px;
            width: 80%;
        }

        .result-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }

        .result-row.correct {
            background-color: green;
        }

        .result-row.incorrect {
            background-color: red;
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
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: grey;
        }
    </style>
</head>
<body>
    <?php
    include 'header.php';
    include 'db_conn.php';

    $quiz_id = isset($_GET['quiz_id']) ? base64_decode($_GET['quiz_id']) : null;
    $question_id = isset($_GET['question_id'])
        ? base64_decode($_GET['question_id'])
        : null;

    if (!$quiz_id || !$question_id) {
        echo "<script>alert('Invalid quiz or question selection.');</script>";
        exit();
    }

    // Fetch quiz title
    $sql_title = "SELECT title FROM quizzes WHERE quiz_id = '$quiz_id'";
    $title_result = mysqli_query($conn, $sql_title);
    $quiz = mysqli_fetch_assoc($title_result);

    // Fetch question content
    $sql_question = "SELECT content FROM questions WHERE question_id = '$question_id' AND quiz_id = '$quiz_id'";
    $question_result = mysqli_query($conn, $sql_question);
    $question = mysqli_fetch_assoc($question_result);

    // Fetch average score
    $sql_avg = "SELECT 
                    SUM(o.is_correct AND a.option_id = o.option_id) AS correct_answers, 
                    COUNT(a.answer_id) AS total_answers
                FROM answers a
                JOIN options o ON a.option_id = o.option_id
                WHERE a.question_id = '$question_id'";
    $avg_result = mysqli_query($conn, $sql_avg);
    $average_data = mysqli_fetch_assoc($avg_result);
    $average_score =
        $average_data['total_answers'] > 0
            ? ($average_data['correct_answers'] /
                    $average_data['total_answers']) *
                100
            : 0;

    // Fetch student answers
    $sql_answers = "SELECT users.name, options.content, options.is_correct 
                    FROM answers 
                    JOIN users ON answers.student_id = users.id 
                    JOIN options ON answers.option_id = options.option_id 
                    WHERE answers.question_id = '$question_id'";
    $results = mysqli_query($conn, $sql_answers);
    ?>
    <div class="title-box"><?= htmlspecialchars($quiz['title']) ?></div>

    <div class="result-container">
        <h3>Question: <?= htmlspecialchars($question['content']) ?></h3>
        <p>Average Score: <?= number_format($average_score, 2) ?>%</p>

        <?php while ($row = mysqli_fetch_assoc($results)): ?>
            <div class="result-row <?= $row['is_correct']
                ? 'correct'
                : 'incorrect' ?>">
                <span><?= htmlspecialchars(
                    $row['name'],
                ) ?> - <?= htmlspecialchars($row['content']) ?></span>
                <span><?= $row['is_correct'] ? '✔' : '✖' ?></span>
            </div>
        <?php endwhile; ?>
    </div>

    <a href="lecturer_view_result.php?quiz_id=<?= htmlspecialchars(
        base64_encode($quiz_id),
    ) ?>" class="back-btn">Back</a>
</body>
</html>
