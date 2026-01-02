<?php
require __DIR__ . '/../vendor/autoload.php';
$reg = new \Livewire\Mechanisms\ComponentRegistry();
try {
    $c = $reg->getClass('profile.credentials.subsections.highschool-subject-records.highschool-subject-records-details-modal');
    echo "RESOLVED: " . $c . PHP_EOL;
} catch (Exception $e) {
    echo "ERROR: " . get_class($e) . " - " . $e->getMessage() . PHP_EOL;
}
