<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Define ranks in order
$ranks = array(
    'GEN', 'LT GEN', 'MAJ GEN', 'BRIG GEN', 'COL', 'LT COL', 'MAJ', 
    'CAPT', 'LT', '2LT', 'WO I', 'WO II', 'SM', 'SSGT', 'SGT', 'CPL', 'PTE'
);

// Create the FIELD clause for sorting
$rankOrder = "'" . implode("','", $ranks) . "'";

// Pagination setup
$records_per_page = 100;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Total records count
$total_records_query = "SELECT COUNT(*) as count FROM Identification WHERE ranks IN ($rankOrder)";
$total_records_result = mysqli_query($conn, $total_records_query);
$total_records = mysqli_fetch_assoc($total_records_result)['count'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch records with pagination and custom sorting
$sql = "SELECT * FROM Identification 
        WHERE ranks IN ($rankOrder)
        ORDER BY FIELD(ranks, $rankOrder)
        LIMIT ?, ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $offset, $records_per_page);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Ensure page number is within valid range
if ($page < 1) $page = 1;
if ($page > $total_pages) $page = $total_pages;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Records View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }
        .records-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 30px;
        }
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
        .table thead {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .search-container {
            margin-bottom: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
            .table-responsive {
                max-height: none;
                overflow: visible;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="records-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-primary">
                            <i class="bi bi-table me-2"></i>Personal Identification Records
                        </h2>
                        <div class="action-buttons no-print">
                            <button class="btn btn-success" onclick="window.print()">
                                <i class="bi bi-printer me-2"></i>Print
                            </button>
                            <a href="admin.php" class="btn btn-primary">
                                <i class="bi bi-house me-2"></i>Home
                            </a>
                        </div>
                    </div>

                    <div class="search-container no-print">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search records...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="recordsTable">
                            <thead>
                                <tr>
                                    <th>SVC No</th>
                                    <th>Name</th>
                                    <th>Rank</th>
                                    <th>ID No</th>
                                    <th>Unit</th>
                                    <th>LOE</th>
                                    <th class="no-print">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['SVC_No']); ?></td>
                                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['ranks']); ?></td>
                                        <td><?php echo htmlspecialchars($row['ID_No']); ?></td>
                                        <td><?php echo htmlspecialchars($row['unit']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Level_Of_Edication']); ?></td>
                                        <td class="no-print">
                                            <div class="action-buttons">
                                                <a href="view_details.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-info btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="edit.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button onclick="deleteRecord(<?php echo (int)$row['id']; ?>)" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation" class="no-print">
                        <ul class="pagination justify-content-center">
                            <?php if ($total_pages > 1): ?>
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page-1; ?>">&laquo;</a>
                                </li>
                                <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++) { ?>
                                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php } ?>
                                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page+1; ?>">&raquo;</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            let rows = document.querySelectorAll('#recordsTable tbody tr');
            
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Delete record function with fetch API
        function deleteRecord(id) {
            if (confirm('Are you sure you want to delete this record?')) {
                fetch('delete_record.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting record: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting record');
                });
            }
        }
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>