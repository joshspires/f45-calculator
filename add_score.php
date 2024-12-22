<?php
// Load database configuration
$config = include('db_config.php');

// Create a connection
$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

// Function to generate a random unique ID
function generateUniqueId($conn) {
    $unique = false;
    $id = 0;

    while (!$unique) {
        // Generate a random number between 1 and 99,999,999,999 (11 digits)
        $id = rand(1, 99999999999);

        // Check if this ID already exists in the database
        $result = $conn->query("SELECT COUNT(*) AS count FROM scores WHERE id = $id");
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            $unique = true;
        }
    }

    return $id;
}

// Get data from POST request
$scoreOne = $_POST['score_one'];
$scoreTwo = $_POST['score_two'];
$scoreThree = $_POST['score_three'];
$scoreFour = $_POST['score_four'];
$scoreFive = $_POST['score_five'];
$scoreSix = $_POST['score_six'];
$scoreSeven = $_POST['score_seven'];
$scoreEight = $_POST['score_eight'];
$scoreNine = $_POST['score_nine'];
$scoreTen = $_POST['score_ten'];
$score = $_POST['score'];

// Generate a unique random ID
$uniqueId = generateUniqueId($conn);

// Prepare and execute the SQL query to insert individual scores with the random ID
$stmt = $conn->prepare("INSERT INTO scores (id, score_one, score_two, score_three, score_four, score_five, score_six, score_seven, score_eight, score_nine, score_ten, total_score) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("dddddddddddd", $uniqueId, $scoreOne, $scoreTwo, $scoreThree, $scoreFour, $scoreFive, $scoreSix, $scoreSeven, $scoreEight, $scoreNine, $scoreTen, $score);

$stmt->execute();
$stmt->close();

// Close the connection
$conn->close();
?>