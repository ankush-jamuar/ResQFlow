<?php
$service = app(App\Services\HospitalLocatorService::class);
$h = $service->nearest(31.22, 75.77);
if ($h) {
    echo "Nearest to [31.22, 75.77]: {$h->name} (ID: {$h->id}), Distance: " . round($h->distance, 2) . " km\n";
} else {
    echo "No hospital found.\n";
}
