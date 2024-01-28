<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/forms.css">
    <title>Edit Book</title>
</head>
<body>

<?php
require('includes/header.html');
require('mysqli_connect.php');

function sanitize_input($dbc, $input) {
    return mysqli_real_escape_string($dbc, trim($input));
}

// Initialize variables
$id = $isbn = $title = $author = $genre = $cover = $notes = '';
$errors = [];

// Validate ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];
} else {
    echo '<p class="error">This page has been accessed in error.</p>';
    include('includes/footer.html');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isbn = empty($_POST['ISBN']) ? $errors[] = 'You forgot to enter an ISBN.' : sanitize_input($dbc, $_POST['ISBN']);
    $title = empty($_POST['Title']) ? $errors[] = 'You forgot to enter a title.' : sanitize_input($dbc, $_POST['Title']);
    $author = empty($_POST['Author']) ? $errors[] = 'You forgot to enter an author name.' : sanitize_input($dbc, $_POST['Author']);
    $genre = sanitize_input($dbc, $_POST['Genre']);
    $cover = sanitize_input($dbc, $_POST['CoverImageURL']);
    $notes = sanitize_input($dbc, $_POST['Notes']);

    if (empty($errors)) {
        $q = "UPDATE book SET ISBN=?, Title=?, Author=?, Genre=?, CoverImageURL=?, Notes=? WHERE ID=? LIMIT 1";
        $stmt = mysqli_prepare($dbc, $q);
        mysqli_stmt_bind_param($stmt, 'ssssssi', $isbn, $title, $author, $genre, $cover, $notes, $id);

        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) == 1) {
            echo '<p>The book has been edited.</p>';
        } else {
            echo '<p class="error">The book could not be edited due to a system error.</p>';
        }
    } else {
        echo '<p class="error">The following error(s) occurred:<br>' . implode("<br>", $errors) . '</p><p>Please try again.</p>';
    }
}

// Retrieve the book's information for the form
if (!empty($id)) {
    $q = "SELECT ISBN, Title, Author, Genre, CoverImageURL, Notes FROM book WHERE ID = ?";
    $stmt = mysqli_prepare($dbc, $q);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $isbn, $title, $author, $genre, $cover, $notes);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Display the form
?>
<h1>Edit Book</h1>
<form action="edit_book.php" method="post">
    <fieldset id="isbn-title-author">
        <legend>ISBN, Title, Author <em>(required)</em></legend>
        <div>
            <label for="ISBN">ISBN: <span aria-label="required">*</span></label>
            <input type="text" id="ISBN" name="ISBN" required value="<?php echo htmlspecialchars($isbn); ?>">
        </div>
        <div>
            <label for="Title">Title: <span aria-label="required">*</span></label>
            <input type="text" id="Title" name="Title" required value="<?php echo htmlspecialchars($title); ?>">
        </div>
        <div>
            <label for="Author">Author: <span aria-label="required">*</span></label>
            <input type="text" id="Author" name="Author" required value="<?php echo htmlspecialchars($author); ?>">
        </div>
    </fieldset>

    <fieldset id="genre-cover-notes">
        <legend>Genre/Subjects, Cover Image URL, Notes <em>(optional)</em></legend>
        <div>
            <label for="Genre">Genre/Subjects:</label>
            <input type="text" id="Genre" name="Genre" value="<?php echo htmlspecialchars($genre); ?>">
        </div>
        <div>
            <label for="CoverImageURL">Cover Image URL:</label>
            <input type="text" id="CoverImageURL" name="CoverImageURL" value="<?php echo htmlspecialchars($cover); ?>">
        </div>
        <div>
            <label for="Notes">Notes:</label>
            <textarea id="Notes" name="Notes" class="multi-line"><?php echo htmlspecialchars($notes); ?></textarea>
        </div>
    </fieldset>

    <div>
        <input type="submit" name="submit" class="button" value="Submit">
    </div>
    <input type="hidden" name="id" value="<?php echo $id; ?>">
</form>

<?php
mysqli_close($dbc);
include('includes/footer.html');
?>

</body>
</html>
