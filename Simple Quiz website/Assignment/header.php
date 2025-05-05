<?php
if (!isset($_COOKIE['dark-mode'])) {
    setcookie('dark-mode', 'disabled', time() + 86400 * 30, '/');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APQuiz</title>
    <script>
    window.onload = function () {
        const darkModeCookie = getCookie('dark-mode');
        if (darkModeCookie === 'enabled') {
            document.body.classList.add('dark-mode');
            document.getElementById('logo-img').src = 'img/APSpacewhite.png';
            document.getElementById('icon-img').src = 'img/sun.png';
        }
    };

    function setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};path=/;expires=${d.toUTCString()}`;
    }

    function getCookie(name) {
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [key, value] = cookie.trim().split('=');
            if (key === name) return value;
        }
        return null;
    }

    function DarkMode() {
        const body = document.body;
        const logo = document.getElementById('logo-img');
        const icon = document.getElementById('icon-img');

        body.classList.toggle('dark-mode');

        const isDarkMode = body.classList.contains('dark-mode');

        logo.src = isDarkMode ? 'img/APSpacewhite.png' : 'img/APSpaceblack.png';
        icon.src = isDarkMode ? 'img/sun.png' : 'img/moon.png';

        setCookie('dark-mode', isDarkMode ? 'enabled' : 'disabled', 30);
    }
    </script>
    
    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #F8F8F8;
        color: black;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .header {
        height: 80px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-bottom: 1px solid #ddd;
        padding: 10px 20px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .left-section, .right-section {
        display: flex;
        align-items: center;
    }

    #logo-img {
        height: 50px;
    }

    #icon-img {
        height: 30px;
        width: 30px;
        cursor: pointer;
        margin-left: 15px;
        margin-top: 10px;
    }

    .right-section img{
        height: 45px;
        width: 45px;
        margin-right: 10px;
    }

    .user-info {
        display: flex;
        align-items: center;
        font-weight: bold;
        margin-right: 10px;
    }
    

    .logoutBtn {
        background-color: #2a83ff;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-left: 10px;
    }

    .logoutBtn:hover {
        background-color: #0056b3;
    }

    body.dark-mode {
        background-color: black;
        color: white;
    }

    body.dark-mode .header {
        background-color: #1e1e1e;
        border-bottom: 1px solid #555;
    }

    @media (max-width: 900px) {
        #logo-img {
            height: 35px;
        }

        #icon-img {
            height: 25px;
            width: 25px;
        }

        .right-section img{
            height: 30px;
            width: 30px;
        }

        .header {
            height: 50px;
        }
    }
</style>
</head>
<body>
    <div class="header">
        <div class="left-section">
            <img id="logo-img" src="img/APSpaceblack.png" alt="APSpace Logo" onclick="window.location.href = 'https:/\/apspace.apu.edu.my/tabs/dashboard'">
            <img id="icon-img" src="img/moon.png" alt="Dark Mode Toggle Icon" onclick="DarkMode();">
        </div>
        <div class="right-section">
            <img src="img/user.png" alt="Profile Icon">
            <div class="user-info">
                <span><?php echo $_SESSION['username']; ?></span>
            </div>
            <button class="logoutBtn" onclick="confirmLogOut();">Log Out</button>
        </div>
    </div>
    <script>
    function confirmLogOut() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href="logout.php"
        }
    }
    </script>
</body>
</html>