<nav class="navbar navbar-expand-lg navbar-dark primary-color shadow">
    <a class="navbar-brand" href="/">
      NextCaligo
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#notLoggedNavbar"
      aria-controls="notLoggedNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="notLoggedNavbar">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" id="langChoose" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false"><i class="fas fa-language"></i> Languages</a>
          <div class="dropdown-menu dropdown-primary" aria-labelledby="langChoose">
            <a class="dropdown-item" href="?lang=hu"><span class="fi fi-hu"></span> Magyar</a>
            <a class="dropdown-item" href="?lang=en"><span class="fi fi-us"></span> English</a>
          </div>
        </li>
        <?php if(isset($_SESSION['username']))
        { ?> 
        <li class="nav-item">
          <a class="nav-link" href="/account/my">
            <i class="fas fa-user"></i>
            <?php echo $lang['Welcome']; echo htmlspecialchars($_SESSION['username']); ?>! 
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/logout">
            <i class="fas fa-sign-out-alt"></i>
            <?php echo $lang['Logout']; ?> 
          </a>
        </li>
        <?php } ?>
      </ul>  
  </div>  
</nav>
