<?php
include 'config.php';

// Pagination setup
$limit = 5; // entries per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM entries WHERE name LIKE '%$search%' OR email LIKE '%$search%' ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Total entries for pagination
$sql_total = "SELECT COUNT(*) as total FROM entries WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
$total_result = $conn->query($sql_total);
$total_row = $total_result->fetch_assoc();
$total_entries = $total_row['total'];
$total_pages = ceil($total_entries / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>View Appointments - Doctor Appointment App</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(to right, #e0f7fa, #e1f5fe);
    min-height: 100vh;
    padding: 30px;
}
table {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 8px 15px rgba(0,0,0,0.2);
}
</style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">All Appointments</h2>

    <!-- Search Form -->
    <form method="GET" class="mb-3 d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by Name or Email" 
        value="<?php if(isset($_GET['search'])) echo $_GET['search']; ?>">

        <button type="submit" class="btn btn-primary me-2">
            <i class="bi bi-search"></i> Search
        </button>

        <a href="view.php" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Reset
        </a>
    </form>

    <!-- Entries Table -->
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['created_at']}</td>
                    <td>
                        <a href='update.php?id={$row['id']}' class='btn btn-sm btn-warning me-1'>
                            <i class='bi bi-pencil-square'></i> Edit
                        </a>
                        <a href='delete.php?id={$row['id']}' class='btn btn-sm btn-danger' 
                           onclick=\"return confirm('Are you sure you want to delete this entry?')\">
                            <i class='bi bi-trash'></i> Delete
                        </a>
                    </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No entries found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination pagination-sm justify-content-center">
            <?php if($page > 1): ?>
            <li class="page-item">
                <a class="page-link rounded-pill" href="?page=1&search=<?php echo $search; ?>">First</a>
            </li>
            <li class="page-item">
                <a class="page-link rounded-pill" href="?page=<?php echo $page-1; ?>&search=<?php echo $search; ?>">Previous</a>
            </li>
            <?php endif; ?>

            <?php for($i=1; $i<=$total_pages; $i++): ?>
            <li class="page-item <?php if($i==$page) echo 'active'; ?>">
                <a class="page-link rounded-pill" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link rounded-pill" href="?page=<?php echo $page+1; ?>&search=<?php echo $search; ?>">Next</a>
            </li>
            <li class="page-item">
                <a class="page-link rounded-pill" href="?page=<?php echo $total_pages; ?>&search=<?php echo $search; ?>">Last</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
