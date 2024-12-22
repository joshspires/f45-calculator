<?php
// Load database configuration
$config = include('db_config.php');
$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

// Check connection
if ($conn->connect_error) {
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

    $usedNames = [];

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
