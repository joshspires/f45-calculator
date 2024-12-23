<?php
require 'vendor/autoload.php'; // Load MongoDB library
use MongoDB\Driver\ServerApi;

$uri = 'mongodb+srv://admin_dc347w:<db_password>@f45calculator.9ccmp.mongodb.net/?retryWrites=true&w=majority&appName=F45Calculator';

// Set the version of the Stable API on the client
$apiVersion = new ServerApi(ServerApi::V1);

// Create a new client and connect to the server
$client = new MongoDB\Client($uri, [], ['serverApi' => $apiVersion]);

try {
    // Confirm a successful connection
    $client->selectDatabase('admin')->command(['ping' => 1]);
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// Database and collection references
$database = $client->selectDatabase('f45calculator');
$scoresCollection = $database->selectCollection('scores');

// Query to fetch the top 10 scores
$topScoresCursor = $scoresCollection->find(
    [],
    [
        'sort' => ['total_score' => -1],
        'limit' => 10,
    ]
);

$topScores = iterator_to_array($topScoresCursor);

// Count the total number of documents in the `scores` collection
$totalRows = $scoresCollection->countDocuments();

// Array of random names
$names = ["Fluffy Puffy", "Smudgy Fudgy", "Rocky Jockey", "Princess Peach", "Big Fluff", "Budgy Smuggler", "Rocky Road", "Princess Bliss", "Fluffy Buffy", "Smudgy Nudgy"];

echo "<p>Top 10 results out of $totalRows.</p>";

if (!empty($topScores)) {
    echo "<table>";
    echo "<thead>";
    echo "<tr><th>Name</th><th>Total Score</th></tr>";
    echo "</thead><tbody>";

    foreach ($topScores as $document) {
        // Select a random name and remove it from the array to avoid duplication
        $randomIndex = array_rand($names);
        $name = $names[$randomIndex];
        unset($names[$randomIndex]);

        // Escape the output to prevent XSS
        $total_score = htmlspecialchars($document['total_score']);

        echo "<tr>";
        echo "<td>$name</td>";
        echo "<td>$total_score</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No leaderboard data available.</p>";
}
?>
