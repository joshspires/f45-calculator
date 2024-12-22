<?php
// Load database configuration from environment variables
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 3306; // Default to 3306 if not set

// Enable SSL options if needed
$sslOptions = [
    MYSQLI_OPT_SSL_VERIFY_SERVER_CERT => true,
    MYSQLI_INIT_COMMAND => "SET SESSION sql_mode = 'STRICT_ALL_TABLES'"
];

// Create a connection with error handling
$conn = mysqli_init();
mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
if (!$conn->real_connect($host, $username, $password, $dbname, $port)) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch the last 10 rows
$sql = "SELECT total_score 
        FROM scores 
        ORDER BY total_score DESC 
        LIMIT 10";
$result = $conn->query($sql);

// Array of random names
$names = ["Fluffy Puffy", "Smudgy Fudgy", "Rocky Jockey", "Princess Peach", "Big Fluff", "Budgy Smuggler", "Rocky Road", "Princess Bliss", "Fluffy Buffy", "Smudgy Nudgy"];

// Query to get the total number of rows in the `scores` table
$countQuery = "SELECT COUNT(*) as total_rows FROM scores";
$countResult = $conn->query($countQuery);
$totalRows = 0;

if ($countResult && $countResult->num_rows > 0) {
    $row = $countResult->fetch_assoc();
    $totalRows = $row['total_rows'];
}    
// Display the total number of rows
echo "<p>Top 10 results out of $totalRows.</p>";

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<thead>";
    echo "<tr><th>Name</th><th>Total Score</th></tr>";
    echo "</thead><tbody>";

    while ($row = $result->fetch_assoc()) {
        // Select a random name and remove it from the array to avoid duplication
        $randomIndex = array_rand($names);
        $name = $names[$randomIndex];

        // Remove the selected name from the array
        unset($names[$randomIndex]);

        // Escape the output to prevent XSS
        $total_score = htmlspecialchars($row['total_score']);
        
        echo "<tr>";
        echo "<td>$name</td>";
        echo "<td>$total_score</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No leaderboard data available.</p>";
}

$conn->close();
?>
