<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= BASE_URL ?>/">Court Tracker</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <?php
      $role = $_SESSION['role'] ?? 'viewer';
      ?>

      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>/cases"><?= $role === 'admin' ? 'Manage Cases' : 'Cases' ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>/defendants"><?= $role === 'admin' ? 'Manage Defendants' : 'Defendants' ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>/lawyers"><?= $role === 'admin' ? 'Manage Lawyers' : 'Lawyers' ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>/defendant/search">Search Defendants</a>
        </li>
        <li class="nav-item">
          <?php if (isset($_SESSION['user_id'])): ?>
            <a class="nav-link" href="<?= BASE_URL ?>/logout">Logout</a>
          <?php else: ?>
            <a class="nav-link" href="<?= BASE_URL ?>/login">Login</a>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </div>
</nav>
