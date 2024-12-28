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

        // Проверка роли пользователя перед удалением статьи
        if ($user_role === 'администратор') {
            // Получение идентификатора статьи для удаления
            $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';

            if ($article_id) {
                // Удаление статьи из базы данных
                $delete_query = "DELETE FROM articles WHERE id = '$article_id'";
                if ($conn->query($delete_query) === TRUE) {
                    // Удаление файла статьи из папки Articles
                    $article_page = "Articles/article_$article_id.php";
                    if (file_exists($article_page)) {
                        unlink($article_page);
                    }

                    // После успешного удаления статьи перенаправляем пользователя обратно на страницу со статьями
                    header("Location: articles.php");
                    exit;
                } else {
                    echo "Ошибка при удалении статьи: " . $conn->error;
                }
            } else {
                echo "Не удалось получить идентификатор статьи для удаления.";
            }
        } else {
            echo "У вас нет прав на удаление статьи.";
        }
    } else {
        echo "Ошибка при получении роли пользователя.";
    }
} else {
    echo "Вы не авторизованы.";
}

$conn->close();
?>
