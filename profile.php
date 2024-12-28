<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личная информация</title>
    <style>
        /* Общие стили */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            background-image: url('background-pattern.png'); /* Фоновый узор */
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        header {
            background-color: #fff;
            padding: 20px;
            border-bottom: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header h1 {
            color: #333;
            margin: 0;
        }

        header img {
            max-width: 100px;
        }

        header img, header h1 {
            cursor: pointer;
        }

        /* Стили для формы и информации о пользователе */
        .user-info {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .user-info p {
            margin: 0;
            padding: 10px 0;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .button {
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 10px;
        }

        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header onclick="window.location.href='main.html'">
        <h1>Kaleidoscope Books</h1>
        <img src="./log.png" alt="Логотип">
    </header>

    <div class="container">
        <h2>Личная информация</h2>

        <?php
        session_start();

        // Проверяем, вошел ли пользователь в систему
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            // Подключение к базе данных
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "uni-lit_project";

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Проверка соединения
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Получение данных о пользователе
            $user_query = "SELECT name, lastname, birthday
                           FROM users
                           WHERE id = '$user_id'";

            $result = $conn->query($user_query);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<div class='user-info'>";
                echo "<p>Фамилия: " . $row['lastname'] . "</p>";
                echo "<p>Имя: " . $row['name'] . "</p>";
                echo "<p>Дата рождения: " . $row['birthday'] . "</p>";
                echo "</div>";
            } else {
                echo "Данные о пользователе не найдены";
            }

            $conn->close();
        } else {
            echo "Пользователь не вошел в систему.";
        }
        ?>

        <div class="button-container">
            <?php
            if (isset($_SESSION['user_id'])) {
                // Пользователь авторизован
                echo '<a href="logout.php" class="button">Выйти из аккаунта</a>';
            } else {
                // Пользователь не авторизован
                echo '<a href="login.html" class="button" style="background-color: #4c8baf;">Вход</a>';
                echo '<a href="registration.html" class="button" style="background-color: #4c8baf;">Регистрация</a>';
            }
            ?>
        </div>
    </div>
</body>
</html>
