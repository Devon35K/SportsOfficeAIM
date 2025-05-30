    <?php
    // Start the session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

    // Check if user is logged in
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: ../view/loginView.php");
            exit;
        }

    // Check session timeout (30 minutes)
        $session_timeout = 1800;
        if (isset($_SESSION['user']['last_activity']) && (time() - $_SESSION['user']['last_activity'] > $session_timeout)) {
            session_unset();
            session_destroy();
            header("Location: ../view/loginView.php?timeout=1");
            exit;
        }

    // Update last activity
        $_SESSION['user']['last_activity'] = time();

    // OPTIONAL: Debug year_section value
        if (!isset($_SESSION['submissions']['year_section'])) {
            error_log("DEBUG: year_section not set in session.");
        } else {
            error_log("DEBUG: year_section = " . $_SESSION['submissions']['year_section']);
        }


    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: ../view/loginView.php");
        exit;
    }

    // Check session timeout (30 minutes)
    $session_timeout = 1800; // 30 minutes in seconds
    if (isset($_SESSION['user']['last_activity']) && (time() - $_SESSION['user']['last_activity'] > $session_timeout)) {
        // Session expired
        session_unset();
        session_destroy();
        header("Location: ../view/loginView.php?timeout=1");
        exit;
    }

    // Update last activity time
    $_SESSION['user']['last_activity'] = time();
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons/css/boxicons.min.css" rel="stylesheet">
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <link rel="stylesheet" href="../public/CSS/userStyle.css">
    <script src="../public/JAVASCRIPT/userScript.js" defer></script>
    <link rel="icon" href="../public/image/Usep.png" sizes="any">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="flex h-screen w-full relative bg-gray-100">















<!-- Sidebar -->
<div id="sidebar" class="sidebar collapsed"> <!-- Add collapsed class by default -->
    <div class="flex flex-col items-center mt-6 space-y-4">
        <img src="../public/image/SportOffice.png" alt="Logo" class="w-20 h-20">
        <div class="text-center text-xs leading-tight">
            <p class="font-semibold">One Data. One USeP.</p>
            <p>USeP OSAS-Sports Unit</p>
        </div>

        <nav class="space-y-2 w-full px-2 mt-4">
            <?php

            // Get current page from query parameter with security validation

            $currentPage = isset($_GET['page']) ? $_GET['page'] : 'Dashboard';
            $menu = ['Dashboard', 'Achievement','Submissions', 'Track', 'Log-out'];
            $icon = [
                'Dashboard' => "<box-icon type='solid' name='Dashboard' color='white'></box-icon>",
                'Achievement' => "<box-icon name='trophy' type='solid' color='white'></box-icon>",
                'Submissions' => "<box-icon type='solid' name='file-export' color='white'></box-icon>",
                'Track' => "<box-icon type='solid' name='file' color='white'></box-icon>",
                'Log-out' => "<box-icon name='log-out' color='white'></box-icon>"
            ];

            foreach ($menu as $item) {
                $isLogout = $item === 'Log-out';
                $isActive = $item === $currentPage;
                $class = $isActive ? 'menu-item active-menu' : 'menu-item';
                $idAttr = $isLogout ? "id='logoutBtn' href='#'" : "href='?page=$item'";

                echo "<a $idAttr class='$class' data-title='$item'>
                        <span class='menu-icon'>{$icon[$item]}</span>
                        <span class='menu-text'>$item</span>
                    </a>";
            }
            ?>
        </nav>
    </div>


    <!-- Collapse Button - Hidden on mobile -->
    <div class="w-full px-2 mb-4 mt-auto hidden md:block"> <!-- Add hidden md:block -->
        <button id="collapseBtn" class="menu-item w-full focus:outline-none">
            <box-icon id="collapseBoxIcon" name='expand-horizontal' color='white'></box-icon>
            <span class="menu-text">Collapse Sidebar</span>
        </button>
    </div>
</div>























