<?php
session_start();

// Database connection details
$db = mysqli_connect('localhost', 'root', '', 'guide');

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// User login, registration, and logout logic
$login_error = '';
$registration_success = '';
$registration_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $password = $_POST['password'];

        $sql = "SELECT user_id, email, password_hash, is_admin FROM users WHERE email = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['is_admin'] = $user['is_admin'];
                header("Location: index.php");
                exit();
            } else {
                $login_error = "Invalid email or password.";
            }
        } else {
            $login_error = "Invalid email or password.";
        }
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$is_logged_in = isset($_SESSION['user_id']);
$is_admin = $is_logged_in && $_SESSION['is_admin'];

// Function to get all guides with their steps
function get_guides($db) {
    $guides = [];
    $sql = "SELECT guide_id, title, description FROM guides ORDER BY title ASC";
    $result = mysqli_query($db, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $guide_id = $row['guide_id'];
            $steps = [];
            $steps_sql = "SELECT step_number, title, description, image_url, notes FROM steps WHERE guide_id = ? ORDER BY step_number ASC";
            $stmt = mysqli_prepare($db, $steps_sql);
            mysqli_stmt_bind_param($stmt, 'i', $guide_id);
            mysqli_stmt_execute($stmt);
            $steps_result = mysqli_stmt_get_result($stmt);
            while ($step_row = mysqli_fetch_assoc($steps_result)) {
                $steps[] = $step_row;
            }
            $row['steps'] = $steps;
            $guides[] = $row;
        }
    }
    return $guides;
}

$guides = $is_logged_in ? get_guides($db) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAP Knowledge Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
        .hero-gradient { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body>
    <div id="app" class="min-h-screen bg-gray-50 text-gray-800">
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="https://i.ibb.co/LdQyYj0/logo.png" alt="Logo" class="h-8 w-8">
                    <span class="text-xl font-bold text-gray-900">SAP Knowledge Hub</span>
                </div>
                <?php if ($is_logged_in): ?>
                    <div class="flex items-center space-x-4">
                        <?php if ($is_admin): ?>
                            <a href="guides.php" class="px-3 py-1 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition duration-300">Manage Guides</a>
                            <a href="users.php" class="px-3 py-1 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition duration-300">Manage Users</a>
                        <?php endif; ?>
                        <span class="text-gray-600">Welcome, User</span>
                        <a href="index.php?logout=true" class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-300">Logout</a>
                    </div>
                <?php endif; ?>
            </nav>
        </header>

        <main class="container mx-auto px-4 py-8">
            <?php if ($is_logged_in): ?>
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900">Welcome to the SAP Knowledge Hub</h1>
                    <p class="text-gray-600 mt-2">Your central source for guides and tutorials.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($guides as $guide): ?>
                        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 card-hover cursor-pointer"
                            onclick="window.location.href='index.php?guide_id=<?php echo htmlspecialchars($guide['guide_id']); ?>'">
                            <h2 class="text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($guide['title']); ?></h2>
                            <p class="text-gray-500 mt-2 text-sm"><?php echo htmlspecialchars($guide['description']); ?></p>
                            <div class="flex items-center mt-4 text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12a1 1 0 102 0V9a1 1 0 10-2 0v3zm2-5a1 1 0 10-2 0h2zm-1 9a1 1 0 100-2 1 1 0 000 2z"/>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-5-8a5 5 0 1110 0 5 5 0 01-10 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Steps: <?php echo count($guide['steps']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (isset($_GET['guide_id'])): 
                    $selected_guide_id = mysqli_real_escape_string($db, $_GET['guide_id']);
                    $selected_guide = null;
                    foreach ($guides as $guide) {
                        if ($guide['guide_id'] == $selected_guide_id) {
                            $selected_guide = $guide;
                            break;
                        }
                    }
                ?>
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-screen overflow-y-auto p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($selected_guide['title']); ?></h2>
                            <button onclick="window.location.href='index.php'"
                                    class="text-gray-500 hover:text-gray-700 transition duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-6">
                            <?php foreach ($selected_guide['steps'] as $index => $step): ?>
                                <div class="bg-gray-100 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Step <?php echo $index + 1; ?>: <?php echo htmlspecialchars($step['title']); ?></h3>
                                    <?php if (!empty($step['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($step['image_url']); ?>" alt="Image for step <?php echo $index + 1; ?>" class="w-full h-auto rounded-md mt-4">
                                    <?php endif; ?>
                                    <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($step['description']); ?></p>
                                    <?php if (!empty($step['notes'])): ?>
                                        <p class="text-sm text-gray-500 mt-2">Note: <?php echo htmlspecialchars($step['notes']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Login Form (publicly accessible) -->
                <div class="flex items-center justify-center min-h-screen -mt-24">
                    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
                        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
                        <?php if ($login_error): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <span class="block sm:inline"><?php echo $login_error; ?></span>
                            </div>
                        <?php endif; ?>
                        <form action="index.php" method="POST">
                            <div class="mb-4">
                                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                                <input type="email" name="email" id="email" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div class="mb-6">
                                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                                <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus->ring-blue-500" required>
                            </div>
                            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-300">Login</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
    <script>
        // No admin specific JS needed on index.php anymore
    </script>
</body>
</html>
