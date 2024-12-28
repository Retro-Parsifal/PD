<?php
session_start(); // Начинаем сессию для работы с пользователем
$isAdmin = false; // По умолчанию пользователь не админ
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Соединение с базой данных (замените параметры на свои)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uni-lit_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение роли пользователя из таблицы users
if ($user_id) {
    $sql_user = "SELECT role FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    if ($stmt_user) {
         $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows > 0) {
            $row_user = $result_user->fetch_assoc();
           if ($row_user['role'] == 'администратор') {
               $isAdmin = true;
           }
        }
        $stmt_user->close();
    }
}


// Обработка удаления книги
if (isset($_GET['delete_id']) && $isAdmin) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
           echo "<script>alert('Книга удалена!'); window.location.href='novelties.php';</script>";
        } else {
          echo "<script>alert('Ошибка при удалении книги!');</script>";
        }
        $stmt->close();
    }  else {
        echo "<script>alert('Ошибка при подготовке запроса удаления!');</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новинки наших писателей</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            text-align: center;
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

        h2 {
            margin-top: 20px;
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }
        .delete-button {
             background-color: #f44336;
              color: white;
             padding: 5px 10px;
            border: none;
              cursor: pointer;
             border-radius: 4px;
        }
        .delete-button:hover{
           background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <header onclick="window.location.href='main.html'">
        <img src="./log.png" alt="Логотип">
        <h1>Kaleidoscope Books</h1>
    </header>
    <h2>Новинки наших писателей</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Автор</th>
                <th>Название книги</th>
                <th>Дата добавления</th>
                <th>Действия</th>
              <?php if($isAdmin): ?>
                 <th>Удалить</th>
              <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            

            // Выполнение запроса для извлечения данных из базы данных
            $sql = "SELECT * FROM books";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["author"] . "</td>";
                    echo "<td>" . $row["title"] . "</td>";
                    echo "<td>" . $row["date_added"] . "</td>";
                    echo "<td><a href='view_book.php?id=" . $row["id"] . "'>Просмотреть</a></td>";
                  if ($isAdmin) {
                       echo "<td><button class='delete-button' onclick=\"window.location.href='novelties.php?delete_id=" . $row["id"] . "'\">Удалить</button></td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Нет доступных книг</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>