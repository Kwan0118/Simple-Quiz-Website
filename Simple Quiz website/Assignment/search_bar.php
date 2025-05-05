<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        form {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            width: 100%;
            max-width: 800px;
            justify-content: center;
            align-items: center;
        }

        form div {
            display: flex;
            position: relative;
            flex: 1;
            max-width: 600px;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 25px;
            align-items: center;
        }

        #searchField {
            background-color: transparent;
            flex: 1;
            border: none;
            outline: none;
            padding: 10px;
            padding-right: 30px;
        }

        #clearSearchButton {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            color: #808080;
            cursor: pointer;
        }

        #clearSearchButton:hover {
            color: #a9a9a9;
        }

        input[type="submit"] {
            padding: 10px 20px;
            font-size: 1rem;
            border: none;
            border-radius: 20px;
            background-color: #2a83ff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        body.dark-mode #searchField {
            color: white;
        }

        @media (max-width: 900px) {
            form {
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <?php $placeholder = $placeholder ?? 'Find a Quiz'; ?>
    <form action="<?php /* prettier-ignore */ echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div id="search-bar">
            <input type="text" id="searchField" name="searchField" placeholder="<?php /* prettier-ignore */ echo htmlspecialchars($placeholder); ?>" autocomplete="off">
            <span id="clearSearchButton" onclick="clearSearch();">×</span>
        </div>
        <input type="submit" value="Search">
    </form>
    
    <script>
        function clearSearch() {
            document.getElementById("searchField").value = "";
        }
    </script>
</body>
</html>