<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "member";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection
    failed: " . mysqli_connect_error());
}
// update functionality
$update_id = "";
$edit_branch = "";

if (isset($_REQUEST['update_id'])) {
    $update_id = $_REQUEST['update_id'];
    $query = " SELECT * FROM branch WHERE id='" . $update_id . "'";
    $result = $conn->query($query);
    if ($result) {
        foreach ($result as $row) {
            $update_id = $row['id'];
            $edit_branch = $row['branch_name'];
        }
    }
}

// Create branch table if not exists
$sqlCreateBranch = " CREATE TABLE IF NOT EXISTS branch ( id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, branch_name
    VARCHAR(255) NOT NULL )";
if (mysqli_query($conn, $sqlCreateBranch)) { // Check if the table was actually created or it already existed 
    if (mysqli_affected_rows($conn) > 0) {
        echo "Branch table created successfully.<br>";
    }
} else {
    echo "Error creating branch table: " . mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Branch Entry Form</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Enter Branch Details</h1>
    <form method="POST" class="form w-100 text-center">
        <div class="form-group">
            <label for="branch-name">Branch Name:</label>
            <input
                value="<?php echo isset($_POST['branch_name']) ? htmlspecialchars($_POST['branch_name']) : $edit_branch; ?>"
                type="text" id="branch-name" name="branch_name" class="form-control">

            <div class="branchname-error">
                <?php
                // Assuming you're submitting data to this same PHP script
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['branch_name'])) {
                    // Validate brand name
                    $branchName = mysqli_real_escape_string($conn, $_POST['branch_name']);

                    if (empty($branchName)) {
                        echo "<div class='mt-5 alert alert-danger' role='alert'>Branch name is required</div>";
                    } else if (preg_match('/\d/', $branchName)) {
                        echo "<div class='alert alert-danger mt-5' role='alert'>Branch name cannot contain numbers</div>";
                    } else {
                        // Check if we are updating or inserting
                        if (!empty($update_id)) {
                            // Prepare an update statement
                            $sqlUpdate = "UPDATE branch SET branch_name = ? WHERE id = ?";
                            $stmt = mysqli_prepare($conn, $sqlUpdate);
                            mysqli_stmt_bind_param($stmt, "si", $branchName, $update_id);

                            if (mysqli_stmt_execute($stmt)) {
                                echo "<p class='text-bg-success p-2 mt-4'>Record updated successfully.</p><br>";
                            } else {
                                echo "Error: " . $sqlUpdate . "<br>" . mysqli_error($conn);
                            }
                        } else {
                            // Prepare an insert statement
                            $sqlInsert = "INSERT INTO branch (branch_name) VALUES (?)";
                            $stmt = mysqli_prepare($conn, $sqlInsert);
                            mysqli_stmt_bind_param($stmt, "s", $branchName);

                            if (mysqli_stmt_execute($stmt)) {
                                echo "<p class='text-bg-success p-2 mt-4'>New record created successfully.</p><br>";
                            } else {
                                echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
                            }
                        }
                    }
                }
                // closing the prepared statement and the connection
                if (isset($stmt)) {
                    mysqli_stmt_close($stmt);
                }
                mysqli_close($conn);
                ?>


            </div>
        </div>
        <br>
        <input class="btn btn-primary" type="submit" value="Submit">
        <a class="btn btn-dark" href="branch_table.php">Go to Table</a>
    </form>
</body>

</html>