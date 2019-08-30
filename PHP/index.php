<?php
session_start();
include_once "db.php";

if (isset($_SESSION['userid'])) {
    if ($_SESSION['type'] == 'apprentice') {
        header('location: apprentice.php');
    }

    if ($_SESSION['type'] == 'instructor') {
        header('location: instructor.php');
    }

    if ($_SESSION['type'] == 'ihk') {
        header('location: ihk.php');
    }

}

if (isset($_GET['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $statement = $pdo->prepare("SELECT * FROM user WHERE username = :username");
    $result = $statement->execute(array('username' => $username));
    $user = $statement->fetch();

    //Überprüfung des Passworts
    if ($user !== false && sha1($password) == $user['password'] && $user['active'] == 1) {
    $_SESSION['userid'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['type'] = $user['type'];

        if ($_SESSION['type'] == 'apprentice') {
            header('location: apprentice.php');
        }

        if ($_SESSION['type'] == 'instructor') {
            header('location: instructor.php');
        }

        if ($_SESSION['type'] == 'ihk') {
            header('location: ihk.php');
        }

    } else{
        header('location: index.php?e=1');
    }

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>digitales Berichtsheft - Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>

<?php
if (isset($errorMessage)) {
}
?>

 <nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <!-- Brand -->
  <a class="navbar-brand" href="#">digitales Berichtsheft</a>

  <!-- Toggler/collapsibe Button -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- Navbar links -->
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
    </ul>
  </div>
</nav>

<div class="container">
<br><h1>Login</h1>
<div class="form-group">
    <form action="?login=1" method="post">
    Username:<br>
    <input class="form-control" type="text" size="40" maxlength="250" name="username"><br><br>

    Passwort:<br>
    <input class="form-control" type="password" size="40"  maxlength="250" name="password"><br>
    <?php if(isset($_GET['e']) && $_GET['e'] == 1) echo "<p style='color: red'>Logindaten falsch oder Benutzer inaktiv!!!</p>" ?>
    <input class="form-control btn btn-primary" type="submit" value="Abschicken">

    </form>
</div>

</div>


</body>
</html>