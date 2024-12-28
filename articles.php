<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статьи</title>
    <style>
        /* Общие стили */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            background-color: #fff;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        header img {
            max-width: 100px;
            margin-right: 20px;
            cursor: pointer;
        }

        header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }

        .articles-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .article {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .article h2 {
            color: #333;
            margin-top: 0;
        }

        .article p {
            color: #666;
            margin-bottom: 10px;
            overflow: hidden; /* Скрытие части текста, выходящей за границы */
            text-overflow: ellipsis; /* Замена скрытой части текста многоточием */
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Ограничение количества отображаемых строк */
            -webkit-box-orient: vertical;
        }

        .article a {
            color: #4CAF50;
            text-decoration: none;
        }

        .delete-btn {
            background-color: #ff0000;
            color: #fff;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 10px; /* Добавленный стиль для создания расстояния */
        }

        .delete-btn:hover {
            background-color: #cc0000;
        }

        .write-article-btn {
            display: block;
            margin-top: 20px;
            text-align: center;
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none; /* Добавленный стиль для убирания подчеркивания */
            transition: background-color 0.3s ease;
        }

        .write-article-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header onclick="window.location.href='main.html'">
        <img src="log.png" alt="Логотип">
        <h1>Kaleidoscope Books</h1>
    </header>

    <div class="articles-container">
        <h2>Статьи</h2>

        <?php
        session_start();

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

        // Получение роли пользователя
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
        if ($user_id) {
            $role_query = "SELECT role FROM users WHERE id = '$user_id'";
            $role_result = $conn->query($role_query);

            if ($role_result->num_rows > 0) {
                $row = $role_result->fetch_assoc();
                $user_role = $row['role'];

                // Выполнение запроса для получения списка статей
                $sql = "SELECT * FROM articles";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Вывод каждой статьи
                    while ($row = $result->fetch_assoc()) {
                        $article_id = $row['id'];
                        $article_title = $row['title'];
                        $article_content = $row['content'];

                        // Создание ссылки на страницу с полным содержанием статьи
                        $article_page = "Articles/article_$article_id.php";
                        file_put_contents($article_page, "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>$article_title</title></head><body><h1>$article_title</h1><p>$article_content</p></body></html>");

                        // Отображение названия статьи и маленькой части текста
                        echo "<div class='article'>";
                        echo "<h2>$article_title</h2>";
                        echo "<p>$article_content</p>";
                        echo "<a href='$article_page'>Читать далее</a>";

                        // Проверка роли пользователя для отображения кнопки удаления
                        if ($user_role === 'администратор') {
                            echo "<form action='delete_article.php' method='post'>";
                            echo "<input type='hidden' name='article_id' value='$article_id'>";
                            echo "<button type='submit' class='delete-btn'>Удалить статью</button>";
                            echo "</form>";
                        }

                        echo "</div>";
                    }
                } else {
                    echo "Статей не найдено.";
                }
            } else {
                echo "Роль пользователя не определена.";
            }
        } else {
            echo "Вы не авторизованы.";
        }

        $conn->close();
        ?>

        <!-- Кнопка "Написать статью" -->
        <?php if (isset($user_role) && $user_role === 'администратор') : ?>
            <a href="write_article.html" class="write-article-btn">Написать статью</a>
        <?php endif; ?>
    </div>
</body>
</html>
