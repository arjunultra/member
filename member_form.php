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
$nameError = $addressError = $mobileError = $branchError = $zoneNameError = "";

// Initialize variables for update
$update_id = $update_name = $update_address = $update_mobile = $update_branch = $update_zone_name = "";
$isValid = false;

// Fetch member data for update if update_id is set
if (isset($_GET['update_id'])) {
    $update_id = mysqli_real_escape_string($conn, $_GET['update_id']);
    $query = "SELECT * FROM memberform WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $update_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $update_name = $row['member_name'];
        $update_address = $row['member_address'];
        $update_mobile = $row['mobile_number']; // Corrected column name from 'member_mobile' to 'mobile_number'
        $update_zone_name = $row['member_zone']; // Corrected column name from 'zone_name' to 'member_zone'
        $update_branch = $row['branch_name'];
    }
    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $memberName = mysqli_real_escape_string($conn, $_POST['member_name']);
    $memberAddress = mysqli_real_escape_string($conn, $_POST['member_address']);
    $memberMobile = mysqli_real_escape_string($conn, $_POST['member_mobile']);
    $zoneName = mysqli_real_escape_string($conn, $_POST['zone_name']);
    $branchName = mysqli_real_escape_string($conn, $_POST['branch_name']);
    $isValid = true;

    // Create Table
    $sqlCreateMember = "CREATE TABLE IF NOT EXISTS memberform (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        member_name VARCHAR(255) NOT NULL,
        member_address VARCHAR(255) NOT NULL,
        mobile_number BIGINT(20) NOT NULL,
        branch_name VARCHAR(255) NOT NULL,
        member_zone VARCHAR(255) NOT NULL
    )";
    if (!mysqli_query($conn, $sqlCreateMember)) {
        echo "Error creating members form table: " . mysqli_error($conn);
    }

    // Validate member name
    if (empty($memberName)) {
        $nameError = "Member name is required.";
        $isValid = false;
    } elseif (!preg_match("/^[a-zA-Z0-9 \-_]+$/", $memberName)) {
        $nameError = "Member name contains invalid characters."; // Corrected variable name from $zoneNameError to $nameError
        $isValid = false;
    }

    // Validate member address
    if (empty($memberAddress)) {
        $addressError = "Member address is required.";
        $isValid = false;
    }

    // Validate member mobile
    if (empty($memberMobile)) {
        $mobileError = "Member mobile number is required.";
        $isValid = false;
    } elseif (!preg_match("/^\d{10}$/", $memberMobile)) {
        $mobileError = "Invalid mobile number format.";
        $isValid = false;
    }

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
            $stmt = mysqli_prepare($conn, "UPDATE memberform SET member_name=?, member_address=?, mobile_number=?, branch_name=?, member_zone=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssssi", $memberName, $memberAddress, $memberMobile, $branchName, $zoneName, $update_id);
        } else {
            // Perform an insert operation
            $stmt = mysqli_prepare($conn, "INSERT INTO memberform (member_name, member_address, mobile_number, branch_name, member_zone) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $memberName, $memberAddress, $memberMobile, $branchName, $zoneName);
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
// Fetch zone data from myzone table
$resultBranch = mysqli_query($conn, "SELECT * FROM myzone");
$zoneOptions = "";

if (mysqli_num_rows($resultBranch) > 0) {
    while ($row = mysqli_fetch_assoc($resultBranch)) {
        $zoneId = $row['id'];
        $zoneName = $row['zone_name'];
        $isSelected = ($update_zone_name === $zoneName) ? "selected" : "";
        $zoneOptions .= "<option value='$zoneName' $isSelected>$zoneName</option>";
    }
}

mysqli_close($conn);
?>


<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label for="member_name">Member Name:</label>
            <input class="form-control" type="text" id="member_name" name="member_name"
                value="<?php echo $update_name; ?>">
            <span class="text-bg-danger"><?php echo $nameError; ?></span>
        </div>
        <div>
            <label for="member_address">Address:</label>
            <input class="form-control" type="text" id="member_address" name="member_address"
                value="<?php echo $update_address; ?>">
            <span class="text-bg-danger"><?php echo $addressError; ?></span>
        </div>
        <div>
            <label for="member_mobile">Mobile:</label>
            <input class="form-control" type="tel" id="member_mobile" name="member_mobile" pattern="\d{10}"
                value="<?php echo $update_mobile; ?>">
            <span class="text-bg-danger"><?php echo $mobileError; ?></span>
        </div>
        <div>
            <label for="branch_name">Branch:</label>
            <select id="branch_name" name="branch_name">
                <option value="" selected>Select a Branch</option>
                <?php echo $branchOptions; ?>
            </select>
            <span class="text-bg-danger"><?php echo $branchError; ?></span>
        </div>
        <div>
            <label for="zone_name">Zone:</label>
            <select id="zone_name" name="zone_name">
                <option value="" selected>Select a zone</option>
                <?php echo $zoneOptions; ?>
                <!-- Dynamically populated zones go here -->
            </select>
            <span class="text-bg-danger"><?php echo $zoneNameError; ?></span>
        </div>
        <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
        <button class="btn btn-primary mt-5" type="submit">Submit</button>
    </form>
</body>

</html>