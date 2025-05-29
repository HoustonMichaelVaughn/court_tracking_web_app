<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<?php
  $isLoginPage = str_contains($_SERVER['REQUEST_URI'], '/login');
  $loggedIn = isset($_SESSION['user_id']);
  if (!($isLoginPage && !$loggedIn)):
?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?= BASE_URL ?>/">Court Tracker</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>/cases">
              <?= (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'Manage Cases' : 'Cases' ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>/defendants">
              <?= (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'Manage Defendants' : 'Defendants' ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>/lawyers">
              <?= (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'Manage Lawyers' : 'Lawyers' ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>/defendant/search">Search</a>
          </li>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>/register">Register Staff</a>
            </li>
          <?php endif; ?>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>/accounts/manage">Manage Accounts</a>
            </li>
          <?php endif; ?>
        </ul>

        <ul class="navbar-nav ms-auto">
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>/logout">Logout</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>/login">Login</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
<?php endif; ?>