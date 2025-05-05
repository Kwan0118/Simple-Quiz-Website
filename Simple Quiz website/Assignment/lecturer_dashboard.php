<?php include 'session.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>        
        #top-container {
            display: flex;
            margin: 20px auto;
            width: 100%;
            max-width: 1200px;
            justify-content: center;
            align-items: center;
        }

        #top-container button {
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

        #top-container button:hover {
            background-color: #0056b3;
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

        #container img {
            position: absolute;
            bottom: 20px;
            right: 10px;
            width: 50px;
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
            #top-container {
                gap: 10px;
            }

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
    echo "<button onclick=\"window.location.href='create_quiz.php'\" style=\"cursor: pointer;\">Create Quiz</button>";
    include 'search_bar.php';
    echo '</div>';

    include 'db_conn.php';

    // Search algorithm
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['searchField'])) {
        $sql =
            "SELECT * FROM quizzes WHERE created_by = \"{$_SESSION['id']}\" AND title LIKE '%" .
            $_POST['searchField'] .
            "%' ORDER BY quiz_id";
    } else {
        $sql = "SELECT * FROM quizzes WHERE created_by = \"{$_SESSION['id']}\" ORDER BY quiz_id";
    }
    $result = mysqli_query($conn, $sql);

    // Display quizzes
    if (mysqli_num_rows($result) > 0) {
        echo "<div id='container'>";
        while ($row = mysqli_fetch_assoc($result)) {
            $quiz_id = base64_encode($row['quiz_id']);
            $title = $row['title'];
            echo <<<HTML
            <div onclick="window.location.href='lecturer_view_result.php?quiz_id={$quiz_id}'">
                <h2>$title</h2>
                <img src="img/deleteQuiz.png" alt="Delete button to delete {$title} quiz" onclick="event.stopPropagation(); confirmDelete('{$quiz_id}', '{$title}');">
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
        function confirmDelete(quizId, title) {
            if (confirm("Are you sure you want to delete " + title + " quiz?")) {
                window.location.href = "delete_quiz.php?quiz_id=" + quizId;
            }
        }
    </script>
</body>
</html>