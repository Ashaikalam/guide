<?php
session_start();

// Database connection details
$db = mysqli_connect('localhost', 'root', '', 'guide');

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Security: Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== 1) {
    header("Location: index.php");
    exit();
}

$message = ''; // For displaying success/error messages

// --- Handle POST requests for User Management ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Add New User
    if ($action === 'add_user') {
        $name = mysqli_real_escape_string($db, $_POST['name']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $password = $_POST['password'];
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        // Check if email already exists
        $check_sql = "SELECT user_id FROM users WHERE email = ?";
        $check_stmt = mysqli_prepare($db, $check_sql);
        mysqli_stmt_bind_param($check_stmt, 's', $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error: This email is already registered.</div>";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (name, email, password_hash, is_admin) VALUES (?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($db, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, 'sssi', $name, $email, $password_hash, $is_admin);

            if (mysqli_stmt_execute($insert_stmt)) {
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>User '{$name}' added successfully!</div>";
            } else {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error: Failed to add user. " . mysqli_error($db) . "</div>";
            }
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($check_stmt);
    }

    // Edit User
    if ($action === 'edit_user') {
        $user_id = $_POST['user_id'];
        $name = mysqli_real_escape_string($db, $_POST['name']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        // Prevent admin from revoking their own admin status
        if ($user_id == $_SESSION['user_id'] && $is_admin === 0) {
             $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error: You cannot demote your own admin account.</div>";
        } else {
            $sql = "UPDATE users SET name = ?, email = ?, is_admin = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, 'ssii', $name, $email, $is_admin, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>User details updated successfully.</div>";
            } else {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error updating user: " . mysqli_error($db) . "</div>";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Delete User
    if ($action === 'delete_user') {
        $user_id = $_POST['user_id'];
        
        // Prevent admin from deleting themselves
        if ($user_id == $_SESSION['user_id']) {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error: You cannot delete your own admin account.</div>";
        } else {
            $sql = "DELETE FROM users WHERE user_id = ?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $user_id);
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>User deleted successfully.</div>";
                } else {
                    $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error: User not found.</div>";
                }
            } else {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error deleting user: " . mysqli_error($db) . "</div>";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Fetch all users for display
$users = [];
$sql = "SELECT user_id, name, email, is_admin FROM users ORDER BY name ASC";
$result = mysqli_query($db, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

mysqli_close($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - SAP Knowledge Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 100; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 90%;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <div class="min-h-screen bg-gray-50 text-gray-800">
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="https://i.ibb.co/LdQyYj0/logo.png" alt="Logo" class="h-8 w-8">
                    <span class="text-xl font-bold text-gray-900">SAP Knowledge Hub</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300">Back to Guides</a>
                    <a href="guides.php" class="px-3 py-1 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition duration-300">Manage Guides</a>
                    <a href="index.php?logout=true" class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-300">Logout</a>
                </div>
            </nav>
        </header>

        <main class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">User Management Panel</h1>

            <?php echo $message; // Display messages ?>

            <div class="mb-6">
                <button onclick="openModal('add-user-modal')" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 transition duration-300">
                    Add New User
                </button>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $user['is_admin'] ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'; ?>">
                                    <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($user['name']); ?>?');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modals -->

    <!-- Add User Modal -->
    <div id="add-user-modal" class="modal">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-6 text-center">Add New User</h2>
            <form action="users.php" method="POST">
                <input type="hidden" name="action" value="add_user">
                <div class="mb-4">
                    <label for="add_name" class="block text-gray-700 font-bold mb-2">Name</label>
                    <input type="text" id="add_name" name="name" class="w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="add_email" class="block text-gray-700 font-bold mb-2">Email</label>
                    <input type="email" id="add_email" name="email" class="w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="add_password" class="block text-gray-700 font-bold mb-2">Password</label>
                    <input type="password" id="add_password" name="password" class="w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-6 flex items-center">
                    <input type="checkbox" id="add_is_admin" name="is_admin" class="mr-2">
                    <label for="add_is_admin" class="text-gray-700">Is Admin?</label>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal('add-user-modal')" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="edit-user-modal" class="modal">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-6 text-center">Edit User</h2>
            <form action="users.php" method="POST">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="mb-4">
                    <label for="edit_name" class="block text-gray-700 font-bold mb-2">Name</label>
                    <input type="text" id="edit_name" name="name" class="w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="edit_email" class="block text-gray-700 font-bold mb-2">Email</label>
                    <input type="email" id="edit_email" name="email" class="w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-6 flex items-center">
                    <input type="checkbox" id="edit_is_admin" name="is_admin" class="mr-2">
                    <label for="edit_is_admin" class="text-gray-700">Is Admin?</label>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal('edit-user-modal')" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function openEditModal(user) {
            document.getElementById('edit_user_id').value = user.user_id;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_is_admin').checked = user.is_admin === 1; // Checkbox for admin status
            openModal('edit-user-modal');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
