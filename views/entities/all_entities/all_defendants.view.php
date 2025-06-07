<h2>Manage Defendants</h2>
<a href="<?= BASE_URL ?>/defendants" class="btn btn-outline-secondary mb-3">← Back</a>

<?php if (!empty($_GET['success'])): ?>
  <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (empty($defendants)): ?>
    <p>No defendants found.</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Defendant ID</th>
                <th>Defendant</th>
                <th>Date of Birth</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($defendants as $defendant): ?>
                <tr>
                    <td><?= htmlspecialchars($defendant['defendant_ID']) ?></td>
                    <td><?= htmlspecialchars($defendant['defendant_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($defendant['defendant_DOB'] ?? 'N/A') ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/defendant/edit/<?= $defendant['defendant_ID'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="<?= BASE_URL ?>/defendant/delete/<?= $defendant['defendant_ID'] ?>" 
                            class="btn btn-sm btn-secondary"
                            onclick="return confirm('Are you sure you want to delete this defendant?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
