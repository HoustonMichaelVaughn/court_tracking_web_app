<?php
// This view displays an error message for unexpected errors, optionally showing a custom message.
?>
<h1>Whoops! This is an unexpected error!</h1>
<?php if (!empty($message)): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php else: ?>
    <p>An unexpected error occurred.</p>
<?php endif; ?>
