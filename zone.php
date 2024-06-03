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
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize error messages
$zoneNameError = $branchError = "";

// Initialize variables
$update_id = $update_zone_name = $update_branch = "";
$isValid = false;

// Fetch product data for update if update_id is set
if (isset($_GET['update_id'])) {
    $update_id = mysqli_real_escape_string($conn, $_GET['update_id']);
    $query = "SELECT * FROM myzone WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $update_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $update_zone_name = $row['zone_name'];
        $update_branch = $row['branch_name'];
    }
    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $zoneName = mysqli_real_escape_string($conn, $_POST['zone_name']);
    $branchName = mysqli_real_escape_string($conn, $_POST['branch_name']);
    $isValid = true;

    // Validate zone name
    if (empty($zoneName)) {
        $zoneNameError = "Zone name is required.";
        $isValid = false;
    } elseif (!preg_match("/^[a-zA-Z0-9 \-_]+$/", $zoneName)) {
        $zoneNameError = "Zone name contains invalid characters.";
        $isValid = false;
    }

    // Validate branch name
    if (empty($branchName)) {
        $branchError = "Branch name is required.";
        $isValid = false;
    }

    if ($isValid) {
        if (!empty($update_id)) {
            // Perform an update operation
            $stmt = mysqli_prepare($conn, "UPDATE myzone SET zone_name=?, branch_name=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssi", $zoneName, $branchName, $update_id);
        } else {
            // Perform an insert operation
            $stmt = mysqli_prepare($conn, "INSERT INTO myzone (zone_name, branch_name) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $zoneName, $branchName);
        }

        if (mysqli_stmt_execute($stmt)) {
            $message = !empty($update_id) ? 'Record updated successfully.' : 'New record created successfully.';
            echo "<script>alert('$message'); window.location.href = 'branch.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch branch data from branch table
$resultBranch = mysqli_query($conn, "SELECT * FROM branch");
$branchOptions = "";

if (mysqli_num_rows($resultBranch) > 0) {
    while ($row = mysqli_fetch_assoc($resultBranch)) {
        $branchId = $row['id'];
        $branchName = $row['branch_name'];
        $isSelected = ($update_branch === $branchName) ? "selected" : "";
        $branchOptions .= "<option value='$branchName' $isSelected>$branchName</option>";
    }
}

mysqli_close($conn);
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
                value="<?php echo isset($_POST['zone_name']) ? htmlspecialchars($_POST['zone_name']) : ''; ?><?php echo $update_zone_name; ?>"
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
    </form>