<nav class="navbar navbar-expand-md bg-dark navbar-dark">
    <!-- Brand -->
    <a class="navbar-brand" href="#">digitales Berichtsheft</a>

    <!-- Toggler/collapsibe Button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link">Rolle: <?php if ($_SESSION['type'] == 'apprentice') {
    echo "Auszubildender";
}
?>
            <?php if ($_SESSION['type'] == 'instructor') {
    echo "Ausbilder";
}
?>
            <?php if ($_SESSION['type'] == 'ihk') {
    echo "IHK";
}
?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><?php echo $_SESSION['username'] ?> ausloggen</a>
        </li>
      </ul>
    </div>
  </nav>