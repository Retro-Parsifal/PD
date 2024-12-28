<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр книги</title>
</head>
<body>

<?php
// Соединение с базой данных (замените параметры на свои)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uni-lit_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Получение ID книги из параметра запроса
    $book_id = $_GET["id"];

    // Выполнение запроса для извлечения данных о книге
    $sql = "SELECT * FROM books WHERE id = $book_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h2>{$row['title']} (автор: {$row['author']})</h2>";
        echo "<p>Дата добавления: {$row['date_added']}</p>";

        // Проверяем наличие пути к файлу docx
        if (!empty($row['docx_path']) && file_exists($row['docx_path'])) {
            echo "<h3>Содержимое книги:</h3>";

            // Читаем содержимое файла docx и выводим его на странице
            $docx_content = file_get_contents($row['docx_path']);
            $original_encoding = 'windows-1251'; // Замените на реальную кодировку файла
           
            $decoded_content = mb_convert_encoding($docx_content, 'UTF-8', $original_encoding);
           
            echo "<div>{$decoded_content}</div>";
        } elseif (!empty($row['content'])) {
            // Если файла docx нет, выводим текст из столбца content
             echo "<h3>Содержимое книги:</h3>";
              $content_from_db =  $row['content'];
             $decoded_content = mb_convert_encoding($content_from_db, 'UTF-8');
             echo "<div>{$decoded_content}</div>";

        } else {
            echo "<p>К сожалению, нет содержимого для просмотра.</p>";
        }
    } else {
        echo "<p>Книга не найдена.</p>";
    }
}

$conn->close();
?>

</body>
</html>