<!-- Main Content -->
<div id="mainContent" class="main-content px-1 sm:px-4 lg:px-0">

    <div class="sticky top-0 z-30 bg-gray-100 w-full px-1 sm:px-4 lg:px-3">

            <div class="sticky top-0 z-30 bg-gray-100 w-full px-1 sm:px-4 lg:px-3">
            <div class="border-b-4 border-red-500 px-5 pt-2 pb-1 flex justify-between items-center">
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 tracking-tight">
                    <?php echo htmlspecialchars($currentPage); ?>
                </h1>

                <?php if ($currentPage === 'Dashboard'): ?>
                <?php elseif ($currentPage === 'Achievement'): ?>
                <?php elseif ($currentPage === 'Submissions'): ?>
                <?php elseif ($currentPage === 'Track'): ?>
                <?php endif; ?>
        </div>
        </div>




        <?php if ($currentPage === 'Dashboard'): ?>
            <div class="flex flex-col md:flex-row gap-4 p-4">
                <!-- Left Column -->
                <div class="w-full md:w-1/2 space-y-4">
                    <!-- Profile Info Card -->
                    <div class="bg-white rounded-lg shadow p-6 relative">
                        <div class="flex justify-between items-start mb-6">
                            <h2 class="text-xl font-bold">Profile</h2>
                            <button class="text-gray-500 hover:text-blue-500 transition" onclick="document.getElementById('edit-profile-modal').classList.remove('hidden')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex flex-col sm:flex-row items-start gap-6">
                            <div class="flex-shrink-0 relative group">
                                <div class="w-32 h-32 rounded-full bg-gray-200 overflow-hidden border-4 border-blue-100">
                                    <?php if ($_SESSION['user']['has_profile_image']): ?>
                                        <img src="../controller/get_profile_image.php?id=<?php echo $_SESSION['user']['id']; ?>"
                                             onerror="this.onerror=null; this.src='../public/image/user.png'"
                                             alt="Profile Image" class="profile-image">
                                    <?php else: ?>
                                        <div class="profile-initials">
                                            <?php
                                            $name = $_SESSION['user']['full_name'];
                                            $initials = '';
                                            $parts = explode(' ', $name);
                                            if (count($parts) > 1) {
                                                $initials = strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
                                            } else {
                                                $initials = strtoupper(substr($name, 0, 1));
                                            }
                                            echo $initials;
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="space-y-4 flex-1">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Student ID </p>
                                        <p class="font-semibold"><?php echo htmlspecialchars($_SESSION['user']['student_id'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Full Name</p>
                                        <p class="font-semibold">
                                            <?php
                                            echo htmlspecialchars(
                                                ($_SESSION['user']['full_name'] ?? 'N/A'));
                                            ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Email</p>
                                        <p class="font-semibold"><?php echo htmlspecialchars($_SESSION['user']['email'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                             </div>
                            </div>
                        </div>


































                    <!-- Track Card -->
                    <div class="bg-white rounded-lg shadow p-6 relative">
                        <!-- Edit button positioned top right -->
                        <button class="absolute top-4 right-4 text-gray-500 hover:text-blue-500 transition"
                                onclick="window.location.href='?page=Track'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>

                        <div class="flex justify-between items-start mb-6">
                            <h2 class="text-xl font-bold">Track</h2>
                        </div>

                        <div class="flex items-center justify-center">
                            <div class="mr-6 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="text-center">
                                <?php
                                // Database connection
                                $host = "localhost";
                                $username = "root";
                                $password = "";
                                $dbname = "SportOfficeDB";

                                // Connect to MySQL server
                                $conn = new mysqli($host, $username, $password, $dbname);
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                // Fetch count of submissions for the logged-in user
                                $user_id = $_SESSION['user']['id']; // Assuming user ID is stored in session
                                $stmt = $conn->prepare("SELECT COUNT(*) as document_count FROM submissions WHERE user_id = ?");
                                $stmt->bind_param("i", $user_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $document_count = 0;
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $document_count = $row['document_count'];
                                }

                                // Store document count in session for potential reuse
                                $_SESSION['user']['document_count'] = $document_count;

                                // Clean up
                                $stmt->close();
                                $conn->close();
                                ?>
                                <div class="text-5xl font-bold text-gray-800">
                                    <?php echo htmlspecialchars($document_count); ?>
                                </div>
                                <div class="text-lg text-gray-600 mt-2">Submitted Documents</div>
                            </div>
                        </div>
                    </div>






                </div>






















                <!-- Right Column - Notifications -->
                <!-- Right Column - Notifications -->
                <div class="w-full md:w-1/2 h-[calc(100vh-150px)]">
                    <div class="bg-white rounded-lg shadow h-full flex flex-col">
                        <!-- Header with buttons (fixed height) -->
                        <div class="flex justify-between items-center p-4 border-b">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                </svg>
                                <h2 class="text-xl font-bold">Notifications</h2>
                            </div>
                            <div>
                                <button id="refreshNotifications" class="text-blue-500 hover:text-blue-700 text-sm mr-3">
                                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                                </button>
                                <button id="clearNotifications" class="text-blue-500 hover:text-blue-700 text-sm">
                                    <i class="fas fa-trash-alt mr-1"></i>Clear All
                                </button>
                            </div>
                        </div>

                        <!-- Scrollable notifications container -->
                        <div class="flex-1 overflow-y-auto" style="max-height: calc(100vh - 200px);">
                            <div id="notificationsContainer" class="space-y-3 p-4">
                                <!-- Notifications will be dynamically inserted here -->
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Notification system elements
                        const notificationsContainer = document.getElementById('notificationsContainer');
                        const refreshBtn = document.getElementById('refreshNotifications');
                        const clearBtn = document.getElementById('clearNotifications');

                        // Load notifications from localStorage or initialize
                        let notifications = JSON.parse(localStorage.getItem('notifications')) || [];
                        let timestampUpdaters = {}; // Store interval IDs for timestamp updates

                        // Display notifications from PHP session
                        function displaySessionNotifications() {
                            <?php if (!empty($_SESSION['notifications'])): ?>
                            const sessionNotifications = <?php echo json_encode($_SESSION['notifications']); ?>;

                            sessionNotifications.forEach(notification => {
                                addNotification(notification.message, notification.timestamp, false); // Don't save to localStorage yet
                            });

                            // Save all new notifications to localStorage at once
                            localStorage.setItem('notifications', JSON.stringify(notifications));

                            // Clear notifications from session after displaying
                            <?php unset($_SESSION['notifications']); ?>
                            <?php endif; ?>
                        }

                        // Format time with real-time updates
                        function formatTime(timestamp, element) {
                            const updateTime = () => {
                                const now = new Date();
                                const notificationTime = new Date(timestamp);
                                const diffInSeconds = Math.floor((now - notificationTime) / 1000);

                                if (diffInSeconds < 60) {
                                    element.textContent = 'Just now';
                                } else if (diffInSeconds < 3600) {
                                    const minutes = Math.floor(diffInSeconds / 60);
                                    element.textContent = `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
                                } else if (diffInSeconds < 86400) {
                                    const hours = Math.floor(diffInSeconds / 3600);
                                    element.textContent = `${hours} hour${hours !== 1 ? 's' : ''} ago`;
                                } else {
                                    element.textContent = notificationTime.toLocaleDateString();
                                    // Stop updating if more than 1 day old
                                    clearInterval(timestampUpdaters[timestamp]);
                                    delete timestampUpdaters[timestamp];
                                }
                            };

                            // Initial update
                            updateTime();

                            // Set up interval for updates (every minute for recent notifications)
                            if (!timestampUpdaters[timestamp]) {
                                timestampUpdaters[timestamp] = setInterval(updateTime, 60000);
                            }
                        }

                        // Display notifications in the UI
                        function displayNotifications() {
                            notificationsContainer.innerHTML = '';

                            if (notifications.length === 0) {
                                notificationsContainer.innerHTML = '<p class="text-gray-500 text-center py-4">No notifications</p>';
                                return;
                            }

                            notifications.forEach((notification, index) => {
                                const notificationElement = document.createElement('div');
                                notificationElement.className = 'p-3 bg-gray-50 rounded border-l-4 border-blue-500 flex justify-between items-start';
                                notificationElement.innerHTML = `
                <div>
                    <p class="text-sm">${notification.message}</p>
                    <p class="text-xs text-gray-500 mt-1 timestamp" data-timestamp="${notification.timestamp}"></p>
                </div>
                <button class="text-gray-400 hover:text-gray-600 delete-notification" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            `;
                                notificationsContainer.appendChild(notificationElement);

                                // Initialize timestamp with auto-updating
                                const timestampElement = notificationElement.querySelector('.timestamp');
                                formatTime(notification.timestamp, timestampElement);
                            });

                            // Add event listeners to delete buttons
                            document.querySelectorAll('.delete-notification').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    const index = parseInt(this.getAttribute('data-index'));

                                    // Clear the timestamp updater for this notification
                                    const timestamp = notifications[index].timestamp;
                                    if (timestampUpdaters[timestamp]) {
                                        clearInterval(timestampUpdaters[timestamp]);
                                        delete timestampUpdaters[timestamp];
                                    }

                                    notifications.splice(index, 1);
                                    localStorage.setItem('notifications', JSON.stringify(notifications));
                                    displayNotifications();
                                });
                            });
                        }

                        // Add a new notification
                        function addNotification(message, timestamp = null, saveToStorage = true) {
                            const newTimestamp = timestamp || new Date().toISOString();
                            notifications.unshift({
                                message: message,
                                timestamp: newTimestamp
                            });

                            // Keep only the last 50 notifications
                            if (notifications.length > 50) {
                                const removed = notifications.pop();
                                // Clean up timestamp updater for removed notification
                                if (timestampUpdaters[removed.timestamp]) {
                                    clearInterval(timestampUpdaters[removed.timestamp]);
                                    delete timestampUpdaters[removed.timestamp];
                                }
                            }

                            if (saveToStorage) {
                                localStorage.setItem('notifications', JSON.stringify(notifications));
                            }
                            displayNotifications();
                        }

                        // Clear all notifications
                        clearBtn.addEventListener('click', function() {
                            if (confirm('Are you sure you want to clear all notifications?')) {
                                // Clear all timestamp updaters
                                Object.values(timestampUpdaters).forEach(interval => clearInterval(interval));
                                timestampUpdaters = {};

                                notifications = [];
                                localStorage.setItem('notifications', JSON.stringify(notifications));
                                displayNotifications();
                            }
                        });

                        // Refresh notifications - now fetches from server if needed
                        refreshBtn.addEventListener('click', function() {
                            // Add loading indicator
                            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Refreshing';

                            // Simulate fetching new notifications from server
                            fetch('/api/notifications')
                                .then(response => response.json())
                                .then(newNotifications => {
                                    if (newNotifications && newNotifications.length > 0) {
                                        newNotifications.forEach(notification => {
                                            addNotification(notification.message, notification.timestamp);
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error refreshing notifications:', error);
                                })
                                .finally(() => {
                                    // Restore refresh button
                                    refreshBtn.innerHTML = '<i class="fas fa-sync-alt mr-1"></i>Refresh';
                                });
                        });

                        // Initial setup
                        displaySessionNotifications();
                        displayNotifications();

                        // Listen for custom events (like profile updates)
                        document.addEventListener('profileUpdated', function(e) {
                            const changes = e.detail.changes;
                            let message = 'Profile updated: ';
                            const changesList = [];

                            if (changes.name) changesList.push('name');
                            if (changes.email) changesList.push('email');
                            if (changes.photo) changesList.push('profile photo');
                            if (changes.address) changesList.push('address');
                            if (changes.password) changesList.push('password');

                            if (changesList.length > 0) {
                                message += changesList.join(', ');
                                addNotification(message);
                            }
                        });

                        // Make the function available globally
                        window.addNotification = addNotification;

                        // Clean up intervals when page unloads
                        window.addEventListener('beforeunload', function() {
                            Object.values(timestampUpdaters).forEach(interval => clearInterval(interval));
                        });
                    });
                </script>











































































                <?php elseif ($currentPage === 'Submissions'): ?>
                <!-- Enhanced Submissions Content -->
                <div class="submissions-container">
                    <!-- Header Section with Icon -->
                    <div class="submissions-header">
                        <div class="header-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2>Submit Your Documents</h2>
                        <p>One Data. One USeP. OSAS-Sports Unit</p>
                    </div>

                    <!-- Card Container -->
                    <div class="submissions-card">
                        <!-- Progress Indicator -->
                        <div class="progress-indicator">
                            <div class="progress-steps">
                                <div class="step">
                                    <div class="step-number">1</div>
                                    <p class="step-label">Personal Info</p>
                                </div>
                                <div class="progress-line">
                                    <div class="progress-completed"></div>
                                </div>
                                <div class="step">
                                    <div class="step-number">2</div>
                                    <p class="step-label">Document Details</p>
                                </div>
                                <div class="progress-line">
                                    <div class="progress-completed"></div>
                                </div>
                                <div class="step">
                                    <div class="step-number">3</div>
                                    <p class="step-label">Submit</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form -->
                        <form action="../controller/submit_form.php" method="POST" enctype="multipart/form-data" class="submissions-form">
                            <!-- Section: Personal Information -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Personal Information
                                </h3>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="fullname">Full Name</label>
                                        <input type="text" id="fullname" name="fullname"
                                               placeholder="Enter your full name" required
                                               value="<?php echo isset($_SESSION['user']['full_name']) ? htmlspecialchars($_SESSION['user']['full_name']) : ''; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="year_section">Year & Section</label>
                                        <input type="text" id="year_section" name="year_section"
                                               placeholder="Ex: 1IT - BSIT" required
                                               value="<?php echo htmlspecialchars($_SESSION['submissions']['year_section'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="student_id">Student Id</label>
                                        <input type="text" id="student_id" name="student_id"
                                               placeholder="Enter your student id" required
                                               value="<?php echo htmlspecialchars($_SESSION['user']['student_id'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_email">Email</label>
                                        <input type="email" id="contact_email" name="contact_email"
                                               placeholder="Enter your email" required
                                               value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Document Information -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Document Information
                                </h3>

                                <div class="form-stack">
                                    <div class="form-group">
                                        <label for="document_type">Document Type</label>
                                        <select id="document_type" name="document_type" required>
                                            <option value="" disabled selected>Select document type</option>
                                            <option value="Medical Certificate">Medical Certificate</option>
                                            <option value="Certification">Certification</option>
                                            <option value="Recommendation Letter">Recommendation Letter</option>
                                            <option value="Sports Clearance">Sports Clearance</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>

                                    <div id="other_type_container" class="form-group hidden">
                                        <label for="other_type">Specify Other Document Type</label>
                                        <input type="text" id="other_type" name="other_type" placeholder="Specify document type">
                                    </div>

                                    <div class="form-group">
                                        <label for="uploaded_file">Upload Document</label>
                                        <div class="file-upload-area">
                                            <input type="file" id="uploaded_file" name="uploaded_file" class="hidden" required>
                                            <label for="uploaded_file" class="file-upload-label">
                                                <div class="upload-icon-container">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                </div>
                                                <span class="upload-instruction">Click to upload or drag and drop</span>
                                                <span class="upload-requirements">PDF, DOC, DOCX, JPG, PNG (Max 5MB)</span>
                                            </label>
                                            <div id="file_info" class="file-info hidden">
                                                <span class="file-info-label">Selected file:</span> <span id="file_name"></span>
                                                <button type="button" id="preview_button" class="ml-2 px-2 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600 transition-colors" onclick="previewFile()">Preview</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Description -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                    </svg>
                                    Description
                                </h3>

                                <div class="form-group">
                                    <label for="description">Document Description</label>
                                    <textarea id="description" name="description" placeholder="Provide a brief description of the document" required></textarea>
                                    <p class="form-hint">Please provide any relevant details about your document submission.</p>
                                    <p id="desc-warning" style="color: red; display: none;">Description must be at least 10 characters long.</p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-submit">
                                <button type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Submit Document
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- File Preview Modal -->
                <div id="filePreviewModal" class="modal fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 hidden" role="dialog" aria-labelledby="filePreviewModalTitle" aria-modal="true">
                    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-3xl border border-gray-200 mx-4 flex flex-col">
                        <div class="modal-header relative mb-4">
                            <h2 id="filePreviewModalTitle" class="text-lg font-semibold text-gray-800">Preview File</h2>
                            <button onclick="closeModal('filePreviewModal')" class="modal-close-btn absolute top-0 right-0 text-gray-400 hover:text-gray-600 transition-colors" aria-label="Close modal">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div id="filePreviewContent" class="w-full flex-1 overflow-auto bg-gray-50">
                            <!-- Content will be injected here -->
                        </div>
                        <div class="modal-footer mt-4 flex justify-center gap-3 bg-gray-100 p-4 rounded-b-2xl">
                            <a id="fileDownloadLink" href="#" class="action-btn bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg flex items-center gap-2 transition-colors duration-200 focus:outline-none hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                            <button onclick="closeModal('filePreviewModal')" class="action-btn bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg flex items-center gap-2 transition-colors duration-200 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Close
                            </button>
                        </div>
                    </div>
                </div>

                <style>
                    /* Submissions Page Responsive Styles */
                    .submissions-container {
                        padding: 1rem;
                        max-width: 1200px;
                        margin: 0 auto;
                    }

                    .submissions-header {
                        text-align: center;
                        margin-bottom: 2rem;
                    }

                    .submissions-card {
                        background-color: white;
                        border-radius: 0.75rem;
                        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                        border: 1px solid #e5e7eb;
                        padding: 1.25rem;
                        margin-bottom: 2rem;
                    }

                    /* Progress Indicator */
                    .progress-steps {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        margin-bottom: 2rem;
                    }

                    .step {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        position: relative;
                        z-index: 1;
                        flex: 1;
                    }

                    .step-number {
                        width: 2.5rem;
                        height: 2.5rem;
                        background-color: #ef4444;
                        border-radius: 9999px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: 700;
                        margin-bottom: 0.5rem;
                    }

                    .step-label {
                        font-size: 0.875rem;
                        font-weight: 500;
                        text-align: center;
                        color: #1f2937;
                    }

                    .progress-line {
                        flex: 1;
                        height: 0.25rem;
                        margin: 0 0.5rem;
                        background-color: #fecaca;
                        position: relative;
                    }

                    .progress-completed {
                        height: 100%;
                        width: 100%;
                        background-color: #ef4444;
                    }

                    /* Form Sections */
                    .form-section {
                        background-color: #f9fafb;
                        padding: 1.5rem;
                        border-radius: 0.5rem;
                        margin-bottom: 1.5rem;
                    }

                    .section-title {
                        font-size: 1.125rem;
                        font-weight: 600;
                        color: #1f2937;
                        margin-bottom: 1rem;
                        display: flex;
                        align-items: center;
                    }

                    .section-title svg {
                        height: 1.25rem;
                        width: 1.25rem;
                        color: #ef4444;
                        margin-right: 0.5rem;
                    }

                    /* Form Grid Layout */
                    .form-grid {
                        display: grid;
                        gap: 1rem;
                    }

                    .form-stack {
                        display: flex;
                        flex-direction: column;
                        gap: 1rem;
                    }

                    .form-group {
                        margin-bottom: 1rem;
                    }

                    .form-group label {
                        display: block;
                        font-size: 0.875rem;
                        font-weight: 500;
                        color: #374151;
                        margin-bottom: 0.5rem;
                    }

                    .form-group input,
                    .form-group select,
                    .form-group textarea {
                        width: 100%;
                        border: 1px solid #d1d5db;
                        border-radius: 0.5rem;
                        padding: 0.75rem;
                        font-size: 1rem;
                        transition: all 0.2s ease;
                    }

                    .form-group textarea {
                        min-height: 120px;
                        resize: vertical;
                    }

                    /* File Upload Area */
                    .file-upload-area {
                        border: 2px dashed #d1d5db;
                        border-radius: 0.5rem;
                        padding: 2rem;
                        text-align: center;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    }

                    .file-upload-area:hover {
                        border-color: #ef4444;
                        background-color: #fef2f2;
                    }

                    .file-upload-label {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                    }

                    .file-upload-label svg {
                        height: 2.5rem;
                        width: 2.5rem;
                        color: #9ca3af;
                        margin-bottom: 0.5rem;
                    }

                    .upload-instruction {
                        font-size: 1rem;
                        font-weight: 500;
                        color: #374151;
                        margin-bottom: 0.25rem;
                    }

                    .upload-requirements {
                        font-size: 0.875rem;
                        color: #6b7280;
                    }

                    /* Submit Button */
                    .form-submit {
                        text-align: center;
                        margin-top: 1.5rem;
                    }

                    .form-submit button {
                        background-color: #ef4444;
                        color: white;
                        font-weight: 700;
                        padding: 0.75rem 2rem;
                        border-radius: 0.5rem;
                        font-size: 1rem;
                        border: none;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .form-submit button:hover {
                        background-color: #dc2626;
                    }

                    .form-submit button svg {
                        height: 1.25rem;
                        width: 1.25rem;
                        margin-right: 0.5rem;
                    }

                    /* Utility Classes */
                    .hidden {
                        display: none;
                    }

                    /* Responsive Adjustments */
                    @media (min-width: 768px) {
                        .submissions-container {
                            padding: 2rem;
                        }

                        .submissions-card {
                            padding: 2rem;
                        }

                        .form-grid {
                            grid-template-columns: repeat(2, 1fr);
                        }

                        .file-upload-area {
                            padding: 3rem;
                        }
                    }

                    @media (max-width: 767px) {
                        .progress-steps {
                            flex-wrap: wrap;
                            justify-content: center;
                        }

                        .step {
                            min-width: 80px;
                            margin-bottom: 1rem;
                        }

                        .progress-line {
                            display: none;
                        }

                        .file-upload-area {
                            padding: 1.5rem;
                        }
                    }

                    /* Modal Styles */
                    .modal-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 0.75rem 1rem;
                        border-bottom: 1px solid #e5e7eb;
                    }

                    .modal-close-btn {
                        cursor: pointer;
                    }

                    .modal-footer {
                        padding: 0.75rem;
                        background-color: #f3f4f6;
                        display: flex;
                        justify-content: center;
                        gap: 0.75rem;
                        border-bottom-left-radius: 0.5rem;
                        border-bottom-right-radius: 0.5rem;
                        flex-shrink: 0;
                    }

                    .action-btn {
                        padding: 0.75rem 1.25rem;
                        border-radius: 0.375rem;
                        font-weight: 500;
                        transition: background-color 0.2s;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        border: none;
                        cursor: pointer;
                        font-size: 0.9rem;
                    }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Handle document type "Others" selection
                        const documentTypeSelect = document.getElementById('document_type');
                        const otherTypeContainer = document.getElementById('other_type_container');
                        const otherTypeInput = document.getElementById('other_type');

                        documentTypeSelect.addEventListener('change', function() {
                            if (this.value === 'Others') {
                                otherTypeContainer.classList.remove('hidden');
                                otherTypeInput.setAttribute('required', 'required');
                            } else {
                                otherTypeContainer.classList.add('hidden');
                                otherTypeInput.removeAttribute('required');
                                otherTypeInput.value = '';
                            }
                        });

                        // File upload preview
                        const fileInput = document.getElementById('uploaded_file');
                        const fileInfo = document.getElementById('file_info');
                        const fileName = document.getElementById('file_name');
                        const previewButton = document.getElementById('preview_button');

                        fileInput.addEventListener('change', function() {
                            if (this.files.length > 0) {
                                const file = this.files[0];
                                const fileSize = (file.size / (1024 * 1024)).toFixed(2); // Convert to MB

                                fileName.textContent = `${file.name} (${fileSize} MB)`;
                                fileInfo.classList.remove('hidden');
                                previewButton.classList.remove('hidden');

                                // Validate file size (5MB max)
                                if (file.size > 5 * 1024 * 1024) {
                                    alert('File size exceeds 5MB limit. Please choose a smaller file.');
                                    this.value = ''; // Clear the input
                                    fileInfo.classList.add('hidden');
                                    previewButton.classList.add('hidden');
                                }
                            } else {
                                fileInfo.classList.add('hidden');
                                previewButton.classList.add('hidden');
                            }
                        });

                        // Drag and drop functionality
                        const uploadArea = document.querySelector('.file-upload-area');

                        uploadArea.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            uploadArea.classList.add('drag-over');
                        });

                        uploadArea.addEventListener('dragleave', () => {
                            uploadArea.classList.remove('drag-over');
                        });

                        uploadArea.addEventListener('drop', (e) => {
                            e.preventDefault();
                            uploadArea.classList.remove('drag-over');

                            if (e.dataTransfer.files.length) {
                                fileInput.files = e.dataTransfer.files;
                                const event = new Event('change');
                                fileInput.dispatchEvent(event);
                            }
                        });

                        // Description validation
                        const description = document.getElementById("description");
                        const warning = document.getElementById("desc-warning");

                        description.addEventListener("input", () => {
                            if (description.value.trim().length < 10) {
                                warning.style.display = "block";
                            } else {
                                warning.style.display = "none";
                            }
                        });
                    });

                    // File preview function
                    function previewFile() {
                        const fileInput = document.getElementById('uploaded_file');
                        const modal = document.getElementById('filePreviewModal');
                        const preview = document.getElementById('filePreviewContent');
                        const downloadLink = document.getElementById('fileDownloadLink');

                        if (!fileInput.files.length) {
                            alert('Please select a file first.');
                            return;
                        }

                        const file = fileInput.files[0];
                        const url = URL.createObjectURL(file);

                        // Show loading state
                        preview.innerHTML = `
            <div class="flex items-center justify-center min-h-full py-6">
                <div class="text-center">
                    <svg class="animate-spin h-6 w-6 text-blue-500 mx-auto" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2 text-gray-500 text-sm">Loading file...</p>
                </div>
            </div>
        `;
                        modal.classList.remove('hidden');

                        // Simulate fetch with File API
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const blob = new Blob([e.target.result], { type: file.type || 'application/octet-stream' });
                            const contentType = file.type || 'application/octet-stream';

                            downloadLink.href = url;
                            downloadLink.classList.remove('hidden');

                            if (contentType === 'application/pdf') {
                                preview.innerHTML = `<iframe src="${url}#zoom=auto" style="width:100%; height:100%; max-height:100%;" frameborder="0" title="File Preview" aria-label="PDF file preview"></iframe>`;
                            } else if (contentType.startsWith('image/')) {
                                preview.innerHTML = `<img src="${url}" alt="Uploaded File" class="w-full h-full object-contain" aria-label="Image preview" />`;
                            } else {
                                preview.innerHTML = `
                    <div class="text-center py-6 text-red-500 text-sm">
                        Preview not available for this file type. Please download to view.
                    </div>
                `;
                                downloadLink.classList.remove('hidden');
                            }
                        };
                        reader.onerror = function() {
                            preview.innerHTML = `
                <div class="text-center py-6 text-red-500 text-sm">
                    Error loading file.
                </div>
            `;
                            downloadLink.classList.remove('hidden');
                        };
                        reader.readAsArrayBuffer(file);

                        // Add click event to close modal when clicking outside
                        modal.addEventListener('click', (e) => {
                            if (e.target === modal) {
                                closeModal('filePreviewModal');
                            }
                        }, { once: true });
                    }

                    // Close modal function
                    function closeModal(modalId) {
                        const modal = document.getElementById(modalId);
                        const preview = document.getElementById('filePreviewContent');
                        modal.classList.add('hidden');
                        if (preview) {
                            const iframes = preview.getElementsByTagName('iframe');
                            const images = preview.getElementsByTagName('img');
                            for (let iframe of iframes) {
                                URL.revokeObjectURL(iframe.src);
                            }
                            for (let img of images) {
                                URL.revokeObjectURL(img.src);
                            }
                            preview.innerHTML = '';
                        }
                    }
                </script>














                <?php elseif ($currentPage === 'Track'): ?>
                <!-- Track content -->
                <div class="p-6 bg-gray-100 min-h-screen">
                    <div class="space-y-4">
                        <?php
                        // Database connection (use secure credentials in production, e.g., environment variables)
                        $host = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "SportOfficeDB";

                        // Connect to MySQL server
                        $conn = new mysqli($host, $username, $password, $dbname);
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Fetch submissions for the logged-in user
                        $user_id = $_SESSION['user']['id']; // Assuming user ID is stored in session
                        $stmt = $conn->prepare("
            SELECT id, document_type, submission_date, status, description, file_name, other_type 
            FROM submissions 
            WHERE user_id = ? 
            ORDER BY submission_date DESC
        ");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Fetch user profile image
                        $profile_image_data = null;
                        $profile_image_type = null;
                        $profile_stmt = $conn->prepare("SELECT image, image_type FROM user_images WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 1");
                        $profile_stmt->bind_param("i", $user_id);
                        $profile_stmt->execute();
                        $profile_result = $profile_stmt->get_result();
                        if ($profile_result->num_rows > 0) {
                            $profile_row = $profile_result->fetch_assoc();
                            $profile_image_data = $profile_row['image'];
                            $profile_image_type = $profile_row['image_type'];
                        }
                        $profile_stmt->close();

                        if ($result->num_rows > 0) {
                            while ($doc = $result->fetch_assoc()) {
                                // Format the submission date with fallback
                                $submission_date = $doc['submission_date'] ? date("m-d-Y", strtotime($doc['submission_date'])) : 'N/A';

                                // Map status to Tailwind CSS classes
                                $status_class = match ($doc['status']) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };

                                // Adjust status display text
                                $status_display = match ($doc['status']) {
                                    'pending' => 'Not been Approved',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    default => $doc['status']
                                };
                                ?>
                                <div class="flex items-center bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow">
                                    <!-- File Icon -->
                                    <div class="mr-5">
                                        <svg class="h-12 w-12 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 14H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                        </svg>
                                    </div>
                                    <!-- Document Details -->
                                    <div class="flex-1">
                                        <p class="text-base font-semibold text-gray-900">Document Type: <?php echo htmlspecialchars($doc['document_type']); ?></p>
                                        <p class="text-sm text-gray-600">Date of Submission: <?php echo htmlspecialchars($submission_date); ?></p>
                                    </div>
                                    <!-- Status -->
                                    <div class="ml-5">
                        <span class="inline-block px-4 py-1.5 text-sm font-semibold rounded-full <?php echo $status_class; ?>">
                            <?php echo htmlspecialchars($status_display); ?>
                        </span>
                                    </div>
                                    <!-- Action Buttons -->
                                    <div class="ml-5 flex space-x-3">
                                        <!-- View Button -->
                                        <button onclick="openDocumentModal('<?php echo htmlspecialchars($doc['document_type']); ?>', '<?php echo htmlspecialchars($submission_date); ?>', '<?php echo htmlspecialchars($status_display); ?>', '<?php echo htmlspecialchars($doc['description']); ?>', '<?php echo htmlspecialchars($doc['file_name']); ?>', '<?php echo $doc['id']; ?>')" class="text-gray-500 hover:text-gray-700 transition-colors" aria-label="View document <?php echo htmlspecialchars($doc['document_type']); ?>">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <!-- Edit Button -->
                                        <button onclick="openEditModal('<?php echo $doc['id']; ?>', '<?php echo htmlspecialchars($doc['document_type']); ?>', '<?php echo htmlspecialchars($doc['other_type']); ?>', '<?php echo htmlspecialchars($doc['description']); ?>', '<?php echo htmlspecialchars($doc['file_name']); ?>', '<?php echo $doc['status']; ?>', '<?php echo htmlspecialchars($submission_date); ?>', null)" class="text-gray-500 hover:text-blue-500 transition-colors" aria-label="Edit document <?php echo htmlspecialchars($doc['document_type']); ?>">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p class="text-gray-500 text-center py-6 text-lg">No submissions found.</p>';
                        }

                        // Clean up
                        $stmt->close();
                        $conn->close();
                        ?>
                    </div>
                </div>

                <!-- Edit Submission Modal -->
                <div id="editSubmissionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 hidden">
                    <div class="bg-white rounded-2xl shadow-2xl p-4 w-full max-w-lg border border-gray-200 mx-4">
                        <!-- Header -->
                        <div class="relative mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Edit Submission</h2>
                            <p class="text-xs text-gray-500 mt-1">Update your document details</p>
                            <button onclick="closeModal('editSubmissionModal')" class="absolute top-0 right-0 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Profile Info -->
                        <div class="flex items-center gap-2 mb-4 px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="w-8 h-8 flex-shrink-0 overflow-hidden rounded-full border border-gray-300">
                                <?php if ($profile_image_data && $profile_image_type): ?>
                                    <img src="data:<?php echo htmlspecialchars($profile_image_type); ?>;base64,<?php echo base64_encode($profile_image_data); ?>"
                                         alt="Profile" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <svg class="w-5 h-5 text-gray-400 mx-auto my-1.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-800 truncate"><?php echo htmlspecialchars($_SESSION['user']['full_name']); ?></p>
                                <p class="text-xs text-gray-500 truncate">ID: <?php echo htmlspecialchars($_SESSION['user']['student_id']); ?></p>
                            </div>
                        </div>

                        <!-- Form -->
                        <form id="editSubmissionForm" action="../controller/update_submission.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="submission_id" id="edit_submission_id">

                            <!-- Document Info -->
                            <div class="flex justify-between items-center mb-3 gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-gray-500 whitespace-nowrap">Document Type:</span>
                                    <p id="edit_document_type_display" class="text-xs font-medium text-gray-800 truncate"></p>
                                </div>
                                <div id="edit_status_display" class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 whitespace-nowrap"></div>
                            </div>

                            <div class="w-full max-w-xs mx-auto">
                                <label for="edit_description" class="text-sm font-medium text-gray-700 block text-center">Description</label>
                                <span id="edit_desc_warning" class="text-xs text-red-500 hidden block text-center">Minimum 10 characters</span>
                                <textarea id="edit_description" name="description" rows="4"
                                          class="w-full p-3 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                          style="min-height: 80px; overflow-y: auto;"
                                          required></textarea>
                            </div>

                            <!-- File Upload -->
                            <div class="w-full max-w-xs mx-auto mt-3">
                                <label for="uploaded_file" class="text-sm font-medium text-gray-700 block text-center">Upload New File (Optional)</label>
                                <div class="file-upload-area border border-gray-300 rounded-md p-4 text-center">
                                    <input type="file" id="uploaded_file" name="uploaded_file" class="hidden" accept=".pdf,.doc,.docx,.jpg,.png">
                                    <label for="uploaded_file" class="file-upload-label cursor-pointer">
                                        <div class="upload-icon-container mx-auto w-12 h-12 text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" class="w-full h-full">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                        </div>
                                        <span class="upload-instruction block text-sm text-gray-600">Click to upload or drag and drop</span>
                                        <span class="upload-requirements block text-xs text-gray-500">PDF, DOC, DOCX, JPG, PNG (Max 5MB)</span>
                                    </label>
                                    <div id="file_info" class="file-info hidden mt-2">
                                        <span class="file-info-label text-sm text-gray-600">Selected file:</span> <span id="file_name" class="text-sm text-gray-800"></span>
                                    </div>
                                    <span id="edit_file_warning" class="text-xs text-red-500 hidden block mt-2">Please select a valid file (PDF, DOC, DOCX, JPG, PNG, max 5MB)</span>
                                </div>
                            </div>
                            <div class="w-full max-w-xs mx-auto mt-3">
                                <!-- Action Buttons -->
                                <div id="edit_action_buttons" class="flex flex-wrap gap-2 mt-2 justify-center"></div>
                            </div>
                            <!-- Footer Info -->
                            <div class="flex justify-center items-center border-t border-gray-200 pt-3 w-full">
                                <p class="text-xs text-gray-500 text-center">
                                    <span>Submitted: </span>
                                    <span id="edit_submission_date" class="font-medium text-gray-600"></span>
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                    class="w-full py-2 bg-blue-600 text-white rounded-md text-xs font-semibold hover:bg-blue-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 mt-3">
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- File View Modal -->
                <div id="fileViewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 hidden" role="dialog" aria-labelledby="file-view-title">
                    <div class="modal-content bg-white rounded-2xl shadow-2xl p-6 w-full max-w-3xl border border-gray-200 mx-4 flex flex-col">
                        <div class="modal-header relative mb-4">
                            <h2 id="file-view-title" class="text-lg font-semibold text-gray-800">View File</h2>
                            <button onclick="closeModal('fileViewModal')" class="modal-close-btn absolute top-0 right-0 text-gray-400 hover:text-gray-600 transition-colors" aria-label="Close modal">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div id="fileViewPreview" class="w-full flex-1 overflow-auto bg-gray-50">
                            <!-- Content will be injected here -->
                        </div>
                        <div class="modal-footer mt-4 flex justify-center gap-3 bg-gray-100 p-4 rounded-b-2xl">
                            <a id="fileDownloadLink" href="#" class="action-btn bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg flex items-center gap-2 transition-colors duration-200 focus:outline-none hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                            <button onclick="closeModal('fileViewModal')" class="action-btn bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg flex items-center gap-2 transition-colors duration-200 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Close
                            </button>
                        </div>
                    </div>
                </div>

                <!-- JavaScript for Modal and Form Handling -->
                <script>
                    function openEditModal(submissionId, documentType, otherType, description, fileName, status, submissionDate, approvalDate = null) {
                        // Populate the modal form with existing data
                        document.getElementById('edit_submission_id').value = submissionId;
                        document.getElementById('edit_document_type_display').textContent = documentType === 'Others' && otherType ? otherType : documentType;
                        document.getElementById('edit_description').value = description;
                        document.getElementById('edit_submission_date').textContent = submissionDate;

                        // Reset file input and file info
                        const fileInput = document.getElementById('uploaded_file');
                        const fileInfo = document.getElementById('file_info');
                        const fileNameSpan = document.getElementById('file_name');
                        const fileWarning = document.getElementById('edit_file_warning');
                        fileInput.value = '';
                        fileInfo.classList.add('hidden');
                        fileNameSpan.textContent = '';
                        fileWarning.classList.add('hidden');

                        // Handle status-specific display and buttons
                        const statusDisplay = document.getElementById('edit_status_display');
                        const actionButtons = document.getElementById('edit_action_buttons');
                        const escapedFileName = fileName ? fileName.replace(/'/g, "\\'") : '';

                        // Common "View File" button for all statuses
                        const viewFileButton = fileName ? `
            <button type="button" onclick="openFileViewModal('${submissionId}', '${escapedFileName}')" class="px-2 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600 shadow-sm transition-colors" aria-label="View file for submission">View File</button>
        ` : '';

                        if (status === 'pending') {
                            statusDisplay.innerHTML = `<p class="text-sm font-medium text-yellow-800 bg-yellow-100 px-4 py-2 rounded-full inline-block">Not been Approved</p>`;
                            actionButtons.innerHTML = `
                <button type="button" onclick="deleteSubmission(${submissionId})" class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300 shadow-sm transition-colors">Delete</button>
                ${viewFileButton}
            `;
                        } else if (status === 'approved') {
                            statusDisplay.innerHTML = `<p class="text-sm font-medium text-green-800 bg-green-100 px-4 py-2 rounded-full inline-block">Approved</p>`;
                            actionButtons.innerHTML = `
                <button type="button" onclick="deleteSubmission(${submissionId})" class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300 shadow-sm transition-colors">Delete</button>
                <a href="../controller/download_submission.php?id=${submissionId}" class="px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600 shadow-sm transition-colors" aria-label="Download file">Download</a>
                ${viewFileButton}
            `;
                            if (approvalDate) {
                                statusDisplay.innerHTML += `<p class="text-sm text-gray-600 mt-2">Date of Approval: ${approvalDate}</p>`;
                            }
                        } else if (status === 'rejected') {
                            statusDisplay.innerHTML = `<p class="text-sm font-medium text-red-800 bg-red-100 px-4 py-2 rounded-full inline-block">Rejected</p>`;
                            actionButtons.innerHTML = `
                <button type="button" onclick="deleteSubmission(${submissionId})" class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300 shadow-sm transition-colors">Delete</button>
                <button type="button" onclick="returnToSubmit(${submissionId})" class="px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600 shadow-sm transition-colors">Return to Submit</button>
                ${viewFileButton}
            `;
                        }

                        // Show the modal with smooth transition
                        const modal = document.getElementById('editSubmissionModal');
                        modal.classList.remove('hidden');
                        setTimeout(() => modal.classList.add('opacity-100'), 50);

                        // Validate description length on input
                        const descriptionInput = document.getElementById('edit_description');
                        const descWarning = document.getElementById('edit_desc_warning');
                        descriptionInput.addEventListener('input', () => {
                            if (descriptionInput.value.trim().length < 10) {
                                descWarning.classList.remove('hidden');
                            } else {
                                descWarning.classList.add('hidden');
                            }
                        });

                        // Validate file input on change
                        fileInput.addEventListener('change', () => {
                            const file = fileInput.files[0];
                            fileInfo.classList.add('hidden');
                            fileNameSpan.textContent = '';
                            fileWarning.classList.add('hidden');

                            if (file) {
                                const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
                                const maxSize = 5 * 1024 * 1024; // 5MB
                                if (!validTypes.includes(file.type)) {
                                    fileWarning.textContent = 'Invalid file type. Please select PDF, DOC, DOCX, JPG, or PNG.';
                                    fileWarning.classList.remove('hidden');
                                    fileInput.value = '';
                                } else if (file.size > maxSize) {
                                    fileWarning.textContent = 'File too large. Maximum size is 5MB.';
                                    fileWarning.classList.remove('hidden');
                                    fileInput.value = '';
                                } else {
                                    fileInfo.classList.remove('hidden');
                                    fileNameSpan.textContent = file.name;
                                }
                            }
                        });
                    }

                    function openFileViewModal(submissionId, fileName) {
                        // Prevent modal stacking by closing other modals
                        const existingModals = document.querySelectorAll('.fixed.inset-0');
                        existingModals.forEach(modal => {
                            if (modal.id !== 'fileViewModal') modal.remove();
                        });

                        const modal = document.getElementById('fileViewModal');
                        const preview = document.getElementById('fileViewPreview');
                        const downloadLink = document.getElementById('fileDownloadLink');

                        // Set download link to original file
                        downloadLink.href = `../controller/download_submission.php?id=${encodeURIComponent(submissionId)}&download=true`;
                        downloadLink.classList.remove('hidden');

                        // Show loading state
                        preview.innerHTML = `
            <div class="flex items-center justify-center min-h-full py-6">
                <div class="text-center">
                    <svg class="animate-spin h-6 w-6 text-blue-500 mx-auto" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2 text-gray-500 text-sm">Loading file...</p>
                </div>
            </div>
        `;
                        modal.classList.remove('hidden');
                        setTimeout(() => modal.classList.add('opacity-100'), 50);

                        // Fetch the file, requesting PDF conversion if possible
                        fetch(`../controller/download_submission.php?id=${encodeURIComponent(submissionId)}&convert=pdf`)
                            .then(response => {
                                if (!response.ok) throw new Error(`Failed to load file: ${response.statusText}`);
                                return response.blob();
                            })
                            .then(blob => {
                                const url = URL.createObjectURL(blob);
                                const contentType = blob.type || 'application/octet-stream';
                                const fileExtension = fileName.split('.').pop().toLowerCase();

                                if (contentType === 'application/pdf' || fileExtension === 'pdf') {
                                    preview.innerHTML = `<iframe src="${url}#zoom=auto" style="width:100%; height:100%; max-height:100%;" frameborder="0" title="File Preview" aria-label="PDF file preview"></iframe>`;
                                } else if (contentType.startsWith('image/') || ['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                                    preview.innerHTML = `<img src="${url}" alt="Uploaded File" class="w-full h-full object-contain" aria-label="Image preview" />`;
                                } else {
                                    preview.innerHTML = `
                        <div class="text-center py-6 text-red-500 text-sm">
                            Preview not available for this file type (.${fileExtension}). Please download the original file.
                        </div>
                    `;
                                    downloadLink.classList.remove('hidden');
                                }
                            })
                            .catch(error => {
                                preview.innerHTML = `
                    <div class="text-center py-6 text-red-500 text-sm">
                        Error loading file: ${error.message}. Please download the original file.
                    </div>
                `;
                                downloadLink.classList.remove('hidden');
                            });

                        // Add click event to close modal when clicking outside
                        modal.addEventListener('click', (e) => {
                            if (e.target === modal) {
                                closeModal('fileViewModal');
                            }
                        }, { once: true });
                    }

                    function openDocumentModal(type, date, status, description, fileName, submissionId) {
                        const existingModals = document.querySelectorAll('.fixed.inset-0');
                        existingModals.forEach(modal => modal.remove());

                        const modal = document.createElement('div');
                        modal.className = 'fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50';
                        modal.setAttribute('role', 'dialog');
                        modal.setAttribute('aria-labelledby', 'modal-title');

                        const escapedDescription = description.replace(/</g, '<').replace(/>/g, '>');
                        let filePreview = '';

                        if (fileName) {
                            const fileExtension = fileName.split('.').pop().toLowerCase();
                            const fileUrl = `../controller/download_submission.php?id=${encodeURIComponent(submissionId)}&convert=pdf`;

                            // Show loading state initially
                            filePreview = `
                <div class="flex items-center justify-center min-h-[200px] py-6">
                    <div class="text-center">
                        <svg class="animate-spin h-6 w-6 text-blue-500 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-2 text-gray-500 text-sm">Loading file...</p>
                    </div>
                </div>
                <div id="documentPreview" class="w-full h-64 mt-4"></div>
            `;

                            // Fetch the file and determine how to display it
                            setTimeout(() => {
                                fetch(fileUrl)
                                    .then(response => {
                                        if (!response.ok) throw new Error(`Failed to load file: ${response.statusText}`);
                                        return response.blob();
                                    })
                                    .then(blob => {
                                        const url = URL.createObjectURL(blob);
                                        const contentType = blob.type || 'application/octet-stream';
                                        const previewDiv = modal.querySelector('#documentPreview');

                                        if (contentType === 'application/pdf' || fileExtension === 'pdf') {
                                            previewDiv.innerHTML = `<iframe src="${url}#zoom=auto" class="w-full h-full" title="PDF Preview" aria-label="PDF document preview"></iframe>`;
                                        } else if (contentType.startsWith('image/') || ['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                                            previewDiv.innerHTML = `<img src="${url}" alt="Uploaded File" class="w-full h-full object-contain" aria-label="Image preview">`;
                                        } else {
                                            previewDiv.innerHTML = `
                                <div class="text-center py-6 text-red-500 text-sm">
                                    Preview not available for this file type (.${fileExtension}). Please download the original file.
                                    <a href="../controller/download_submission.php?id=${submissionId}" class="text-blue-600 hover:underline block mt-2" aria-label="Download file">Download</a>
                                </div>
                            `;
                                        }
                                    })
                                    .catch(error => {
                                        const previewDiv = modal.querySelector('#documentPreview');
                                        previewDiv.innerHTML = `
                            <div class="text-center py-6 text-red-500 text-sm">
                                Error loading file preview: ${error.message}. Please download the original file.
                                <a href="../controller/download_submission.php?id=${submissionId}" class="text-blue-600 hover:underline block mt-2" aria-label="Download file">Download</a>
                            </div>
                        `;
                                    });
                            }, 0);
                        } else {
                            filePreview = `<p class="text-sm text-gray-700 mt-4">No file available for preview.</p>`;
                        }

                        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-lg text-center">
                <h2 id="modal-title" class="text-xl font-semibold mb-4 text-gray-900">Document Details</h2>
                <p class="text-sm text-gray-700"><strong>Type:</strong> ${type}</p>
                <p class="text-sm text-gray-700"><strong>Date:</strong> ${date}</p>
                <p class="text-sm text-gray-700"><strong>Status:</strong> ${status}</p>
                <p class="text-sm text-gray-700 break-words"><strong>Description:</strong> ${escapedDescription}</p>
                ${filePreview}
                <button onclick="this.parentElement.parentElement.remove()" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">Close</button>
            </div>
        `;

                        modal.addEventListener('click', (e) => {
                            if (e.target === modal) {
                                modal.remove();
                            }
                        });

                        document.body.appendChild(modal);
                    }

                    function closeModal(modalId) {
                        const modal = document.getElementById(modalId);
                        const preview = modal?.querySelector('#fileViewPreview') || modal?.querySelector('#documentPreview');
                        if (modal) {
                            modal.classList.remove('opacity-100');
                            setTimeout(() => {
                                modal.classList.add('hidden');
                                if (preview) {
                                    const iframes = preview.getElementsByTagName('iframe');
                                    const images = preview.getElementsByTagName('img');
                                    for (let iframe of iframes) {
                                        URL.revokeObjectURL(iframe.src);
                                    }
                                    for (let img of images) {
                                        URL.revokeObjectURL(img.src);
                                    }
                                    preview.innerHTML = '';
                                }
                            }, 300);
                        }
                    }

                    function deleteSubmission(submissionId) {
                        if (confirm('Are you sure you want to delete this submission?')) {
                            fetch('../controller/delete_submission.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: `id=${encodeURIComponent(submissionId)}`
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alert('Submission deleted successfully!');
                                        location.reload();
                                    } else {
                                        alert('Error deleting submission: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred while deleting the submission.');
                                });
                        }
                    }

                    function returnToSubmit(submissionId) {
                        if (confirm('Are you sure you want to return this submission for resubmission?')) {
                            fetch('../controller/return_submission.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: `id=${encodeURIComponent(submissionId)}`
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alert('Submission returned for resubmission!');
                                        location.reload();
                                    } else {
                                        alert('Error returning submission: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred while returning the submission.');
                                });
                        }
                    }

                    document.getElementById('editSubmissionForm').addEventListener('submit', function(e) {
                        e.preventDefault();

                        const description = document.getElementById('edit_description').value;
                        const fileInput = document.getElementById('uploaded_file');
                        const descWarning = document.getElementById('edit_desc_warning');
                        const fileWarning = document.getElementById('edit_file_warning');

                        // Validate description
                        if (description.trim().length < 10) {
                            descWarning.classList.remove('hidden');
                            return;
                        } else {
                            descWarning.classList.add('hidden');
                        }

                        // Validate file if provided
                        if (fileInput.files.length > 0) {
                            const file = fileInput.files[0];
                            const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
                            const maxSize = 5 * 1024 * 1024; // 5MB
                            if (!validTypes.includes(file.type)) {
                                fileWarning.textContent = 'Invalid file type. Please select PDF, DOC, DOCX, JPG, or PNG.';
                                fileWarning.classList.remove('hidden');
                                fileInput.value = '';
                                return;
                            } else if (file.size > maxSize) {
                                fileWarning.textContent = 'File too large. Maximum size is 5MB.';
                                fileWarning.classList.remove('hidden');
                                fileInput.value = '';
                                return;
                            }
                        }

                        const formData = new FormData(this);
                        const submitBtn = this.querySelector('button[type="submit"]');
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Saving...';
                        submitBtn.disabled = true;

                        fetch(this.action, {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Submission updated successfully!');
                                    location.reload();
                                } else {
                                    alert('Error updating submission: ' + (data.message || 'Unknown error'));
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred while updating the submission.');
                            })
                            .finally(() => {
                                submitBtn.innerHTML = 'Save Changes';
                                submitBtn.disabled = false;
                                closeModal('editSubmissionModal');
                            });
                    });
                </script>






                <?php else: ?>
                    <p class="text-center">This is the <?php echo htmlspecialchars($currentPage); ?> content area.</p>
                <?php endif; ?>














    </div>
</div>




















<!-- Logout Modal -->
<div id="logoutModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-80 text-center">
        <h2 class="text-lg font-semibold mb-4">Are you sure you want to logout?</h2>
        <div class="flex justify-center gap-4">
            <button id="cancelLogout" class="px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-400">No</button>
            <a href="../controller/logout.php" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Yes</a>
        </div>
    </div>
</div>

















<!-- Edit Profile Modal -->
<div id="edit-profile-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-y-auto max-h-screen">
        <!-- Flex direction changes based on screen size -->
        <div class="flex flex-col md:flex-row">
            <!-- Profile Picture Section - Full width on mobile, 1/3 on desktop -->
            <!-- Profile Picture Section -->
            <div class="bg-blue-600 text-white p-6 md:w-1/3 flex flex-col items-center justify-center">

                <div class="relative group mb-6">
                    <div class="w-40 h-40 rounded-full bg-gray-200 overflow-hidden border-[6px] border-blue-100 relative">
                        <?php if (isset($_SESSION['user']['has_profile_image'])): ?>
                            <img id="profile-image"
                                 src="../controller/get_profile_image.php?id=<?php echo $_SESSION['user']['id']; ?>&t=<?php echo time(); ?>"
                                 onerror="this.onerror=null; this.src='../public/image/user.png'"
                                 alt="______Image_____"
                                 class="w-full h-full object-cover">



                            <div id="profile-initials" class="hidden"></div>
                        <?php else: ?>
                            <div id="profile-initials" class="w-full h-full flex items-center justify-center text-center bg-blue-500 text-white text-5xl font-bold">
                                <?php
                                $initials = '';
                                $fullName = $_SESSION['user']['full_name'] ?? '';
                                $nameParts = explode(' ', $fullName);
                                if (count($nameParts) >= 2) {
                                    $initials = substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts)-1], 0, 1);
                                } elseif (count($nameParts) === 1) {
                                    $initials = substr($nameParts[0], 0, 1);
                                }
                                echo strtoupper($initials ?: 'U');
                                ?>
                            </div>
                            <img id="profile-image" src="" alt="Profile" class="hidden w-full h-full object-cover">
                        <?php endif; ?>
                    </div>

                    <label for="modal-profile-upload" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-full bg-black/50 cursor-pointer">
                        <span class="text-white text-sm font-medium bg-blue-600 px-2 py-1 rounded">Change Photo</span>
                        <input id="modal-profile-upload" type="file" accept="image/*" class="hidden">
                    </label>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const profileUpload = document.getElementById('modal-profile-upload');
                        const profileImage = document.getElementById('profile-image');
                        const profileInitials = document.getElementById('profile-initials');
                        const maxSizeMB = 2; // Maximum allowed size in MB
                        const maxSizeBytes = maxSizeMB * 1024 * 1024; // Convert to bytes

                        profileUpload.addEventListener('change', function(e) {
                            if (e.target.files && e.target.files[0]) {
                                const file = e.target.files[0];

                                // Validate file type
                                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                                if (!validTypes.includes(file.type)) {
                                    alert('Please select a valid image file (JPEG, PNG, GIF)');
                                    resetFileInput();
                                    return;
                                }

                                // Validate file size (max 2MB)
                                if (file.size > maxSizeBytes) {
                                    alert(`Image size should be less than ${maxSizeMB}MB. Your file is ${(file.size/(1024*1024)).toFixed(2)}MB`);
                                    resetFileInput();
                                    return;
                                }

                                const reader = new FileReader();

                                reader.onload = function(event) {
                                    // Show the image and hide initials
                                    profileImage.src = event.target.result;
                                    profileImage.classList.remove('hidden');

                                    if (profileInitials) {
                                        profileInitials.classList.add('hidden');
                                    }

                                    // Optional: Trigger a save to server here via AJAX
                                    // uploadProfileImage(file);
                                };

                                reader.readAsDataURL(file);
                            }
                        });

                        function resetFileInput() {
                            // Reset the file input to allow selecting a new file
                            profileUpload.value = '';
                        }

                        // Optional: Function to upload to server immediately
                        function uploadProfileImage(file) {
                            const formData = new FormData();
                            formData.append('profile_image', file);

                            fetch('../controller/update_profile_image.php', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Update timestamp to prevent caching
                                        document.getElementById('profile-image').src =
                                            `../controller/get_profile_image.php?id=<?php echo $_SESSION['user']['id']; ?>&t=${new Date().getTime()}`;
                                    } else {
                                        alert('Error uploading image: ' + (data.message || 'Unknown error'));
                                        resetFileInput();
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error uploading image');
                                    resetFileInput();
                                });
                        }
                    });
                </script>


                <h3 class="text-xl font-semibold mb-1 text-center break-words"><?php echo htmlspecialchars($_SESSION['user']['full_name'] ?? 'User'); ?></h3>
                <p class="text-blue-100 text-base text-center break-words"><?php echo htmlspecialchars($_SESSION['user']['student_id'] ?? ''); ?></p>
            </div>

            <!-- Form Section - Full width on mobile, 2/3 on desktop -->
            <div class="p-4 md:p-6 md:w-4/5 lg:w-3/4 xl:w-2/3">
                <div class="flex justify-between items-center mb-4 md:mb-6">
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800">Edit Profile</h3>
                    <button onclick="closeModal('edit-profile-modal')" class="text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="profile-edit-form" class="space-y-3 md:space-y-4" method="post" action="../controller/update_profile.php" enctype="multipart/form-data">
                    <!-- Single column on mobile, two columns on desktop -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="full_name" name="full_name"
                               value="<?php echo htmlspecialchars($_SESSION['user']['full_name'] ?? 'N/A'); ?>"
                               class="w-96 px-3 py-2 md:px-4 md:py-2 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? 'N/A'); ?>"
                               class="w-96 px-3 py-2 md:px-4 md:py-2 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" id="address" name="address"
                               value="<?php echo htmlspecialchars($_SESSION['user']['address'] ?? 'N/A'); ?>"
                               class="w-96 px-3 py-2 md:px-4 md:py-2 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>


                    <div class="pt-3 md:pt-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <div class="relative">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                       value=""
                                       class="w-full px-3 py-2 md:px-4 md:py-2 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                       placeholder="Enter new password">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700" onclick="togglePassword('password')">
                                    <svg class="h-4 w-4 md:h-5 md:w-5 eye-icon" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="h-4 w-4 md:h-5 md:w-5 eye-slash-icon hidden" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="relative">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <div class="relative">
                                <input type="password" id="confirm_password" name="confirm_password"
                                       value=""
                                       class="w-full px-3 py-2 md:px-4 md:py-2 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                       placeholder="Confirm password">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700" onclick="togglePassword('confirm_password')">
                                    <svg class="h-4 w-4 md:h-5 md:w-5 eye-icon" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="h-4 w-4 md:h-5 md:w-5 eye-slash-icon hidden" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Remove the hidden profile_pic_path field which isn't working correctly -->
                    <!-- Instead, use the file input directly in the form submission -->
                    <!-- Hidden file input will be added by JavaScript -->

                    <div class="pt-3 md:pt-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <button type="button" onclick="closeModal('edit-profile-modal')"
                                class="w-full sm:w-auto px-4 py-2 md:px-5 md:py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            Cancel
                        </button>

                        <button type="submit"
                                class="w-full sm:w-auto px-4 py-2 md:px-5 md:py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle profile image upload and preview -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle profile image upload in the edit profile modal
        const profileUpload = document.getElementById('modal-profile-upload');
        const profileForm = document.getElementById('profile-edit-form');

        if (profileUpload) {
            profileUpload.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const file = e.target.files[0];

                    // Validate file
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Image size exceeds 2MB. Please choose a smaller file.');
                        return;
                    }

                    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        alert('Only JPG, PNG and GIF images are allowed.');
                        return;
                    }

                    // Create a preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const profileImage = document.getElementById('modal-profile-image');
                        const profileInitials = document.getElementById('modal-profile-initials');

                        profileImage.src = e.target.result;
                        profileImage.classList.remove('hidden');
                        profileInitials.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);

                    // Create a hidden file input if it doesn't exist
                    let fileInput = profileForm.querySelector('input[name="profile_image"]');
                    if (!fileInput) {
                        fileInput = document.createElement('input');
                        fileInput.type = 'file';
                        fileInput.name = 'profile_image';
                        fileInput.classList.add('hidden');
                        profileForm.appendChild(fileInput);
                    }

                    // Create a new FileList-like object with our file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                }
            });
        }

        // Handle form submission
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                // If password fields are not empty, check if they match
                if (password || confirmPassword) {
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        document.getElementById('passwordModal').classList.remove('hidden');
                        return false;
                    }

                    // Check password length if provided
                    if (password.length < 8) {
                        e.preventDefault();
                        alert('Password must be at least 8 characters long.');
                        return false;
                    }
                }

                // Add enctype attribute to ensure file upload works
                profileForm.enctype = "multipart/form-data";
            });
        }
    });

    function refreshProfileImage() {
        const profileImage = document.getElementById('profile-image');
        if (profileImage) {
            // Add timestamp to prevent caching
            profileImage.src = profileImage.src.split('?')[0] + '?t=' + new Date().getTime();
        }
    }

    // Helper function to get user ID from the URL or data attribute
    function getUserId() {
        // Try to get from data attribute first
        const userIdElement = document.querySelector('[data-user-id]');
        if (userIdElement && userIdElement.dataset.userId) {
            return userIdElement.dataset.userId;
        }

        // Default to session user ID (this should be set as a JS variable in your PHP)
        return window.currentUserId || '';
    }

    // Function to toggle password visibility
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = passwordInput.nextElementSibling.querySelector('.eye-icon');
        const eyeSlashIcon = passwordInput.nextElementSibling.querySelector('.eye-slash-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeSlashIcon.classList.add('hidden');
        }
    }

    // Function to close modal
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Function to show success modal
    function showSuccessModal(message) {
        const modal = document.getElementById('success-modal');
        const messageElement = document.getElementById('success-message');

        if (messageElement && message) {
            messageElement.textContent = message;
        }

        if (modal) {
            modal.classList.remove('hidden');
        }
    }
