<?php include 'session.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
        }

        .container {
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }

        h1 {
            background-color: #4285F4;
            color: #ffffff;
            text-align: center;
            padding: 15px;
            margin: 0;
            border-radius: 8px 8px 0 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        table thead {
            background-color: #4285F4;
            color: #ffffff;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr td {
            border-bottom: 1px solid #e0e0e0;
        }

        .correct {
            color: #28a745;
            font-weight: bold;
        }

        .incorrect {
            color: #dc3545;
            font-weight: bold;
        }

        .result-icon {
            margin-right: 5px;
        }

        .dropdown {
            margin: 5px 0;
        }

        .dropdown button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .dropdown button:hover {
            background-color: #45a049;
        }

        .answer-row {
            display: none;
            background-color: #f9f9f9;
        }

        .answer-container {
            padding: 15px;
        }

        .answer-container span {
            display: block;
            padding: 5px;
            border-radius: 4px;
        }

        .user-answer {
            background-color: #ffeb3b;
        }

        .correct-answer {
            background-color: #4caf50;
        }

        .percentage {
            float: right;
            background-color: #4285F4;
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
            margin-right: 10px;
        }

        body.dark-mode p, div {
            color: black;
        }
    </style>
</head>
<body>
<?php
include 'header.php';

include 'db_conn.php';
$quizid = $_GET['quiz_id']; // Query to get correct answers from the database
$correctAnswersQuery =
    'SELECT question_id, option_id, content AS correct_answer_content FROM options WHERE is_correct = 1';
$correctAnswersResult = $conn->query($correctAnswersQuery);
$correctAnswers = [];
if ($correctAnswersResult->num_rows > 0) {
    while ($row = $correctAnswersResult->fetch_assoc()) {
        $correctAnswers[$row['question_id']] = [
            'option_id' => $row['option_id'],
            'content' => $row['correct_answer_content'],
        ];
    }
} // get user answers and questions from database
$userAnswersQuery = "
    SELECT ua.student_id, ua.question_id, q.quiz_id, ua.option_id AS user_answer, q.content AS question_text, o.content AS user_answer_content
    FROM answers ua
    JOIN questions q ON ua.question_id = q.question_id
    JOIN options o ON ua.option_id = o.option_id
    WHERE student_id = '{$_SESSION['id']}' AND q.quiz_id ='$quizid'
";
$userAnswersResult = $conn->query($userAnswersQuery);
$comparisonResults = [];
$counter = 1;
$correctCount = 0;
$totalQuestions = 0;
if ($userAnswersResult->num_rows > 0) {
    while ($row = $userAnswersResult->fetch_assoc()) {
        $totalQuestions++;
        $questionId = $row['question_id'];
        $questionText = $row['question_text'];
        $userAnswerContent = $row['user_answer_content'];
        $correctAnswerContent = $correctAnswers[$questionId]['content']; // Compare user's answer with the correct answer
        $isCorrect =
            $userAnswerContent == $correctAnswerContent
                ? 'Correct'
                : 'Incorrect';
        if ($isCorrect == 'Correct') {
            $correctCount++;
        }
        $comparisonResults[] = [
            'number' => $counter++,
            'question_text' => $questionText,
            'user_answer' => $userAnswerContent,
            'correct_answer' => $correctAnswerContent,
            'is_correct' => $isCorrect,
        ];
    }
}
$correctAnswersText = "$correctCount / $totalQuestions";
$conn->close();
?>
    <div class="container">
        <!-- Back Button -->
        <a href="student_dashboard.php" class="back-button" style="display: inline-block; margin-bottom: 20px; text-decoration: none; color: #fff; background-color: #007bff; padding: 10px 15px; border-radius: 5px;">&larr; Back to Dashboard</a>
        
        <h1 style="text-align: left;">Results</h1>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Question</th>
                    <th>Answers</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comparisonResults as $result): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(
                            $result['number'],
                        ); ?></td>
                        <td><?php echo htmlspecialchars(
                            $result['question_text'],
                        ); ?></td>
                        <td>
                            <div class="dropdown">
                                <button onclick="toggleAnswerRow(<?php echo $result[
                                    'number'
                                ]; ?>)">View Answers</button>
                            </div>
                        </td>
                        <td class="<?php echo strtolower(
                            $result['is_correct'],
                        ); ?>">
                            <?php if ($result['is_correct'] == 'Correct'): ?>
                                <i class="fas fa-check-circle result-icon"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle result-icon"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars(
                                $result['is_correct'],
                            ); ?>
                        </td>
                    </tr>
                    <tr id="answer-row-<?php echo $result[
                        'number'
                    ]; ?>" class="answer-row">
                        <td colspan="4">
                            <div class="answer-container">
                                <span class="user-answer">User Answer: <?php echo htmlspecialchars(
                                    $result['user_answer'],
                                ); ?></span>
                                <span class="correct-answer">Correct Answer: <?php echo htmlspecialchars(
                                    $result['correct_answer'],
                                ); ?></span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="percentage">Correct Answers: <?php echo $correctAnswersText; ?></div>
    </div>

<script>
        function toggleAnswerRow(id) {
            var answerRow = document.getElementById('answer-row-' + id);
            if (answerRow.style.display === 'none' || answerRow.style.display === '') {
                answerRow.style.display = 'table-row';
            } else {
                answerRow.style.display = 'none';
            }
        }
    </script>
</body>
</html>
