<?php
// Database connection details
$servername = "localhost";
$username = "lamaat_usr";
$password = "Lamaat@123#";
$dbname = "4lamaat";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve and sanitize user inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_NUMBER_INT);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $services = filter_input(INPUT_POST, 'services', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);

    // Validate sanitized inputs
    if ($name && $email && $number && $address && $services && $date && $time) {
        // Check if the booking is after 2 hours of the current time
        $bookingDateTime = new DateTime("$date $time");
        $currentDateTime = new DateTime();
        $currentDateTime->modify('+2 hours');

        if ($bookingDateTime < $currentDateTime) {
            echo json_encode(['success' => false, 'message' => 'Booking time must be at least 2 hours from now.']);
            exit();
        }

        // Check if maximum slots for the day are booked
        $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE Bookingdate = :date");
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $bookingsForDay = $stmt->fetchColumn();

        if ($bookingsForDay >= 8) {
            echo json_encode(['success' => false, 'message' => 'Maximum 8 slots can be booked in a day.']);
            exit();
        }

        // Check if the time slot is already booked
        $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE Bookingdate = :date AND time_sloat = :time");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->execute();
        $bookingsForTime = $stmt->fetchColumn();

        if ($bookingsForTime >= 2) {
            echo json_encode(['success' => false, 'message' => 'This time slot is already fully booked.']);
            exit();
        }

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO bookings (name, email, number, address, services, Bookingdate, time_sloat) VALUES (:name, :email, :number, :address, :services, :date, :time)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':number', $number);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':services', $services);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);

        // Execute the prepared statement
        $stmt->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input!']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
}

$conn = null;
?>
