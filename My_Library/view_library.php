<?php # view_library.php
// This script retrieves all the records from the book table.
// and allows the results to be sorted in different ways.

$page_title = 'The Library';

echo '<h1>The Library</h1>
		<p>Welcome to my library project (began 12/25/23). This is a database of books I have read or intend to read. They are currently sortable by Author, Title, and Genre. Credit is due to Larry Ullman for his wonderful book <a href="https://larryullman.com/books/php-and-mysql-for-dynamic-web-sites-visual-quickpro-guide-5th-edition/"> PHP and MySQL for Dynamic Web Sites: Visual QuickPro Guide (5th Edition) </a> which was a giant resource in starting this project.</p>
		<p>Most of the book cover images here are pulled directly from <a href="https://openlibrary.org/">Open Library</a>, with just a few exceptions.</p>
		';

require('mysqli_connect.php');

// Number of records to show per page:
$display = 20;

// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
	$pages = $_GET['p'];
} else { // Need to determine.
 	// Count the number of records:
	$q = "SELECT COUNT(ID) FROM book";
	$r = @mysqli_query($dbc, $q);
	$row = @mysqli_fetch_array($r, MYSQLI_NUM);
	$records = $row[0];
	// Calculate the number of pages...
	if ($records > $display) { // More than 1 page.
		$pages = ceil($records/$display);
	} else {
		$pages = 1;
	}
} // End of p IF.

// Determine where in the database to start returning results...
if (isset($_GET['s']) && is_numeric($_GET['s'])) {
	$start = $_GET['s'];
} else {
	$start = 0;
}

// Determine the sort...
// Default is by title.
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 't';

// Determine the sorting order:
switch ($sort) {
	case 't':
		$order_by = 'Title ASC';
		break;
	case 'a':
		$order_by = 'Author ASC';
		break;
	case 'g':
		$order_by = 'Genre ASC';
		break;
	default:
		$order_by = 'Title ASC';
		$sort = 't';
		break;
}

// Define the query:
$q = "SELECT * FROM book ORDER BY $order_by LIMIT $start, $display";
$r = @mysqli_query($dbc, $q); // Run the query.


// Table header:
echo '<table>
<thead>
<tr>
	<th align="left"><strong>Edit</strong></th>
	<th align="left"><strong>Delete</strong></th>
	<th align="left"><strong><a href="index.php?sort=t">Title</a></strong></th>
	<th align="left"><strong><a href="index.php?sort=a">Author(s)</a></strong></th>
	<th align="left"><strong><a href="index.php?sort=g">Genre/Subjects</a></strong></th>
	<th align="left"><strong>Cover Image</th>
</tr>
</thead>
<tbody>
';

// Fetch and print all the records....
$bg = '#eeeeee';
while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
	$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
		echo '<tr bgcolor="' . $bg . '">
		<td align="left"><a href="edit_book.php?id=' . $row['ID'] . '">Edit</a></td>
		<td align="left"><a href="delete_book.php?id=' . $row['ID'] . '">Delete</a></td>
		<td align="left">' . $row['Title'] . '</td>
		<td align="left">' . $row['Author'] . '</td>
        <td align="left" id="truncated">' . $row['Genre'] . '</td>
		<td align="center"><img class="book-cover" src="' . $row['CoverImageURL'] . '" alt="no image yet" loading="lazy"></td>
		</tr>';

} // End of WHILE loop.

echo '</tbody></table>';
mysqli_free_result($r);
mysqli_close($dbc);

// Make the links to other pages, if necessary.
if ($pages > 1) {

	echo '<br><p>';
	$current_page = ($start/$display) + 1;

	// If it's not the first page, make a Previous button:
	if ($current_page != 1) {
		echo '<a href="index.php?s=' . ($start - $display) . '&p=' . $pages . '&sort=' . $sort . '">Previous</a> ';
	}

	// Make all the numbered pages:
	for ($i = 1; $i <= $pages; $i++) {
		if ($i != $current_page) {
			echo '<a href="index.php?s=' . (($display * ($i - 1))) . '&p=' . $pages . '&sort=' . $sort . '">' . $i . '</a> ';
		} else {
			echo $i . ' ';
		}
	} // End of FOR loop.

	// If it's not the last page, make a Next button:
	if ($current_page != $pages) {
		echo '<a href="index.php?s=' . ($start + $display) . '&p=' . $pages . '&sort=' . $sort . '">Next</a>';
	}

	echo '</p>'; // Close the paragraph.

} // End of links section.

?>