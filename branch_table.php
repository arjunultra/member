<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "member";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check Connection
if (!$conn) {
    die("Connection Failed:" . mysqli_connect_error());
}
// Getting Data from branch table in member
$sql = "SELECT * FROM branch";
$result = mysqli_query($conn, $sql);




?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Branch Form Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h2>Branch Form Data</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark bg-primary">
                    <tr>
                        <th>ID</th>
                        <th>Branch Name</th>
                        <th class="text-center">Function</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) == !empty($result)) {
                        // Fetch all data at once and store it in an associative array
                        $allRows = mysqli_fetch_all($result, MYSQLI_ASSOC);

                        // Iterate through each row using a foreach loop
                        foreach ($allRows as $row) { ?>
                            <tr>
                                <td><?php echo $row["id"] ?></td>
                                <td><?php echo $row["branch_name"] ?></td>
                                <td class='d-flex'> <a target="_blank" class="btn btn-outline-primary w-50 me-2"
                                        href="branch.php?update_id=<?php echo $row['id']; ?>">UPDATE</a>
                                    <a class="btn btn-danger w-50"
                                        href="branch_table.php?delete_id=<?php echo $row['id']; ?>">DELETE</a>
                                </td>
                            </tr>
                        <?php }
                    } else {
                        echo "<tr><td class='bg-danger text-light text-center fw-bold h1' colspan='5'>No results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php
            // Delete functionality
            if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
                $delete_id = $_GET['delete_id'];
                $sql = "DELETE FROM branch WHERE id=$delete_id";
                if (mysqli_query($conn, $sql)) {
                    echo ("<h5 class='d-inline-block p-2 text-center text-danger fw-bold border border-danger'>Record Deleted Successfully</h2>");
                } else {
                    echo "Error deleting record: " . mysqli_error($conn);
                }
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
    <?php
    mysqli_close($conn);
    ?>
</body>

</html>