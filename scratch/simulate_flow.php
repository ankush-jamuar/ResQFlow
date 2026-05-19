<?php
use App\Models\User;
use App\Models\EmergencyRequest;
use App\Http\Controllers\EmergencyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
App\Models\EmergencyAudit::truncate();
App\Models\EmergencyRequest::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
App\Models\Ambulance::where('status', '!=', 'available')->update(['status' => 'available']);

$user = User::where('role', 'user')->first();
auth()->login($user);

$controller = app(EmergencyController::class);

echo "--- PHASE 1: SOS TRIGGER (Initial State: accepted via auto-dispatch) ---\n";
$request = new Request([
    'latitude' => 31.22,
    'longitude' => 75.77,
    'severity' => 'high',
    'emergency_type' => 'Full Lifecycle Test'
]);
$response = $controller->store($request);
$data = json_decode($response->getContent(), true);
$emergencyId = $data['emergency_id'];
$emergency = EmergencyRequest::find($emergencyId);
echo "Status: {$emergency->status}\n";

echo "\n--- PHASE 2: DISPATCH (accepted -> dispatched) ---\n";
$controller->updateStatus(new Request(['status' => 'dispatched', 'ambulance_id' => $emergency->ambulance_id]), $emergency);
$emergency->refresh();
echo "Status: {$emergency->status}, Unit: {$emergency->ambulance->status}\n";

echo "\n--- PHASE 3: EN ROUTE (dispatched -> en_route) ---\n";
$controller->updateStatus(new Request(['status' => 'en_route']), $emergency);
$emergency->refresh();
echo "Status: {$emergency->status}, Unit: {$emergency->ambulance->status}\n";

echo "\n--- PHASE 4: TRACKING POLLING ---\n";
for ($i = 1; $i <= 2; $i++) {
    $trackResponse = $controller->track(new Request(['id' => $emergencyId]));
    $trackData = json_decode($trackResponse->getContent(), true);
    echo "Poll #{$i}: ETA: {$trackData['eta_minutes']} min, Unit At: [{$trackData['ambulance_lat']}, {$trackData['ambulance_lng']}]\n";
}

echo "\n--- PHASE 5: ARRIVAL (en_route -> arrived) ---\n";
$controller->updateStatus(new Request(['status' => 'arrived']), $emergency);
$emergency->refresh();
echo "Status: {$emergency->status}\n";

echo "\n--- PHASE 6: COMPLETION (arrived -> completed) ---\n";
$controller->updateStatus(new Request(['status' => 'completed']), $emergency);
$emergency->refresh();
echo "Final Status: {$emergency->status}\n";
echo "Ambulance Released: {$emergency->ambulance->status}\n";
echo "Audit Log Count: " . $emergency->audits()->count() . "\n";
