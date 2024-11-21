<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <section class="main">
        <form action="login.php" method="post" id="logowanie">
            <label for="email">Email: </label>
            <input type="email" name="email" id="email" required>
            <label for="haslo">Hasło: </label>
            <input type="password" name="haslo" id="haslo" minlength="8" required>
            <div class="info">Hasło musi mieć minimum 8 znaków i zawierać przynajmniej jedną cyfrę</div>
            <input type="submit" value="Zaloguj">
        </form>
    </section>
    <script src="zabezpieczenia.js"></script>
</body>
</html>