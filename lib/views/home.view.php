<!-- lib/views/home.view.php -->
<div class="p-5 mb-4 bg-light rounded-3">
  <div class="container-fluid py-5">
    <h1 class="display-5 fw-bold">Welcome to the Court Outcome Tracking System</h1>
    <p class="col-md-8 fs-4">Log in or select a module from the menu to get started.</p>
  </div>
</div>

<!-- Stats Section -->
<div class="container mb-5">
  <h2 class="mb-4">Case Statistics</h2>
  <div class="row g-4">
    <div class="col-md-3">
      <div class="card text-white bg-primary h-100">
        <div class="card-body text-center">
          <h5 class="card-title">Total Cases</h5>
          <p class="card-text fs-3"><?= htmlspecialchars($stats['total'] ?? 0) ?></p>
          <a href="<?= BASE_URL ?>/case/manage" class="btn btn-light btn-sm mt-2">View All Cases</a>
        </div>
      </div>
    </div>
        <div class="col-md-3">
      <div class="card text-white bg-success h-100">
        <div class="card-body text-center d-flex flex-column justify-content-center">
          <h5 class="card-title">Active Cases</h5>
          <p class="card-text fs-3"><?= htmlspecialchars($stats['active'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning h-100">
        <div class="card-body text-center d-flex flex-column justify-content-center">
          <h5 class="card-title">Pending Cases</h5>
          <p class="card-text fs-3"><?= htmlspecialchars($stats['pending'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-secondary h-100">
        <div class="card-body text-center d-flex flex-column justify-content-center">
          <h5 class="card-title">Closed Cases</h5>
          <p class="card-text fs-3"><?= htmlspecialchars($stats['closed'] ?? 0) ?></p>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Quick Actions -->
<div class="container mb-5">
  <h2 class="mb-4">Quick Actions</h2>
  <div class="row g-3">
    <div class="col-md-4">
      <a href="<?= BASE_URL ?>/case/defendant" class="btn btn-outline-primary w-100">➕ Add Case</a>
    </div>
    <div class="col-md-4">
      <a href="<?= BASE_URL ?>/defendant/add" class="btn btn-outline-success w-100">➕ Add Defendant</a>
    </div>
    <div class="col-md-4">
      <a href="<?= BASE_URL ?>/lawyer/add" class="btn btn-outline-warning w-100">➕ Add Lawyer</a>
    </div>
  </div>
</div>
