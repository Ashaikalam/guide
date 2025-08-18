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

// --- Handle POST requests for Guide Management ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Add New Guide
    if ($action === 'add_guide') {
        $title = mysqli_real_escape_string($db, $_POST['title']);
        $description = mysqli_real_escape_string($db, $_POST['description']);
        
        $sql = "INSERT INTO guides (title, description) VALUES (?, ?)";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $title, $description);
        mysqli_stmt_execute($stmt);
        $guide_id = mysqli_insert_id($db);

        if (isset($_POST['steps']) && is_array($_POST['steps'])) {
            $steps = $_POST['steps'];
            $step_sql = "INSERT INTO steps (guide_id, step_number, title, description, image_url, notes) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_step = mysqli_prepare($db, $step_sql);
            foreach ($steps as $index => $step) {
                $step_number = $index + 1;
                $step_title = mysqli_real_escape_string($db, $step['title']);
                $step_desc = mysqli_real_escape_string($db, $step['description']);
                $image_url = mysqli_real_escape_string($db, $step['image_url']);
                $notes = mysqli_real_escape_string($db, $step['notes']);
                mysqli_stmt_bind_param($stmt_step, 'iissss', $guide_id, $step_number, $step_title, $step_desc, $image_url, $notes);
                mysqli_stmt_execute($stmt_step);
            }
            mysqli_stmt_close($stmt_step);
        }
        mysqli_stmt_close($stmt);
        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>Guide '{$title}' added successfully!</div>";
    }

    // Edit Guide (Placeholder - You'll implement this later)
    if ($action === 'edit_guide') {
        $guide_id = $_POST['guide_id'];
        $title = mysqli_real_escape_string($db, $_POST['title']);
        $description = mysqli_real_escape_string($db, $_POST['description']);
        // Add logic to update guide and its steps in the database
        $message = "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4'>Edit guide functionality to be implemented for Guide ID: {$guide_id}.</div>";
    }

    // Delete Guide (Placeholder - You'll implement this later)
    if ($action === 'delete_guide') {
        $guide_id = $_POST['guide_id'];
        // Add logic to delete guide and its steps from the database
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Delete guide functionality to be implemented for Guide ID: {$guide_id}.</div>";
    }
}

// Function to get all guides with their steps for display
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

$guides = get_guides($db);

