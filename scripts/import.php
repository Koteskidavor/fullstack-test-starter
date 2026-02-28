<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Service\DataImporter;

try {
    $importer = new DataImporter();
    $importer->import(__DIR__ . '/../data.json');

    echo "Import successful!";
} catch (Throwable $e) {
    echo "Import failed: " . $e->getMessage();
}