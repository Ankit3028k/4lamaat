<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Database connection
$host = 'localhost';
$dbname = '4lamaat';
$username = 'lamaat_usr';
$password = 'Lamaat@123#';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from bookings table
$query = "SELECT * FROM bookings";
$data = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Data</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Bookings Data</h1>
    <?php
    if (mysqli_num_rows($data) > 0) {
        echo "<table>";
        echo "<tr>
                <th>Name</th>
                <th>Email</th>
                <th>Number</th>
                <th>Services</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>address</th>
              </tr>";

        while ($result = mysqli_fetch_assoc($data)) {
            echo "<tr>
                    <td>".$result['name']."</td>
                    <td>".$result['email']."</td>
                    <td>".$result['number']."</td>
                    <td>".$result['services']."</td>
                    <td>".$result['Bookingdate']."</td>
                    <td>".$result['time_sloat']."</td>
                    <td>".$result['address']."</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "No records found.";
    }

    mysqli_close($conn);
    ?>
</body>
</html>
