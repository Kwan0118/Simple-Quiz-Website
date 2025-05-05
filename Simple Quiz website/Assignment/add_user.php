<?php include 'session.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .form-container {
            margin: 30px auto;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 30px;
            margin: 50px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"], input[type="password"], #role {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 500px;
            box-sizing: border-box;
        }

        .btn {
            padding: 10px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            color: white;
            background-color: #007BFF; /* Blue color for the Create button */
        }

        .btn:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .error-message {
            color: red;
            font-size: 0.9rem;
            text-align: center;
        }

        body.dark-mode h1, body.dark-mode label {
            color: black;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

    <div class="form-container">
        <h1>Add User</h1>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter Username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required>

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="lecturer">Lecturer</option>
            </select>

            <button type="submit" class="btn">Create</button>
        </form>
    </div>
</body>
</html>

<?php
include 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Determine the prefix based on the role
    $prefix = $role === 'student' ? 'TP' : 'LC';

    // Fetch the highest ID for the given role
    $query = "SELECT id FROM users WHERE id LIKE '$prefix%' ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);
    $last_id = $result->fetch_assoc();

    // Generate the next ID
    if ($last_id) {
        // Extract numeric part of the ID and increment
        $next_number = intval(substr($last_id['id'], 2)) + 1;
    } else {
        // Start numbering from 1 if no existing IDs
        $next_number = 1;
    }

    // Format the new ID with leading zeros
    $new_id = $prefix . str_pad($next_number, 6, '0', STR_PAD_LEFT);

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $sql = "INSERT INTO users (id, name, password, role, status) VALUES ('$new_id', '$username', '$hashed_password', '$role', 1)";

    if ($conn->query($sql) === true) {
        echo "<script> window.location.href = 'admin_dashboard.php'; </script>";
    } else {
        $error_message = 'Error adding user: ' . $conn->error;
    }
}


?>