mysqli_close($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Management - SAP Knowledge Hub</title>
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
        .modal-content.large-modal {
            max-width: 800px; /* Larger max-width for guide modal */
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
                    <a href="users.php" class="px-3 py-1 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition duration-300">Manage Users</a>
                    <a href="index.php?logout=true" class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-300">Logout</a>
                </div>
            </nav>
        </header>

        <main class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Guide Management Panel</h1>

            <?php echo $message; // Display messages ?>

            <div class="mb-6">
                <button onclick="openModal('add-guide-modal')" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 transition duration-300">
                    Add New Guide
                </button>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Steps Count</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($guides as $guide): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($guide['title']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars(substr($guide['description'], 0, 70)) . (strlen($guide['description']) > 70 ? '...' : ''); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo count($guide['steps']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="openEditGuideModal(<?php echo htmlspecialchars(json_encode($guide)); ?>)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($guide['title']); ?>?');">
                                    <input type="hidden" name="action" value="delete_guide">
                                    <input type="hidden" name="guide_id" value="<?php echo htmlspecialchars($guide['guide_id']); ?>">
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

    <!-- Add Guide Modal -->
    <div id="add-guide-modal" class="modal">
        <div class="modal-content large-modal">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Add New Guide</h2>
            <form action="guides.php" method="POST">
                <input type="hidden" name="action" value="add_guide">
                <div class="mb-4">
                    <label for="guide_title" class="block text-gray-700 font-medium mb-1">Guide Title</label>
                    <input type="text" id="guide_title" name="title" class="w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="guide_description" class="block text-gray-700 font-medium mb-1">Description</label>
                    <textarea id="guide_description" name="description" rows="3" class="w-full px-3 py-2 border rounded-md" required></textarea>
                </div>
                <div id="steps-container" class="space-y-4"></div>
                <button type="button" onclick="addStep()" class="mt-4 px-4 py-2 bg-gray-200 rounded-md">Add Step</button>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="closeModal('add-guide-modal')" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Guide</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Guide Modal (Placeholder) -->
    <div id="edit-guide-modal" class="modal">
        <div class="modal-content large-modal">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Edit Guide</h2>
            <form action="guides.php" method="POST">
                <input type="hidden" name="action" value="edit_guide">
                <input type="hidden" name="guide_id" id="edit_guide_id">
                <div class="mb-4">
                    <label for="edit_guide_title" class="block text-gray-700 font-medium mb-1">Guide Title</label>
                    <input type="text" id="edit_guide_title" name="title" class="w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="edit_guide_description" class="block text-gray-700 font-medium mb-1">Description</label>
                    <textarea id="edit_guide_description" name="description" rows="3" class="w-full px-3 py-2 border rounded-md" required></textarea>
                </div>
                <!-- Steps for editing will go here, similar to add_guide but pre-populated -->
                <div id="edit-steps-container" class="space-y-4"></div>
                <button type="button" onclick="addEditStep()" class="mt-4 px-4 py-2 bg-gray-200 rounded-md">Add Step</button>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="closeModal('edit-guide-modal')" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
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

        // JS for adding/removing steps in Add Guide modal
        function addStep() {
            const container = document.getElementById('steps-container');
            const stepIndex = container.children.length;
            const stepDiv = document.createElement('div');
            stepDiv.classList.add('border', 'p-4', 'rounded-md', 'space-y-2');
            stepDiv.innerHTML = `
                <h4 class="font-semibold">Step ${stepIndex + 1}</h4>
                <div class="flex items-center space-x-2">
                    <label class="block text-gray-700">Title:</label>
                    <input type="text" name="steps[${stepIndex}][title]" class="px-2 py-1 border rounded-md w-full" required>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="block text-gray-700">Description:</label>
                    <textarea name="steps[${stepIndex}][description]" rows="2" class="px-2 py-1 border rounded-md w-full" required></textarea>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="block text-gray-700">Image URL:</label>
                    <input type="text" name="steps[${stepIndex}][image_url]" class="px-2 py-1 border rounded-md w-full">
                </div>
                <div class="flex items-center space-x-2">
                    <label class="block text-gray-700">Notes:</label>
                    <textarea name="steps[${stepIndex}][notes]" rows="2" class="px-2 py-1 border rounded-md w-full"></textarea>
                </div>
                <button type="button" onclick="removeStep(this)" class="text-red-500 text-sm">Remove Step</button>
            `;
            container.appendChild(stepDiv);
        }

        function removeStep(button) {
            button.parentElement.remove();
        }

        // JS for adding/removing steps in Edit Guide modal (placeholder)
        function addEditStep() {
            // This function would be similar to addStep but for the edit modal
            // It would need to manage existing steps and newly added ones.
            alert("Add step functionality for editing guides is not yet fully implemented. You would need to dynamically add input fields for new steps here.");
        }

        function openEditGuideModal(guide) {
            document.getElementById('edit_guide_id').value = guide.guide_id;
            document.getElementById('edit_guide_title').value = guide.title;
            document.getElementById('edit_guide_description').value = guide.description;

            const editStepsContainer = document.getElementById('edit-steps-container');
            editStepsContainer.innerHTML = ''; // Clear previous steps

            // Populate existing steps
            guide.steps.forEach((step, index) => {
                const stepDiv = document.createElement('div');
                stepDiv.classList.add('border', 'p-4', 'rounded-md', 'space-y-2');
                stepDiv.innerHTML = `
                    <h4 class="font-semibold">Step ${index + 1}</h4>
                    <input type="hidden" name="steps[${index}][step_id]" value="${step.step_id || ''}">
                    <div class="flex items-center space-x-2">
                        <label class="block text-gray-700">Title:</label>
                        <input type="text" name="steps[${index}][title]" class="px-2 py-1 border rounded-md w-full" value="${step.title || ''}" required>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="block text-gray-700">Description:</label>
                        <textarea name="steps[${index}][description]" rows="2" class="px-2 py-1 border rounded-md w-full" required>${step.description || ''}</textarea>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="block text-gray-700">Image URL:</label>
                        <input type="text" name="steps[${index}][image_url]" class="px-2 py-1 border rounded-md w-full" value="${step.image_url || ''}">
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="block text-gray-700">Notes:</label>
                        <textarea name="steps[${index}][notes]" rows="2" class="px-2 py-1 border rounded-md w-full">${step.notes || ''}</textarea>
                    </div>
                    <button type="button" onclick="removeStep(this)" class="text-red-500 text-sm">Remove Step</button>
                `;
                editStepsContainer.appendChild(stepDiv);
            });

            openModal('edit-guide-modal');
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
