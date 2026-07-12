<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$people = App\Models\People::all()->toArray();
$rooms = App\Models\Room::all()->toArray();
$facilities = App\Models\Facility::all()->toArray();
$bookings = App\Models\Booking::all()->toArray();
$booking_details = App\Models\BookingDetail::all()->toArray();
$fines = \App\Models\Fine::all()->toArray();
$ratings = \App\Models\Rating::all()->toArray();

echo json_encode(compact('people', 'rooms', 'facilities', 'bookings', 'booking_details', 'fines', 'ratings'), JSON_PRETTY_PRINT);
