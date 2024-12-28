<?php
session_start();

// Проверяем метод запроса
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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

    // Получение данных из формы
    $article_author = $_SESSION['user_id']; // Используем ID пользователя как автора статьи
    $article_title = $_POST["article_title"];
    $article_content = $_POST["article_content"];

    // Получение текущей даты и времени
    $current_datetime = date("Y-m-d H:i:s");

    // Выполнение запроса на добавление статьи
    $sql = "INSERT INTO articles (author, title, content, date_added) VALUES ('$article_author', '$article_title', '$article_content', '$current_datetime')";

    if ($conn->query($sql) === TRUE) {
        echo "Статья успешно добавлена";
        echo '<br><a href="articles.php">Перейти ко всем статьям</a>'; // Добавляем ссылку на страницу со всеми статьями
    } else {
        echo "Ошибка при добавлении статьи: " . $conn->error;
    }

    $conn->close();
}
?>
