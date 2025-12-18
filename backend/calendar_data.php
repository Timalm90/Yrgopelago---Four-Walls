<?php

declare(strict_types=1);

$database = require __DIR__ . '/database.php';

$year  = 2026;
$month = 1;

$monthStart = "$year-01-01";
$monthEnd   = "$year-02-01";

$statement = $database->prepare(
    'SELECT arrival_date, departure_date
     FROM bookings
     WHERE arrival_date < :month_end
       AND departure_date > :month_start'
);

$statement->execute([
    ':month_start' => $monthStart,
    ':month_end'   => $monthEnd,
]);

$bookings = $statement->fetchAll(PDO::FETCH_ASSOC);

$booked = [];

foreach ($bookings as $booking) {
    $start = new DateTime($booking['arrival_date']);
    $end   = new DateTime($booking['departure_date']);

    while ($start < $end) {
        if ((int)$start->format('m') === $month) {
            $booked[] = (int)$start->format('j');
        }
        $start->modify('+1 day');
    }
}

$booked = array_values(array_unique($booked));

$daysInMonth   = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayIndex = (int)(new DateTime("$year-$month-01"))->format('N');

return [
    'booked'        => $booked,
    'daysInMonth'   => $daysInMonth,
    'firstDayIndex' => $firstDayIndex,
];