</script>

<!-- Add this modal at the end of the body tag, before the closing </body> -->

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md text-center">
        <div class="mb-4 flex justify-center">
            <div class="rounded-full bg-green-100 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
        <h2 class="text-lg font-semibold mb-2">Success!</h2>
        <p id="success-message" class="text-gray-600 mb-4">Your profile has been updated successfully.</p>
        <button id="close-success-modal" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
            OK
        </button>
    </div>
</div>

<!-- Add this script at the end of the body tag, before the closing </body> -->
<script>
    // Function to show success modal
    function showSuccessModal(message) {
        const modal = document.getElementById('success-modal');
        const messageElement = document.getElementById('success-message');

        if (messageElement && message) {
            messageElement.textContent = message;
        }

        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    // Function to close success modal
    function closeSuccessModal() {
        const modal = document.getElementById('success-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Add event listener to close button
    document.addEventListener('DOMContentLoaded', function() {
        const closeButton = document.getElementById('close-success-modal');
        if (closeButton) {
            closeButton.addEventListener('click', closeSuccessModal);
        }

        // Check for success message in PHP session
        <?php if (isset($_SESSION['profile_update_success']) && $_SESSION['profile_update_success'] === true): ?>
        showSuccessModal(<?php echo json_encode($_SESSION['profile_message'] ?? 'Profile updated successfully.'); ?>);
        <?php
        // Clear the message from session so it doesn't show again on refresh
        unset($_SESSION['profile_update_success']);
        unset($_SESSION['profile_message']);
        ?>
        <?php endif; ?>
    });
</script>


<!-- Modal HTML -->
<div id="passwordModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-5">
        <h2 class="text-lg font-semibold mb-4 text-red-600">Password Mismatch</h2>
        <p class="text-sm text-gray-600 mb-4">New password and confirm password do not match.</p>
        <div class="flex justify-end">
            <button onclick="closeModal('passwordModal')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">OK</button>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling;
        const [eye, eyeSlash] = icon.querySelectorAll('svg');

        if (input.type === "password") {
            input.type = "text";
            eye.classList.add("hidden");
            eyeSlash.classList.remove("hidden");
        } else {
            input.type = "password";
            eye.classList.remove("hidden");
            eyeSlash.classList.add("hidden");
        }
    }

    function validatePasswords() {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        if (password !== confirm) {
            document.getElementById('passwordModal').classList.remove('hidden');
            return false;
        }
        return true;
    }

    function closeModal() {
        document.getElementById('passwordModal').classList.add('hidden');
    }

    // Attach to your form (you can adjust selector as needed)
    document.querySelector('form')?.addEventListener('submit', function(e) {
        if (!validatePasswords()) {
            e.preventDefault();
        }
    });
</script>

    <?php if (isset($_GET['message'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('messageModal').classList.remove('hidden');
    });
    <?php endif; ?>


    <!-- Submission Success Modal -->
    <div id="submissionSuccessModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-80 text-center">
            <div class="mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold mb-2">Submission Successful!</h2>
            <p id="submissionSuccessMessage" class="text-gray-600 mb-4">Your document has been submitted successfully!</p>
            <div class="flex justify-between mt-4 gap-2">
                <button id="goToDashboardBtn" class="px-3 py-1.5 text-sm bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                    Back to Dashboard
                </button>
                <button id="submitAnotherBtn" class="px-3 py-1.5 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                    Submit Another
                </button>
            </div>
        </div>
    </div>

    <script>
        // Handle form submission with AJAX
        document.querySelector('.submissions-form')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Submitting...';
            submitBtn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success modal
                        document.getElementById('submissionSuccessMessage').textContent = data.message;
                        document.getElementById('submissionSuccessModal').classList.remove('hidden');

                        // Reset form
                        form.reset();
                        document.getElementById('file_info').classList.add('hidden');
                    } else {
                        // Show error modal
                        const errorContainer = document.getElementById('submissionErrorMessages');
                        errorContainer.innerHTML = '';

                        data.errors.forEach(error => {
                            const errorElement = document.createElement('p');
                            errorElement.className = 'text-sm mb-1';
                            errorElement.textContent = `• ${error}`;
                            errorContainer.appendChild(errorElement);
                        });

                        document.getElementById('submissionErrorModal').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error modal
                    const errorContainer = document.getElementById('submissionErrorMessages');
                    errorContainer.innerHTML = '<p class="text-sm mb-1">• An unexpected error occurred. Please try again.</p>';
                    document.getElementById('submissionErrorModal').classList.remove('hidden');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Close buttons for modals
        document.getElementById('closeSubmissionError')?.addEventListener('click', function() {
            document.getElementById('submissionErrorModal').classList.add('hidden');
        });

        // Success modal buttons
        document.getElementById('goToDashboardBtn')?.addEventListener('click', function() {
            window.location.href = 'userView.php?page=Dashboard';
        });

        document.getElementById('submitAnotherBtn')?.addEventListener('click', function() {
            document.getElementById('submissionSuccessModal').classList.add('hidden');
            // Optionally focus on the first form field if needed
            // document.querySelector('.submissions-form input').focus();
        });
    </script>



</body>
</html>