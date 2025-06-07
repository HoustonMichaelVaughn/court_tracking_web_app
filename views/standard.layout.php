<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Court Outcome Tracking</title>
  </head>
  <body>
    <?php require_once __DIR__ . '/partials/_navbar.php'; ?>

    <div class="container mt-4">
        <?php
        $viewPath = __DIR__ . "/entities/forms/" . ltrim($content, '/');
        require_once $viewPath . ".view.php";
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

<?php
// This layout view acts as a template, including a navbar and dynamically loading content views.
?>