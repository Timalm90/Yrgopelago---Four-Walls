<?php

if (isset(
    $_POST['guestId'],
    $_POST['transferCode'],
    $_POST['arrival'],
    $_POST['departure'],
    $_POST['room'],
)) {

    $guestName = trim($_POST['guestId']);
    $transferCode = trim($_POST['transferCode']);
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];
    $roomId = (int) $_POST['room'];
    $featuresUsed = $_POST['features'] ?? [];


    //Database logic

    $database = new PDO('sqlite:database/database.db');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if guest already exists
    $statement = $database->prepare('SELECT id, visits FROM guests WHERE name = :name LIMIT 1');
    $statement->execute([':name' => $guestName]);
    $guest = $statement->fetch(PDO::FETCH_ASSOC);

    if ($guest) {
        $guestId = (int) $guest['id'];
        $visits = (int) $guest['visits'] + 1;

        $statement = $database->prepare('UPDATE guests SET visits = :visits WHERE id = :id');
        $statement->execute([
            ':visits' => $visits,
            ':id'     => $guestId
        ]);
    } else {
        // New guest
        $statement = $database->prepare('INSERT INTO guests (name, visits) VALUES (:name, 1)');
        $statement->execute([':name' => $guestName]);
        $guestId = (int) $database->lastInsertId();
    }

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

    ///TO DO: INSERT CORRECT Price!!!"E

    $price = 100;

    /////



    $statement->execute([
        ':room_id'       => $roomId,
        ':guest_id'      => $guestId,
        ':arrival'       => $arrival,
        ':departure'     => $departure,
        ':transfer_code' => $transferCode,
        ':price'         => $price
    ]);

    $bookingId = (int) $database->lastInsertId();


    if (!empty($featuresUsed)) {
        $statement = $database->prepare(
            'INSERT INTO bookings_features (booking_id, feature_id)
             VALUES (:booking_id, :feature_id)'
        );

        foreach ($featuresUsed as $featureId) {
            $statement->execute([
                ':booking_id' => $bookingId,
                ':feature_id' => (int) $featureId
            ]);
        }
    }

    echo 'Booking saved!';
}
