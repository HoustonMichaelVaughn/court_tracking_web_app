<h2>Manage Lawyers</h2>
<a href="<?= BASE_URL ?>/lawyers" class="btn btn-outline-secondary mb-3">← Back</a>

<?php if (!empty($_GET['success'])): ?>
  <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (empty($lawyers)): ?>
    <p>No lawyers found.</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Lawyer ID</th>
                <th>Lawyer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lawyers as $lawyer): ?>
                <tr>
                    <td><?= htmlspecialchars($lawyer['lawyer_ID']) ?></td>
                    <td><?= htmlspecialchars($lawyer['lawyer_name'] ?? 'N/A') ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/lawyer/edit/<?= $lawyer['lawyer_ID'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="<?= BASE_URL ?>/lawyer/delete/<?= $lawyer['lawyer_ID'] ?>"
                            class="btn btn-sm btn-secondary"
                            onclick="return confirm('Are you sure you want to delete this lawyer?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
