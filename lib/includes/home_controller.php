<?php
// controllers/home_controller.php

require_once __DIR__ . '/../models/CaseRecord.php';

// Fetch statistics from the database
$stats = CaseRecord::getStatistics();

