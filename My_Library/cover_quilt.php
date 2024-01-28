<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
     <link rel="stylesheet" href="css/quilt.css">
</head>


<?php # cover_quilt.php

$page_title = 'Cover Quilt';
include('includes/header.html');
require('mysqli_connect.php');

// Query to fetch all books
$query = "SELECT ID, Title, CoverImageURL FROM book ORDER BY RAND()";
$result = mysqli_query($dbc, $query);

echo '<div class="quilt">';

if ($result && mysqli_num_rows($result) > 0) {
    echo '<ul class="quilt">';
    while ($row = mysqli_fetch_assoc($result)) {
        // Check if CoverImageURL is empty
        if (empty($row['CoverImageURL'])) {
            continue; // Skip this iteration and go to the next record
        }
        echo '<li class="quilt"><img class="quilt" src="' . htmlspecialchars($row['CoverImageURL']) . '" alt="' . htmlspecialchars($row['Title']) . '" loading="lazy"></li>';
    }

    echo '</ul>';
}

echo '</div>';

mysqli_free_result($result);
mysqli_close($dbc);
?>

<script src="scripts.js" type="text/javascript"></script>

</body>