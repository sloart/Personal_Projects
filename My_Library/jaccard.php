<?php
# jaccard.php
// This script attempts to pull the genres and titles from the db,
// preprocess the text, and then run the algorithm on the books.

$page_title = 'Jaccard Similarity';
include('includes/header.html');
require('mysqli_connect.php');

try {
    // SQL query to select id, title, and genre from the book table
    $sql = "SELECT id, title, genre FROM book";

    // Execute the query
    $stmt = mysqli_query($dbc, $sql);

    // Processed books array, book titles array, ignore words array
    $processedBooks = [];
    $bookTitles = [];
    $ignoreWords = ['the', 'of', '&', 'and'];

    while ($row = mysqli_fetch_array($stmt, MYSQLI_ASSOC)) {
        // Split by pipe symbol '|' and also by spaces to tokenize every word
        $genres = preg_split('/[|\s]+/', $row['genre'], -1, PREG_SPLIT_NO_EMPTY);

        // Convert each token to lowercase, trim spaces, filter out ignored words, and ensure uniqueness
        $genres = array_unique(array_filter(array_map(function($genre) use ($ignoreWords) {
            $token = trim(strtolower($genre));
            return in_array($token, $ignoreWords) ? '' : $token; // Filter out ignored words
        }, $genres), function($genre) {
            return $genre !== ''; // Filter out empty tokens resulting from ignored words
        }));

        $processedBooks[$row['id']] = $genres;
        $bookTitles[$row['id']] = $row['title']; // Store book title
    }



    // Function to calculate Jaccard Similarity
    function jaccardSimilarity($set1, $set2) {
        $intersection = count(array_intersect($set1, $set2));
        $union = count(array_unique(array_merge($set1, $set2)));
        return $union == 0 ? 0 : $intersection / $union;
    }

    // Calculate similarities and common genres
    $similarities = [];
    $commonGenres = [];
    foreach ($processedBooks as $bookId1 => $genres1) {
        foreach ($processedBooks as $bookId2 => $genres2) {
            if ($bookId1 >= $bookId2) continue; // Avoid redundant calculations
            $similarity = jaccardSimilarity($genres1, $genres2);
            if ($similarity > 0) {
                $title1 = $bookTitles[$bookId1];
                $title2 = $bookTitles[$bookId2];
                $common = array_intersect($genres1, $genres2); // Find common genres
                $commonGenres["$title1-$title2"] = $common; // Store common genres
                $similarities[] = [$title1, $title2, round($similarity, 4), $common]; // Include common genres
            }
        }
    }


    usort($similarities, function($a, $b) {
        $epsilon = 0.00001; // Small value to account for floating-point precision
        $diff = $b[2] - $a[2];
        if (abs($diff) < $epsilon) { // If the difference is smaller than epsilon, consider the values equal
            return 0;
        }
        return ($diff > 0) ? 1 : -1; // Adjusted to return -1 or 1 based on the comparison
    });


    // Start HTML output
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$page_title</title>
        <style>
            table {
            width: 100%;
            border-collapse: collapse;
            }
            th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            }
            tr:nth-child(even) {background-color: #f2f2f2;}
        </style>

    </head>
    <body>
        <h1>$page_title</h1>
        <table border='1'>
            <tr>
                <th>Book 1</th>
                <th>Book 2</th>
                <th>Jaccard Similarity</th>
            </tr>";

    // Output each similarity as a table row with common subjects as a tooltip
    foreach ($similarities as $pair) {
        $tooltip = implode(', ', $pair[3]); // Convert common words array to comma-separated string
        echo "<tr title='In common: $tooltip'>
            <td>{$pair[0]}</td>
            <td>{$pair[1]}</td>
            <td>{$pair[2]}</td>
          </tr>";
    }


    echo "</table>
    </body>
    </html>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Close the connection
mysqli_close($dbc);
include('includes/header.html');

?>
