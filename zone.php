<!DOCTYPE html>
<html>

<head>
    <title>Zone Entry Form</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
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
$edit_zone = "";
$edit_branch = "";

if (isset($_REQUEST['update_id'])) {
    $update_id = $_REQUEST['update_id'];
    $query = " SELECT * FROM myZone WHERE id='" . $update_id . "'";
    $result = $conn->query($query);
    if ($result) {
        foreach ($result as $row) {
            $update_id = $row['id'];
            $edit_zone = $row['zone_name'];
            $edit_branch = $row['branch_name'];
        }
    }
}
// Fetch brands data from brands table
$sqlBranch = "SELECT * FROM branch";
$resultBranch = mysqli_query($conn, $sqlBranch);
$branchOptions = "";

if (mysqli_num_rows($resultBranch) > 0) {
    while ($row = mysqli_fetch_assoc($resultBranch)) {
        $branchId = $row['id'];
        $branchName = $row['branch_name'];
        $isSelected = ($edit_branch == $branchName) ? "selected" : "";
        $branchOptions .= "<option value='$branchName' $isSelected>$branchName</option>";
    }
}

// Create myZone table if not exists
$sqlCreateZone = " CREATE TABLE IF NOT EXISTS myZone ( id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, zone_name
    VARCHAR(255) NOT NULL,branch_name VARCHAR(255) NOT NULL )";
if (mysqli_query($conn, $sqlCreateZone)) { // Check if the table was actually created or it already existed 
    if (mysqli_affected_rows($conn) > 0) {
        echo "zone table created successfully.<br>";
    }
} else {
    echo "Error creating branch table: " . mysqli_error($conn);
}
?>

<body>
    <h1>Enter Zone Details</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form w-100 text-center">
        <div class="form-group">
            <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
        </div>
        <div class="form-group">
            <label for="zone-name">Zone Name:</label>
            <input
                value="<?php echo isset($_POST['zone_name']) ? htmlspecialchars($_POST['zone_name']) : ''; ?><?php echo $edit_zone; ?>"
                type="text" id="zone-name" name="zone_name" class="form-control">
            <?php if (!empty($zoneNameError)): ?>
                <div class="alert alert-danger mt-2"><?php echo $zoneNameError; ?></div>
            <?php endif; ?>
        </div>
        <br>
        <div class="form-group">
            <label for="branch">Branch:</label>
            <select id="branch" name="branch_name" class="form-control">
                <option value="">Select a branch</option>
                <?php echo $branchOptions; ?>
            </select>
            <?php if (!empty($branchError)): ?>
                <div class="alert alert-danger mt-2"><?php echo $branchError; ?></div>
            <?php endif; ?>
        </div>
        <br>
        <input class="btn btn-primary" type="submit" value="Submit">
        <a target="_blank" class="btn btn-dark" href="products-table.php">Go to Table</a>
    </form>