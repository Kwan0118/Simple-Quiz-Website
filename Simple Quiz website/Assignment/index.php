<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APQuiz</title>
    <style>
        .login-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 500px;
            margin: 100px auto;
            padding: 20px;
            border: 2px solid black;
        }

        img {
            width: 250px;
        }

        h2 {
            font-size: 2rem;
        }

        label {
            font-weight: bold;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            margin: 10px auto 30px auto;
            padding: 10px;
            font-size: 14px;
        }

        input[type="submit"] {
            border-radius: 5px;
            background-color: black;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: grey;
        }
    </style>
</head>
<body>
<div class="login-container">
    <img src="img/apu.png" alt="APU Logo">
    <h2>Welcome to APQuiz</h2>

    <form method="POST" action="<?php /*prettier-ignore*/ echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for='id'>User ID</label>
        <input type="text" id="id" name="id" placeholder="Enter User ID" required>

        <label for='password'>Password</label>
        <input type="password" id="password" name="password" placeholder="Enter Password" required>

        <input type="submit" value="Login">
    </form>
</div>

<?php
include 'db_conn.php';

if (!isset($_SESSION['saved_id'])) {
    $_SESSION['saved_id'] = null;
    $_SESSION['login_attempts'] = 3;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        echo "<script>alert('The user id is incorrect');</script>";
    } else {
        if ($user['role'] == 'admin') {
            if (password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['name'];

                header('Location: admin_dashboard.php');
                exit();
            } else {
                echo "<script>alert('The user password is incorrect');</script>";
            }
        } else {
            if ($user['status'] == 1) {
                if ($user['id'] != $_SESSION['saved_id']) {
                    $_SESSION['login_attempts'] = 3;
                }

                if (password_verify($password, $user['password'])) {
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['username'] = $user['name'];

                    if ($user['role'] == 'student') {
                        header('Location: student_dashboard.php');
                        exit();
                    } elseif ($user['role'] == 'lecturer') {
                        header('Location: lecturer_dashboard.php');
                        exit();
                    }
                } else {
                    $_SESSION['login_attempts']--;
                    if ($_SESSION['login_attempts'] == 0) {
                        $sql = "UPDATE users SET status = 0 WHERE id = '$id'";
                        mysqli_query($conn, $sql);
                        echo "<script>alert('Your have reached the maximum number of login attempts, please contact admin to unlock your account');</script>";
                    } else {
                        $_SESSION['saved_id'] = $user['id'];
                        echo "<script>alert('The user password is incorrect. You have {$_SESSION['login_attempts']} attempts left');</script>";
                    }
                }
            } else {
                echo "<script>alert('Your account is locked, please contact admin to unlock your account');</script>";
            }
        }
    }
}
?>
</body>
</html>
