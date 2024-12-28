<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Соединение с базой данных (замените параметры на свои)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "uni-lit_project";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Обработка данных формы входа
    if (isset($_POST["login_submit"])) {
        $login_email = $_POST["login_email"];
        $login_password = $_POST["login_password"];

        // Поиск пользователя в базе данных по электронной почте
        $sql = "SELECT * FROM users WHERE email = '$login_email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_password = $row["password"];

            // Проверка соответствия пароля
            if (password_verify($login_password, $stored_password)) {
                // Успешный вход
                session_start();
                $_SESSION['user_id'] = $row['id'];
                header("Location: main.html");
                exit;
            } else {
                // Неудачный вход
                echo "Неверный пароль. Пожалуйста, попробуйте еще раз.";
                echo '<br><a href="login_registration.html">Повторить вход</a>';
                // Добавим JavaScript код для перенаправления через несколько секунд
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.html';
                    }, 2000); // Переход через 2 секунды (2000 миллисекунд)
                </script>";
            }
        } else {
            echo "Пользователь с указанным email не найден.";
            // Добавим JavaScript код для перенаправления через несколько секунд
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.html';
                }, 4000); // Переход через 4 секунды (4000 миллисекунд)
            </script>";
        }
    }

    // Обработка данных формы регистрации
    if (isset($_POST["register_submit"])) {
        $register_name = $_POST["register_name"];
        $register_lastname = $_POST["register_lastname"];
        $register_birthday = $_POST["register_birthday"];
        $register_email = $_POST["register_email"];
        $register_password = $_POST["register_password"];

        // Проверка наличия пользователя с таким логином (email) в базе данных
        $check_sql = "SELECT * FROM users WHERE email = '$register_email'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            echo "Пользователь с таким email уже существует. Пожалуйста, выберите другой email.";
            // Добавим JavaScript код для перенаправления через несколько секунд
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'registration.html';
                }, 2000); // Переход через 4 секунды (2000 миллисекунд)
            </script>";
            $conn->close();
            exit;
        }

        // Хеширование пароля
        $hashed_password = password_hash($register_password, PASSWORD_DEFAULT);

        // Вставка нового пользователя в базу данных
        $insert_sql = "INSERT INTO users (name, lastname, birthday, email, password) VALUES ('$register_name', '$$register_lastname', '$register_birthday', '$register_email', '$hashed_password')";

        if ($conn->query($insert_sql) === TRUE) {
            // Сохраняем ID нового пользователя в сессии
            session_start();
            $_SESSION['user_id'] = $conn->insert_id;

            echo "Регистрация прошла успешно. Пользователь зарегистрирован.";
            // Добавим JavaScript код для перенаправления через несколько секунд
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'main.html';
                }, 2000); // Переход через 2 секунды (2000 миллисекунд)
            </script>";
        } else {
            echo "Ошибка при регистрации: " . $conn->error;
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.html';
                }, 4000); // Переход через 4 секунды (4000 миллисекунд)
            </script>";
        }
    }

    $conn->close();
}
?>
