<h1>Multi-Table Search</h1>

<form method="get" action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/defendant/search">
    <label for="field">Search by:</label>
    <select name="field" id="field">
        <option value="name" <?= ($query['field'] ?? '') === 'name' ? 'selected' : '' ?>>Defendant Name</option>
        <option value="email" <?= ($query['field'] ?? '') === 'email' ? 'selected' : '' ?>>Defendant Email</option>
        <option value="charge" <?= ($query['field'] ?? '') === 'charge' ? 'selected' : '' ?>>Charge Description</option>
        <option value="status" <?= ($query['field'] ?? '') === 'status' ? 'selected' : '' ?>>Charge Status</option>
        <option value="lawyer" <?= ($query['field'] ?? '') === 'lawyer' ? 'selected' : '' ?>>Lawyer Name</option>
        <option value="event" <?= ($query['field'] ?? '') === 'event' ? 'selected' : '' ?>>Event Description</option>
    </select>

    <input type="text" name="q" value="<?= htmlspecialchars($query['q'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Search term..." />
    <input type="submit" value="Search" />
</form>

<?php if (!empty($results)): ?>
    <ul>
        <?php foreach ($results as $row): ?>
            <li>
                <strong><?= htmlspecialchars($row['Defendant_Name'], ENT_QUOTES, 'UTF-8') ?></strong>
                (<?= htmlspecialchars($row['Defendant_Email'], ENT_QUOTES, 'UTF-8') ?>)
                <ul>
                    <li>Case ID: <?= htmlspecialchars(strval($row['case_ID'] ?? '')) ?></li>
                    <li>Charge: <?= htmlspecialchars($row['Charge_Description'], ENT_QUOTES, 'UTF-8') ?>
                        (<?= htmlspecialchars($row['Charge_Status'], ENT_QUOTES, 'UTF-8') ?>)</li>
                    <li>Lawyer: <?= htmlspecialchars($row['Lawyer_Name'], ENT_QUOTES, 'UTF-8') ?></li>
                    <li>Event: <?= htmlspecialchars($row['Event_Description'], ENT_QUOTES, 'UTF-8') ?> at
                        <?= htmlspecialchars($row['Event_Location'], ENT_QUOTES, 'UTF-8') ?> on
                        <?= htmlspecialchars($row['Event_Date'], ENT_QUOTES, 'UTF-8') ?></li>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
<?php elseif (!empty($query['q'])): ?>
    <p>No results found for "<strong><?= htmlspecialchars($query['q']) ?></strong>"
        in <?= ucfirst($query['field'] ?? 'that field') ?>.</p>
<?php endif; ?>
<?php if (empty($query['q'])): ?>
    <p>Enter a search term to begin.</p>
<?php endif; ?>