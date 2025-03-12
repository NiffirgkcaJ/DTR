<?php
    session_start();
    require 'db.php'; // Use your existing database connection

    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit();
    }

    // Fetch users
    $sql = "SELECT * FROM users ORDER BY user_id ASC";
    $result = $conn->query($sql);

    // Fetch admins
    $admin_sql = "SELECT id, username FROM admin ORDER BY id ASC";
    $admin_result = $conn->query($admin_sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.8">
        <!-- css links -->
        <link href="css/bootstrap-5.3.3.css" rel="stylesheet">
        <link href="css/global.css" rel="stylesheet">
        <!-- js links -->
        <script src="js/bootstrap-5.3.3.js"></script>
        <script src="js/script.js"></script>
        <!-- image links -->
        <link rel="preload" href="images/arrow-both.svg" as="image">
        <link rel="preload" href="images/arrow-up.svg" as="image">
        <link rel="preload" href="images/arrow-down.svg" as="image">
        <title>Admin Dashboard</title>
        <!-- styles -->
        <style>
            @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 600;
            font-display: swap;
            src: url('fonts/Poppins-SemiBold.ttf') format('truetype');
            }

            body {
                font-family: 'Poppins', sans-serif !important;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Admin Dashboard</a>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>
        <div class="container my-5">
            <h1 class="text-center">Admin Panel</h1>
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#users">Users</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#attendance">Attendance Logs</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#settings">Admin Settings</a></li>
            </ul>
            <div class="tab-content">
                <!-- Manage Users -->
                <div id="users" class="container tab-pane fade show active"><br>
                    <h3>Manage Users</h3>
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
                    <table class="table table-bordered table-striped">
                        <colgroup>
                            <col style="width: 15%;"> <!-- User ID -->
                            <col style="width: 55%;"> <!-- Full Name -->
                            <col style="width: 15%;"> <!-- Floor Assigned -->
                            <col style="width: 15%;"> <!-- Actions -->
                        </colgroup>
                        <thead>
                            <tr>
                                <th onclick="sortUsers('user_id')">User ID <span id="user_id-arrow-users"><img src="images/arrow-both.svg" alt="Sort" width="auto" height="15px"></span></th>
                                <th onclick="sortUsers('full_name')">Full Name <span id="full_name-arrow-users"><img src="images/arrow-both.svg" alt="Sort" width="auto" height="15px"></span></th>
                                <th onclick="sortUsers('floor_assigned')">Floor Assigned <span id="floor_assigned-arrow-users"><img src="images/arrow-both.svg" alt="Sort" width="auto" height="15px"></span></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTable">
                            <?php while ($user = $result->fetch_assoc()): ?>
                                <tr data-user-id="<?= htmlspecialchars($user['user_id']) ?>">
                                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                                    <td class="editable truncate-text" data-field="full_name"><?= htmlspecialchars($user['full_name']) ?></td>
                                    <td class="editable truncate-text" data-field="floor_assigned"><?= htmlspecialchars($user['floor_assigned']) ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm edit-btn">Edit</button>
                                        <button class="btn btn-success btn-sm save-btn d-none">Save</button>
                                        <button class="btn btn-danger btn-sm deleteUserBtn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteUserModal" 
                                                data-user-id="<?= htmlspecialchars($user['user_id']) ?>" 
                                                data-full-name="<?= htmlspecialchars($user['full_name']) ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Attendance Logs -->
                <div id="attendance" class="container tab-pane fade"><br>
                    <h3>Attendance Logs</h3>
                    <!-- Filter Options -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" id="searchUser" class="form-control" placeholder="Search by User ID or Name">
                        </div>
                        <div class="col-md-3">
                            <input type="number" id="filterYear" class="form-control" placeholder="Enter Year (YYYY)" min="1900" max="2099">
                        </div>
                        <div class="col-md-3">
                            <select id="filterMonth" class="form-control">
                                <option value="">Select Month</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" id="filterDay" class="form-control" placeholder="Enter Day (DD)" min="1" max="31">
                        </div>
                    </div>
                    <!-- Attendance Table -->
                    <table class="table table-bordered table-striped">
                        <colgroup>
                            <col style="width: 15%;"> <!-- User ID -->
                            <col style="width: 55%;"> <!-- Fullname -->
                            <col style="width: 10%;"> <!-- Date -->
                            <col style="width: 10%;"> <!-- Time In -->
                            <col style="width: 10%;"> <!-- Time Out -->
                        </colgroup>
                        <thead>
                            <tr>
                                <th onclick="sortTable('user_id')">User ID <span id="user_id-arrow-attendance"><img src="images/arrow-both.svg" alt="Double Arrow" width="auto" height="15px"></span></th>
                                <th onclick="sortTable('full_name')">Full Name <span id="full_name-arrow-attendance"><img src="images/arrow-both.svg" alt="Double Arrow" width="auto" height="15px"></span></th>
                                <th onclick="sortTable('date_record')">Date <span id="date_record-arrow-attendance"><img src="images/arrow-both.svg" alt="Double Arrow" width="auto" height="15px"></span></th>
                                <th onclick="sortTable('time_in')">Time In <span id="time_in-arrow-attendance"><img src="images/arrow-both.svg" alt="Double Arrow" width="auto" height="15px"></span></th>
                                <th onclick="sortTable('time_out')">Time Out <span id="time_out-arrow-attendance"><img src="images/arrow-both.svg" alt="Double Arrow" width="auto" height="15px"></span></th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTable">
                            <!-- Attendance records will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <!-- Admin Settings (Placeholder) -->
                <div id="settings" class="container tab-pane fade"><br>
                    <h3>Admin Settings</h3>
                    <!-- Search Bar -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" id="searchAdmin" class="form-control" placeholder="Search by Admin ID or Username">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerAdminModal">Add New Admin</button>
                        </div>
                    </div>
                    <!-- Admin Table -->
                    <table class="table table-bordered table-striped">
                        <colgroup>
                            <col style="width: 15%;"> <!-- Admin ID -->
                            <col style="width: 70%;"> <!-- Username -->
                            <col style="width: 15%;"> <!-- Actions -->
                        </colgroup>
                        <thead>
                            <tr>
                                <th onclick="sortAdminTable('id')">Admin ID <span id="id-arrow"><img src="images/arrow-both.svg" alt="Sort" width="auto" height="15px"></span></th>
                                <th onclick="sortAdminTable('username')">Username <span id="username-arrow"><img src="images/arrow-both.svg" alt="Sort" width="auto" height="15px"></span></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="adminTable">
                            <?php 
                                // Fetch total admin count
                                $admin_count_sql = "SELECT COUNT(*) AS total FROM admin";
                                $admin_count_result = $conn->query($admin_count_sql);
                                $admin_count = $admin_count_result->fetch_assoc()['total'];

                                while ($admin = $admin_result->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($admin['id']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                    <td>
                                        <?php if ($admin['id'] == $_SESSION['admin_id']): ?>
                                            <button class="btn btn-secondary btn-sm" disabled>Logged In</button>
                                        <?php elseif ($admin_count > 1): ?>
                                            <button class="btn btn-danger btn-sm deleteAdminBtn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteAdminModal" 
                                                    data-admin-id="<?= htmlspecialchars($admin['id']) ?>" 
                                                    data-username="<?= htmlspecialchars($admin['username']) ?>">
                                                Delete
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-danger btn-sm" disabled>Cannot Delete Last Admin</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Add Admin Modal -->
        <div class="modal fade" id="registerAdminModal" tabindex="-1" aria-labelledby="registerAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerAdminModalLabel">Register New Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="admin_register.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Admin Modal -->
        <div class="modal fade" id="deleteAdminModal" tabindex="-1" aria-labelledby="deleteAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAdminModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <strong id="adminToDelete"></strong>?
                    </div>
                    <div class="modal-footer">
                        <form action="admin_delete.php" method="POST">
                            <input type="hidden" name="delete_admin_id" id="deleteAdminId">
                            <button type="submit" class="btn btn-danger">Delete</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="user_add.php" method="POST">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User ID</label>
                                <input type="text" class="form-control" name="user_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="floor_assigned" class="form-label">Floor Assigned</label>
                                <input type="text" class="form-control" name="floor_assigned" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete User Modal -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this user?
                    </div>
                    <div class="modal-footer">
                        <form action="user_delete.php" method="POST">
                            <input type="hidden" name="delete_user_id" id="deleteUserId">
                            <button type="submit" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-user-id="<?= htmlspecialchars($user['user_id']) ?>" data-full-name="<?= htmlspecialchars($user['full_name']) ?>">Delete</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // User Section
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll(".edit-btn").forEach(button => {
                    button.addEventListener("click", function() {
                        let row = this.closest("tr");
                        let saveButton = row.querySelector(".save-btn");
                        let editButton = this;

                        // Enable content editing
                        row.querySelectorAll(".editable").forEach(cell => {
                            cell.contentEditable = true;
                            cell.dataset.originalValue = cell.innerText.trim(); // Store original
                            cell.classList.add("table-warning"); // Highlight edits
                        });

                        // Toggle buttons
                        saveButton.classList.remove("d-none");
                        editButton.classList.add("d-none");
                    });
                });

                document.querySelectorAll(".save-btn").forEach(button => {
                    button.addEventListener("click", function() {
                        let row = this.closest("tr");
                        let userId = row.getAttribute("data-user-id");
                        let editButton = row.querySelector(".edit-btn");
                        let saveButton = this;
                        let changes = [];

                        row.querySelectorAll(".editable").forEach(cell => {
                            let field = cell.getAttribute("data-field");
                            let newValue = cell.innerText.trim();
                            let originalValue = cell.dataset.originalValue;

                            if (newValue !== originalValue) {
                                changes.push({ field, value: newValue });
                            }

                            cell.contentEditable = false;
                            cell.classList.remove("table-warning");
                        });

                        // If no changes, just reset UI
                        if (changes.length === 0) {
                            saveButton.classList.add("d-none");
                            editButton.classList.remove("d-none");
                            return;
                        }

                        // Send updates via fetch
                        let params = new URLSearchParams();
                        params.append("user_id", userId);
                        changes.forEach(change => {
                            params.append("field", change.field);
                            params.append("value", change.value);
                        });

                        fetch("user_edit.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: params.toString()
                        }).then(response => response.text())
                        .then(data => {
                            if (data.trim() === "Success") {
                                changes.forEach(change => {
                                    row.querySelector(`[data-field="${change.field}"]`).dataset.originalValue = change.value;
                                });
                            } else {
                                alert("Update failed.");
                            }
                        });

                        // Toggle buttons
                        saveButton.classList.add("d-none");
                        editButton.classList.remove("d-none");
                    });
                });

                // Delete User Modal Handling
                var deleteUserModal = document.getElementById('deleteUserModal');
                deleteUserModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var userId = button.getAttribute('data-user-id');
                var fullName = button.getAttribute('data-full-name'); // Get full_name

                // Update modal text
                var modalBody = deleteUserModal.querySelector('.modal-body');
                modalBody.textContent = "Are you sure you want to delete " + fullName + "?";

                document.getElementById('deleteUserId').value = userId;
                });

                loadAttendance();
            });

            // Attendance Section
            function loadAttendance() {
                fetch("fetch_attendance.php")
                    .then(response => response.json())
                    .then(data => {
                        window.attendanceData = data; // Store the full data set
                        displayAttendance(data);
                    })
                    .catch(error => console.error("Error fetching attendance:", error));
            }

            function displayAttendance(data) {
                let tableBody = document.getElementById("attendanceTable");
                tableBody.innerHTML = "";

                data.forEach(record => {
                    let row = `<tr>
                        <td>${record.user_id}</td>
                        <td>${record.full_name}</td>
                        <td>${record.date_record}</td>
                        <td>${record.time_in}</td>
                        <td>${record.time_out}</td>
                    </tr>`;
                    tableBody.innerHTML += row;
                });
            }

            function filterAttendance() {
                let searchUser = document.getElementById("searchUser").value.toLowerCase().trim();
                let filterYear = document.getElementById("filterYear").value.trim();
                let filterMonth = document.getElementById("filterMonth").value.trim();
                let filterDay = document.getElementById("filterDay").value.trim();

                let filteredData = window.attendanceData.filter(record => {
                    let matchesUser = record.user_id.toLowerCase().includes(searchUser) || record.full_name.toLowerCase().includes(searchUser);
                    let dateParts = record.date_record.split("-"); // Split into [YYYY, MM, DD]

                    let matchesYear = filterYear ? dateParts[0] === filterYear : true;
                    let matchesMonth = filterMonth ? dateParts[1] === filterMonth.padStart(2, '0') : true;
                    let matchesDay = filterDay ? dateParts[2] === filterDay.padStart(2, '0') : true;

                    return matchesUser && matchesYear && matchesMonth && matchesDay;
                });

                displayAttendance(filteredData);
            }

            // Trigger search & filter in real-time
            document.getElementById("searchUser").addEventListener("input", filterAttendance);
            document.getElementById("filterYear").addEventListener("input", filterAttendance);
            document.getElementById("filterMonth").addEventListener("change", filterAttendance);
            document.getElementById("filterDay").addEventListener("input", filterAttendance);

            // Admin Section
            document.getElementById('searchAdmin').addEventListener('keyup', function() {
                let searchValue = this.value.toLowerCase();
                let rows = document.querySelectorAll('#adminTable tr');

                rows.forEach(row => {
                    let adminID = row.cells[0].textContent.toLowerCase();
                    let username = row.cells[1].textContent.toLowerCase();

                    if (adminID.includes(searchValue) || username.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            document.querySelectorAll('.deleteAdminBtn').forEach(button => {
                button.addEventListener('click', function() {
                    let adminId = this.getAttribute('data-admin-id');
                    let username = this.getAttribute('data-username');

                    document.getElementById('deleteAdminId').value = adminId;
                    document.getElementById('adminToDelete').textContent = username;
                });
            });

            // Sorting
            // Manage Users
            let userSort = { column: null, order: "asc" };
            let userData = [];

            // Load user data into array when DOM is ready
            document.addEventListener("DOMContentLoaded", function () {
                loadUserData();
            });

            function loadUserData() {
                let rows = document.querySelectorAll("#usersTable tr");
                userData = Array.from(rows).map(row => ({
                    row: row,
                    user_id: row.cells[0].textContent.trim(), // User ID text
                    full_name: row.cells[1].textContent.trim().toLowerCase(),
                    floor_assigned: row.cells[2].textContent.trim().toLowerCase(),
                }));
            }

            function sortUsers(column) {
                if (userSort.column === column) {
                    userSort.order = userSort.order === "asc" ? "desc" : "asc";
                } else {
                    userSort.column = column;
                    userSort.order = "asc";
                }

                userData.sort((a, b) => {
                    let valA = a[column];
                    let valB = b[column];

                    // Convert user_id to numeric for proper sorting if applicable
                    if (column === "user_id") {
                        valA = isNaN(valA) ? valA : parseInt(valA, 10);
                        valB = isNaN(valB) ? valB : parseInt(valB, 10);
                    }

                    if (valA < valB) return userSort.order === "asc" ? -1 : 1;
                    if (valA > valB) return userSort.order === "asc" ? 1 : -1;
                    return 0;
                });

                updateUserSortingArrows(column);
                displayUsers();
            }

            function updateUserSortingArrows(column) {
                // Reset all sorting icons for the Users table
                document.querySelectorAll("th span[id$='-arrow-users']").forEach(span => {
                    span.innerHTML = '<img src="images/arrow-both.svg" alt="Sort" width="auto" height="15px">';
                });

                let arrow = userSort.order === "asc"
                    ? '<img src="images/arrow-up.svg" alt="Up Arrow" width="auto" height="15px">'
                    : '<img src="images/arrow-down.svg" alt="Down Arrow" width="auto" height="15px">';

                document.querySelector(`#${column}-arrow-users`).innerHTML = arrow;
            }

            function displayUsers() {
                let tbody = document.getElementById("usersTable");
                tbody.innerHTML = "";
                userData.forEach(user => tbody.appendChild(user.row));
            }

            // Reload user data in case new rows are added dynamically
            function refreshUserData() {
                loadUserData();
            }

            // Attendance Logs
            let currentSort = { column: null, order: "asc" };

            function sortTable(column) {
                if (currentSort.column === column) {
                    currentSort.order = currentSort.order === "asc" ? "desc" : "asc";
                } else {
                    currentSort.column = column;
                    currentSort.order = "asc";
                }

                let sortedData = [...window.attendanceData].sort((a, b) => {
                    let valA = a[column];
                    let valB = b[column];

                    // Convert date/time fields for proper sorting
                    if (column === "date_record") {
                        valA = new Date(valA);
                        valB = new Date(valB);
                    } else if (column === "time_in" || column === "time_out") {
                        valA = valA ? new Date("1970-01-01 " + valA) : new Date("1970-01-01 00:00:00");
                        valB = valB ? new Date("1970-01-01 " + valB) : new Date("1970-01-01 00:00:00");
                    } else {
                        valA = valA.toLowerCase();
                        valB = valB.toLowerCase();
                    }

                    if (valA < valB) return currentSort.order === "asc" ? -1 : 1;
                    if (valA > valB) return currentSort.order === "asc" ? 1 : -1;
                    return 0;
                });

                updateSortingArrows(column);
                displayAttendance(sortedData);
            }

            function updateSortingArrows(column) {
                // Reset all sorting icons for the Attendance table
                document.querySelectorAll("th span[id$='-arrow-attendance']").forEach(span => {
                    span.innerHTML = '<img src="images/arrow-both.svg" alt="Double Arrow" width="auto" height="15px">';
                });

                let arrow = currentSort.order === "asc"
                    ? '<img src="images/arrow-up.svg" alt="Up Arrow" width="auto" height="15px">'
                    : '<img src="images/arrow-down.svg" alt="Down Arrow" width="auto" height="15px">';

                document.querySelector(`#${column}-arrow-attendance`).innerHTML = arrow;
            }

            // Admin Settings
            let currentAdminSort = { column: null, order: "asc" };

            function sortAdminTable(column) {
                if (currentAdminSort.column === column) {
                    currentAdminSort.order = currentAdminSort.order === "asc" ? "desc" : "asc";
                } else {
                    currentAdminSort.column = column;
                    currentAdminSort.order = "asc";
                }

                let table = document.getElementById("adminTable");
                let rows = Array.from(table.querySelectorAll("tr"));

                rows.sort((a, b) => {
                    let valA = a.cells[column === 'id' ? 0 : 1].textContent.trim();
                    let valB = b.cells[column === 'id' ? 0 : 1].textContent.trim();

                    if (column === "id") { // Sort numerically
                        valA = parseInt(valA);
                        valB = parseInt(valB);
                    } else { // Sort alphabetically
                        valA = valA.toLowerCase();
                        valB = valB.toLowerCase();
                    }

                    if (valA < valB) return currentAdminSort.order === "asc" ? -1 : 1;
                    if (valA > valB) return currentAdminSort.order === "asc" ? 1 : -1;
                    return 0;
                });

                // Append sorted rows back into the table
                rows.forEach(row => table.appendChild(row));

                updateAdminSortingArrows(column);
            }

            function updateAdminSortingArrows(column) {
                document.querySelectorAll("th span").forEach(span => {
                    span.innerHTML = '<img src="images/arrow-both.svg" alt="Sort" width="auto" height="15px">';
                });

                let arrow = currentAdminSort.order === "asc" 
                    ? '<img src="images/arrow-up.svg" alt="Up Arrow" width="auto" height="15px">' 
                    : '<img src="images/arrow-down.svg" alt="Down Arrow" width="auto" height="15px">';

                document.getElementById(column + "-arrow").innerHTML = arrow;
            }
        </script>
    </body>
</html>
