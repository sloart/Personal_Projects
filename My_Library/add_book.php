<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/forms.css">
</head>
<body>

<?php
$page_title = 'Add New Book';
include('includes/header.html');

function sanitize_input($dbc, $data) {
    return mysqli_real_escape_string($dbc, trim($data));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require('mysqli_connect.php');
    $errors = [];

    $isbn = empty($_POST['ISBN']) ? $errors[] = 'You forgot to enter an ISBN.' : sanitize_input($dbc, $_POST['ISBN']);
    $title = empty($_POST['Title']) ? $errors[] = 'You forgot to enter a title.' : sanitize_input($dbc, $_POST['Title']);
    $author = empty($_POST['Author']) ? $errors[] = 'You forgot to enter the author\'s name.' : sanitize_input($dbc, $_POST['Author']);
    $genre = sanitize_input($dbc, $_POST['Genre']);
    $cover = sanitize_input($dbc, $_POST['CoverImageURL']);
    $notes = sanitize_input($dbc, $_POST['Notes']);

    if (empty($errors)) {
        $stmt = mysqli_prepare($dbc, "INSERT INTO book (ISBN, Title, Author, Genre, CoverImageURL, Notes) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssssss', $isbn, $title, $author, $genre, $cover, $notes);

        if (mysqli_stmt_execute($stmt)) {
            $new_id = mysqli_insert_id($dbc);
            echo "<h1>Success!</h1><p>Another book on the shelf.</p><p>Edit the details <a href='edit_book.php?id=$new_id'>here</a> or click 'edit' next to the book in the library.</p><p><br></p>";
        } else {
            echo "<h1>System Error</h1><p class='error'>The book could not be added due to a system error. We apologize for any inconvenience.</p>";
            echo '<p>' . mysqli_error($dbc) . '<br><br>Query: ' . $q . '</p>';
        }

        mysqli_stmt_close($stmt);
        mysqli_close($dbc);
        include('includes/footer.html');
        exit();
    } else {
        echo '<h1>Error!</h1><p class="error">The following error(s) occurred:<br>';
        foreach ($errors as $msg) {
            echo " - $msg<br>\n";
        }
        echo '</p><p>Please try again.</p><p><br></p>';
    }

    mysqli_close($dbc);
}
?>

<h1>Add Book to Library</h1>
<form action="add_book.php" method="post">
    <fieldset id="isbn-title-author">
        <legend>ISBN, Title, Author <em>(required)</em></legend>
        <div><label for="ISBN">ISBN: <span aria-label="required">*</span></label>
            <input type="text" id="ISBN" name="ISBN" required></div>
        <div><label for="Title">Title: <span aria-label="required">*</span></label>
            <input type="text" id="Title" name="Title" required></div>
        <div><label for="Author">Author(s): <span aria-label="required">*</span></label>
            <input type="text" id="Author" name="Author" required></div>
    </fieldset>
    <fieldset id="genre-cover-notes">
        <legend>Genre/Subjects, Cover Image URL, Notes <em>(optional)</em></legend>
        <div><label for="Genre">Genre/Subjects:</label>
            <input type="text" id="Genre" name="Genre"></div>
        <div><label for="CoverImageURL">Cover Image URL:</label>
            <input type="text" id="CoverImageURL" name="CoverImageURL"></div>
        <div><label for="Notes">Notes:</label>
            <textarea id="Notes" name="Notes" class="multi-line"></textarea></div>
    </fieldset>
    <div><input type="submit" name="submit" class="button" value="Submit"></div>
    <input type="hidden" name="id" value="">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var isbnInput = document.getElementById('ISBN');

        isbnInput.addEventListener('blur', function() {
            var isbn = isbnInput.value.trim();
            if (isbn) {
                // Construct the cover image URL using the inputted ISBN
                var coverImageUrl = `https://covers.openlibrary.org/b/isbn/${isbn}-L.jpg`;
                // Autofill the CoverImageURL input field
                document.getElementById('CoverImageURL').value = coverImageUrl;

                var isbnApiUrl = 'https://openlibrary.org/isbn/' + isbn + '.json';
                fetch(isbnApiUrl).then(response => response.json()).then(bookInfo => {
                    console.log('Book Info:', bookInfo); // Log the book information

                    if (bookInfo) {
                        var fullTitle = bookInfo.title;
                        if (bookInfo.subtitle) {
                            fullTitle += ': ' + bookInfo.subtitle;
                        }
                        document.getElementById('Title').value = fullTitle || '';

                        if (bookInfo.authors) {
                            Promise.all(bookInfo.authors.map(author => {
                                let authorUrl = `https://openlibrary.org${author.key}.json`;
                                return fetch(authorUrl)
                                    .then(response => response.json())
                                    .then(authorDetails => authorDetails.name)
                                    .catch(() => 'Unknown Author');
                            })).then(names => {
                                document.getElementById('Author').value = names.join(', ');
                            });
                        } else {
                            document.getElementById('Author').value = '';
                        }

                        if (bookInfo.works && bookInfo.works[0] && bookInfo.works[0].key) {
                            var workId = bookInfo.works[0].key;
                            const worksApiUrl = `https://openlibrary.org${workId}.json`;

                            fetch(worksApiUrl)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(workData => {
                                    console.log('Work Data:', workData);

                                    if (workData.subjects) {
                                        // Map over the subjects array and handle both string and object types
                                        const subjectsStr = workData.subjects.map(subject => {
                                            // Check if the subject is a string or an object with a name property
                                            return typeof subject === 'string' ? subject : subject.name;
                                        }).join(' | ');

                                        document.getElementById('Genre').value = subjectsStr;
                                    } else {
                                        document.getElementById('Genre').value = 'No subjects available';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching or processing work data:', error);
                                    document.getElementById('Genre').value = 'Error fetching subjects';
                                });
                        }
                    }
                }).catch(error => console.error('Error fetching book details:', error));
            }
        });
    });
</script>

<?php include('includes/footer.html'); ?>
</body>
</html>
