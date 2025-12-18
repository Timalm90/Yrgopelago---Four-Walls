<?php

declare(strict_types=1);

if (
    isset(
        $_POST['guestId'],
        $_POST['transferCode'],
        $_POST['arrival'],
        $_POST['departure'],
        $_POST['room']
    )
) {
    $guestName    = trim($_POST['guestId']);
    $transferCode = trim($_POST['transferCode']);
    $arrival      = $_POST['arrival'];
    $departure    = $_POST['departure'];
    $roomId       = (int) $_POST['room'];
    $featuresUsed = $_POST['features'] ?? [];

    // Database
    $database = new PDO('sqlite:database/database.db');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // Guests
    $statement = $database->prepare('SELECT id, visits FROM guests WHERE name = :name LIMIT 1');
    $statement->execute([':name' => $guestName]);
    $guest = $statement->fetch(PDO::FETCH_ASSOC);

    if (!preg_match('/^[a-zA-Z0-9 _-]{1,50}$/', $guestName)) {
        die('Invalid guest name');
    }


    if ($guest) {
        // check if guest exists
        $guestId = (int) $guest['id'];
        $visits  = (int) $guest['visits'] + 1;

        $statement = $database->prepare('UPDATE guests SET visits = :visits WHERE id = :id');

        $statement->bindParam(':visits', $visits, PDO::PARAM_INT);
        $statement->bindParam(':id', $guestId, PDO::PARAM_INT);
        $statement->execute();
    } else {
        // New guest
        $statement = $database->prepare('INSERT INTO guests (name, visits) VALUES (:name, 1)');

        $statement->bindParam(':name', $guestName, PDO::PARAM_STR);
        $statement->execute();

        $guestId = (int) $database->lastInsertId();
    }


    //booking check if departure is set (otherwise set 1 day)

    if (!$departure) {
        $departure = date('Y-m-d', strtotime($arrival . ' +1 day'));
    }

    if ($arrival >= $departure) {
        die('Error: Departure date must be after arrival date.');
    }

    //calculate number of nights stayed

    $dateArrive = new DateTime($arrival);
    $dateDepart = new DateTime($departure);

    $interval = $dateArrive->diff($dateDepart);
    $nights = (int) $interval->format('%a');

    // sets at least 1 night stay
    $nights = max(1, $nights);


    // Room price
    $statement = $database->prepare('SELECT price_per_night FROM rooms WHERE id = :room_id');

    $statement->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $statement->execute();

    $roomPricePerNight = (float) $statement->fetchColumn();




    // Feature price

    $totalPriceFeature = 0;

    foreach ($featuresUsed as $featureId) {
        $statement = $database->prepare(
            'SELECT price FROM features WHERE id = :id'
        );

        $statement->execute([':id' => $featureId]);
        $totalPriceFeature += (float) $statement->fetchColumn();
    }

    $totalPrice = ($roomPricePerNight * $nights) + $totalPriceFeature;




    // Insert into booking

    $statement = $database->prepare(
        'INSERT INTO bookings (
            room_id,
            guest_id,
            arrival_date,
            departure_date,
            transfer_code,
            price,
            creation_time
        ) VALUES (
            :room_id,
            :guest_id,
            :arrival,
            :departure,
            :transfer_code,
            :price,
            DATE("now")
        )'
    );

    $statement->execute([
        ':room_id'       => $roomId,
        ':guest_id'      => $guestId,
        ':arrival'       => $arrival,
        ':departure'     => $departure,
        ':transfer_code' => $transferCode,
        ':price'         => $totalPrice
    ]);

    $bookingId = (int) $database->lastInsertId();

    // Booking features

    if (!empty($featuresUsed)) {
        $statement = $database->prepare(
            'INSERT INTO bookings_features (booking_id, feature_id) VALUES (:booking_id, :feature_id)'
        );

        foreach ($featuresUsed as $featureId) {
            $statement->execute([
                ':booking_id' => $bookingId,
                ':feature_id' => (int) $featureId
            ]);
        }
    }

    echo 'Booking saved successfully!';
}
