<?php

declare(strict_types=1);
require(__DIR__ . "/backend/vendor/autoload.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yrgopelago</title>
    <link rel="stylesheet" href="frontend/styles/styles.css">
</head>

<body>

    <?php require(__DIR__ . "/frontend/views/calendar.php"); ?>

    <!-- Booking  -->

    <form action="/backend/booking.php" method="post" class="booking">


        <input type="hidden" name="arrival" id="arrivalInput">
        <input type="hidden" name="departure" id="departureInput">

        <p id="selectedDates" class="date-preview">
            Select arrival and departure dates
        </p>

        <label for="guestId" class="block mt-3">Your name (guest_id)</label>
        <input type="text" name="guestId" class="form-input" required>

        <label for="transferCode" class="block mt-3">Transfer code</label>
        <input type="text" name="transferCode" class="form-input" required>

        <label for="room" class="block mt-3">Room</label>
        <select name="room" class="form-input pr-12">
            <option value="1">Economy</option>
            <option value="2">Standard</option>
            <option value="3">Luxury</option>
        </select>

        <br>
        <label class="block mt-6">Features</label>

        <section class="featureWrapper">

            <!-- Water -->
            <div class="featureCategory">
                <p>Water</p>
                <label><input type="checkbox" name="features[]" value="1"> Pool</label>
                <label><input type="checkbox" name="features[]" value="2"> Scuba Diving</label>
                <label><input type="checkbox" name="features[]" value="3"> Olympic Pool</label>
                <label><input type="checkbox" name="features[]" value="4"> Waterpark</label>
            </div>

            <!-- Games -->
            <div class="featureCategory">
                <p>Games</p>
                <label><input type="checkbox" name="features[]" value="5"> Yahtzee</label>
                <label><input type="checkbox" name="features[]" value="6"> Ping Pong</label>
                <label><input type="checkbox" name="features[]" value="7"> PS5</label>
                <label><input type="checkbox" name="features[]" value="8"> Casino</label>
            </div>

            <!-- Wheels -->
            <div class="featureCategory">
                <p>Wheels</p>
                <label><input type="checkbox" name="features[]" value="9"> Unicycle</label>
                <label><input type="checkbox" name="features[]" value="10"> Bicycle</label>
                <label><input type="checkbox" name="features[]" value="11"> Trike</label>
                <label><input type="checkbox" name="features[]" value="12"> Motorized Beast</label>
            </div>

            <!-- Hotel -->
            <div class="featureCategory">
                <p>Hotel-specific</p>
                <label><input type="checkbox" name="features[]" value="13"> Carpet</label>
                <label><input type="checkbox" name="features[]" value="14"> Good Dog</label>
                <label><input type="checkbox" name="features[]" value="15"> Fireplace</label>
                <label><input type="checkbox" name="features[]" value="16"> Butler</label>
            </div>

        </section>

        <button type="submit">Book your visit now!</button>

        <!-- Status messages -->
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'success'): ?>
                <p class="success-message">Booking saved successfully!</p>
            <?php elseif ($_GET['status'] === 'transfer_expired'): ?>
                <p class="error-message">Transfer code expired. Please enter a valid code.</p>
            <?php elseif ($_GET['status'] === 'error'): ?>
                <p class="error-message">Something went wrong. Please try again.</p>
            <?php endif; ?>
        <?php endif; ?>

    </form>

</body>

</html>