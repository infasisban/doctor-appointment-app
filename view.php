<?php
include 'config.php';

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Search filter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Sorting setup
$valid_columns = ['id', 'name', 'email', 'phone', 'created_at'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $valid_columns) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

// Main query
$sql = "SELECT * FROM entries 
        WHERE name LIKE '%$search%' OR email LIKE '%$search%' 
        ORDER BY $sort $order 
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Total entries for pagination
$sql_total = "SELECT COUNT(*) as total FROM entries 
              WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
$total_result = $conn->query($sql_total);
$total_row = $total_result->fetch_assoc();
$total_entries = $total_row['total'];
$total_pages = ceil($total_entries / $limit);

// Toggle order for next click
$next_order = ($order === 'ASC') ? 'desc' : 'asc';
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
    padding: 20px;
}
table {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 8px 15px rgba(0,0,0,0.2);
}
tr.table-row:hover {
    background-color: #d1f7d1 !important;
    transition: 0.3s;
}
.sort-icon {
    font-size: 12px;
    margin-left: 5px;
    opacity: 0.7;
}
</style>
</head>
<body>
<div class="container-fluid mt-3">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Doctor Appointment App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="view.php">View</a></li>
                    <li class="nav-item"><a class="nav-link" href="form.php">Form</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <h2 class="text-center mb-4">All Appointments</h2>

    <!-- Search Form -->
    <form method="GET" class="mb-3 d-flex flex-wrap">
        <input type="text" name="search" class="form-control me-2 mb-2" placeholder="Search by Name or Email" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary me-2 mb-2"><i class="bi bi-search"></i> Search</button>
        <a href="view.php" class="btn btn-secondary mb-2"><i class="bi bi-x-circle"></i> Reset</a>
    </form>

    <!-- Responsive Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="entriesTable">
            <thead class="table-dark">
                <tr>
                    <?php foreach ($valid_columns as $col): ?>
                        <th>
                            <a href="?page=<?php echo $page; ?>&search=<?php echo $search; ?>&sort=<?php echo $col; ?>&order=<?php echo $next_order; ?>" class="text-white text-decoration-none">
                                <?php echo ucfirst($col); ?>
                                <?php if ($sort === $col): ?>
                                    <i class="bi bi-caret-<?php echo strtolower($order) === 'asc' ? 'up' : 'down'; ?>-fill sort-icon"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                    <?php endforeach; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="table-row">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>

                            <!-- Delete Button triggers modal -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $row['id']; ?>">
                                <i class="bi bi-trash"></i> Delete
                            </button>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete <strong><?php echo htmlspecialchars($row['name']); ?></strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Yes, Delete</a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">No entries found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination pagination-sm justify-content-center">
            <?php if($page > 1): ?>
                <li class="page-item">
                    <a class="page-link rounded-pill" href="?page=1&search=<?php echo $search; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">First</a>
                </li>
                <li class="page-item">
                    <a class="page-link rounded-pill" href="?page=<?php echo $page-1; ?>&search=<?php echo $search; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <li class="page-item <?php if($i==$page) echo 'active'; ?>">
                    <a class="page-link rounded-pill" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link rounded-pill" href="?page=<?php echo $page+1; ?>&search=<?php echo $search; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">Next</a>
                </li>
                <li class="page-item">
                    <a class="page-link rounded-pill" href="?page=<?php echo $total_pages; ?>&search=<?php echo $search; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">Last</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
