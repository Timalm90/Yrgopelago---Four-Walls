<?php

$calendar = require __DIR__ . '/../../backend/calendar_data.php';

$booked        = $calendar['booked'];
$daysInMonth   = $calendar['daysInMonth'];
$firstDayIndex = $calendar['firstDayIndex'];
?>

<script>
    const bookedDays = <?= json_encode($booked) ?>;
    const calendarYear = 2026;
    const calendarMonth = 1; // January
</script>

<script src="/frontend/scripts/calendar.js" defer></script>

<section class="calendar" id="calendar">

    <!-- Empty cells before first weekday -->
    <?php for ($i = 1; $i < $firstDayIndex; $i++): ?>
        <div class="day empty"></div>
    <?php endfor; ?>

    <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
        <?php $isBooked = in_array($day, $booked, true); ?>
        <div
            class="day <?= $isBooked ? 'booked' : 'available' ?>"
            data-day="<?= $day ?>">
            <?= $day ?>
        </div>
    <?php endfor; ?>

</section>