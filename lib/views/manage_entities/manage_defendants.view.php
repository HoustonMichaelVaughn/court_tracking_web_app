<a href="<?= BASE_URL ?>/" class="btn btn-outline-secondary mb-3">← Back</a>

<div class="row my-5 justify-content-center">
  <div class="col-md-10">
    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
      <!-- Card 1: Manage Existing Defendants -->
      <div class="card mb-4 shadow-sm rounded-3">
        <div class="row g-0 align-items-center">
          <div class="col-md-8 p-4">
            <h4 class="card-title">Manage Existing Defendants</h4>
            <p class="card-text text-muted">
              View or update defendant records already in the system.
            </p>
          </div>
          <div class="col-md-4 text-end pe-4">
            <a href="<?= BASE_URL ?>/defendant/manage" class="btn btn-outline-primary btn-lg">Manage Defendants</a>
          </div>
        </div>
      </div>

      <!-- Card 2: Add New Defendant -->
      <div class="card shadow-sm rounded-3">
        <div class="row g-0 align-items-center">
          <div class="col-md-8 p-4">
            <h4 class="card-title">Add New Defendant</h4>
            <p class="card-text text-muted">
              Register a new defendant in the system.
            </p>
          </div>
          <div class="col-md-4 text-end pe-4">
            <a href="<?= BASE_URL ?>/defendant/add" class="btn btn-primary btn-lg">Add Defendant</a>
          </div>
        </div>
      </div>

    <?php else: ?>
      <!-- Card: View Existing Defendants -->
      <div class="card shadow-sm rounded-3">
        <div class="row g-0 align-items-center">
          <div class="col-md-8 p-4">
            <h4 class="card-title">View Existing Defendants</h4>
            <p class="card-text text-muted">
              View defendant records already in the system.
            </p>
          </div>
          <div class="col-md-4 text-end pe-4">
            <a href="<?= BASE_URL ?>/defendant/view" class="btn btn-outline-secondary btn-lg">View Defendants</a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
