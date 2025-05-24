<a href="<?= BASE_URL ?>/" class="btn btn-outline-secondary mb-3">← Back</a>

<div class="row my-5 justify-content-center">
  <div class="col-md-10">
    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
      <!-- Card 1: Manage Existing Lawyers -->
      <div class="card mb-4 shadow-sm rounded-3">
        <div class="row g-0 align-items-center">
          <div class="col-md-8 p-4">
            <h4 class="card-title">Manage Existing Lawyers</h4>
            <p class="card-text text-muted">
              View or update lawyer profiles already in the system.
            </p>
          </div>
          <div class="col-md-4 text-end pe-4">
            <a href="<?= BASE_URL ?>/lawyer/manage" class="btn btn-outline-primary btn-lg">Manage Lawyers</a>
          </div>
        </div>
      </div>

      <!-- Card 2: Add New Lawyer -->
      <div class="card shadow-sm rounded-3">
        <div class="row g-0 align-items-center">
          <div class="col-md-8 p-4">
            <h4 class="card-title">Add New Lawyer</h4>
            <p class="card-text text-muted">
              Register a new lawyer in the system.
            </p>
          </div>
          <div class="col-md-4 text-end pe-4">
            <a href="<?= BASE_URL ?>/lawyer/add" class="btn btn-primary btn-lg">Add Lawyer</a>
          </div>
        </div>
      </div>

    <?php else: ?>
      <!-- Card: View Existing Lawyers -->
      <div class="card shadow-sm rounded-3">
        <div class="row g-0 align-items-center">
          <div class="col-md-8 p-4">
            <h4 class="card-title">View Existing Lawyers</h4>
            <p class="card-text text-muted">
              Browse lawyer profiles already registered in the system.
            </p>
          </div>
          <div class="col-md-4 text-end pe-4">
            <a href="<?= BASE_URL ?>/lawyer/view" class="btn btn-outline-secondary btn-lg">View Lawyers</a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>