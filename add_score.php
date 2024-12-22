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

// Function to generate a random unique ID
function generateUniqueId($conn) {
    $unique = false;
    $id = 0;

    while (!$unique) {
        // Generate a random number between 1 and 99,999,999,999 (11 digits)
        $id = rand(1, 99999999999);

        // Check if this ID already exists in the database
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM scores WHERE id = ?");
        $stmt->bind_param("d", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            $unique = true;
        }

        $stmt->close();
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

if (!$stmt->execute()) {
    die("Error inserting data: " . $stmt->error);
}

$stmt->close();

// Close the connection
$conn->close();
?>
