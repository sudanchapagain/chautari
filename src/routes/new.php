<?php
ob_start();
include __DIR__ . "/../components/header.php";

$user_id = $_SESSION["user_id"] ?? null;
$db = getDbConnection();
$errors = [];

if ($user_id) {
    $query = "SELECT is_organizer FROM users WHERE user_id = $1";
    $result = pg_query_params($db, $query, [$user_id]);
    $user = pg_fetch_assoc($result);

    if (!$user || !$user["is_organizer"]) {
        $updateQuery = "UPDATE users SET is_organizer = TRUE WHERE user_id = $1";
        pg_query_params($db, $updateQuery, [$user_id]);
    }
} else {
    header("Location: /login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $start_date = trim($_POST["start_date"] ?? "");
    $end_date = trim($_POST["end_date"] ?? "");
    $capacity = intval($_POST["capacity"] ?? 0);
    $description = trim($_POST["description"] ?? "");
    $category = intval($_POST["category"] ?? null);
    $price = isset($_POST["ticket_price"]) && $_POST["ticket_price"] !== "" ? floatval($_POST["ticket_price"]) : 0.00;
    $ticket_types = $_POST["ticket_types"] ?? [];
    $event_image_url = null;

    if (empty($title) || empty($location) || empty($start_date) || empty($end_date) || $capacity <= 0) {
        $errors[] = "All required fields must be filled.";
    }

    if (!empty($start_date) && !empty($end_date) && strtotime($start_date) >= strtotime($end_date)) {
        $errors[] = "Start date must be earlier than the end date.";
    }

    if (!empty($_FILES['event_image']['name'])) {
        $uploadDir = __DIR__ . '/../../public/uploads/events/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $file = $_FILES['event_image'];
        $fileName = time() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($file['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Invalid file type. Only JPG, PNG, and GIF images are allowed.";
        } elseif ($file['size'] > 5000000) {
            $errors[] = "File is too large. Maximum size is 5MB.";
        } elseif (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errors[] = "Failed to upload image.";
        } else {
            $event_image_url = '/uploads/events/' . $fileName;
        }
    }

    if (empty($errors)) {
        try {
            pg_query($db, "BEGIN");

            $eventQuery = "INSERT INTO events (title, location, organizer_id, description, capacity, ticket_price, is_approved)
                           VALUES ($1, $2, $3, $4, $5, $6, FALSE) RETURNING event_id";
            $eventResult = pg_query_params($db, $eventQuery, [$title, $location, $user_id, $description, $capacity, $price]);
            $event_id = pg_fetch_result($eventResult, 0, "event_id");

            if ($event_image_url) {
                $imageQuery = "INSERT INTO event_images (event_id, image_url, image_type) VALUES ($1, $2, 'main')";
                pg_query_params($db, $imageQuery, [$event_id, $event_image_url]);
            }

            $dateQuery = "INSERT INTO event_dates (event_id, start_date, end_date) VALUES ($1, $2, $3)";
            pg_query_params($db, $dateQuery, [$event_id, $start_date, $end_date]);

            $ticketQuery = "INSERT INTO tickets (event_id, ticket_type, ticket_price) VALUES ($1, $2, $3)";
            foreach ($ticket_types as $ticket) {
                if (!empty($ticket['type']) && is_numeric($ticket['price']) && $ticket['price'] >= 0) {
                    pg_query_params($db, $ticketQuery, [$event_id, $ticket['type'], $ticket['price']]);
                }
            }

            if ($category) {
                $categoryQuery = "INSERT INTO event_category_mapping (event_id, category_id) VALUES ($1, $2)";
                pg_query_params($db, $categoryQuery, [$event_id, $category]);
            }

            pg_query($db, "COMMIT");
            header("Location: /dashboard");
            exit();
        } catch (Exception $e) {
            pg_query($db, "ROLLBACK");
            $errors[] = "Error creating event: " . $e->getMessage();
        }
    }
}

$categoriesQuery = 'SELECT category_id, name FROM event_categories';
$categoriesResult = pg_query($db, $categoriesQuery);
$categories = pg_fetch_all($categoriesResult) ?: [];
?>

<head>
    <title>Create Event - Chautari</title>
    <link rel="stylesheet" href="../assets/css/default.css">
    <link rel="stylesheet" href="../assets/css/index.css">
</head>

<body>
    <main>
        <h1 style="font-size: 2.5rem; color: #ff4a22">Create a new event!</h1>

        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li class="error-message"><?=htmlspecialchars($error)?></li>
                <?php endforeach;?>
            </ul>
        <?php endif;?>

        <form action="new" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="title">Event Title</label>
            <input type="text" name="title" id="title" placeholder="Internastional art exhibition by nepal art council" required>
            <div id="titleError" class="error-message"></div>
            <br>

            <label for="location">Location</label>
            <input type="text" name="location" id="location" placeholder="Pradarshani marg, Kathmandu" required>
            <div id="locationError" class="error-message"></div>
            <br>

            <div id="startDateError" class="error-message"></div>
            <div id="endDateError" class="error-message"></div>
            <div class="new-event-date-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label for="start_date">Start Date and Time</label>
                    <input type="datetime-local" name="start_date" id="start_date" required>
                    <script>
                        document.getElementById("start_date").min = new Date().toISOString().split("T")[0] + "T00:00";
                    </script>
                </div>
                <div>
                    <label for="end_date">End Date and Time</label>
                    <input type="datetime-local" name="end_date" id="end_date" required>
                    <script>
                        document.getElementById("end_date").min = new Date().toISOString().split("T")[0] + "T00:00";
                    </script>
                </div>
            </div>
            <style>
                @media screen and (max-width: 550px) {
                    .new-event-date-grid {
                        grid-template-columns: 1fr !important;
                    }
                }
            </style>
            <br>

            <label for="event_image">Event Image</label>
            <input type="file" name="event_image" id="event_image" accept="image/*">
            <span class="tooltip">Upload an image to represent the event.</span>
            <br><br>

            <label for="description">Event Description</label>
            <textarea name="description" id="description" style="height: 200px;" required></textarea>
            <br><br>

            <label for="capacity">Event Capacity</label>
            <input type="number" name="capacity" id="capacity" placeholder="Specify the maximum number of attendees." required min="1">
            <div id="capacityError" class="error-message"></div>
            <br><br>

            <h3>Ticket Price</h3>
            <span class="tooltip">Leave the price empty if it's free.</span>
            <div id="ticket_price">
                <input type="number" name="ticket_price" step="0.01" min="0" placeholder="Ticket Price">
            </div>
            <br>

            <label for="category">Event Category</label>
            <select name="category" id="category">
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?=$category['category_id']?>"><?=htmlspecialchars($category['name'])?></option>
                <?php endforeach;?>
            </select>
            <br><br>

            <button class="button-new" type="submit">Create Event</button>
        </form>

        <script>
            function validateForm() {
                let isValid = true;
                let title = document.getElementById("title").value.trim();
                let location = document.getElementById("location").value.trim();
                let startDate = document.getElementById("start_date").value;
                let endDate = document.getElementById("end_date").value;
                let capacity = document.getElementById("capacity").value;

                clearErrors();

                if (title === "") {
                    document.getElementById("titleError").innerText = "Title is required.";
                    isValid = false;
                }

                if (location === "") {
                    document.getElementById("locationError").innerText = "Location is required.";
                    isValid = false;
                }

                if (startDate === "") {
                    document.getElementById("startDateError").innerText = "Start date is required.";
                    isValid = false;
                }

                if (endDate === "") {
                    document.getElementById("endDateError").innerText = "End date is required.";
                    isValid = false;
                }

                if (new Date(startDate) >= new Date(endDate)) {
                    document.getElementById("endDateError").innerText = "End date must be after start date.";
                    isValid = false;
                }

                if (capacity <= 0) {
                    document.getElementById("capacityError").innerText = "Capacity must be a positive number.";
                    isValid = false;
                }

                return isValid;
            }

            function clearErrors() {
                document.getElementById("titleError").innerText = "";
                document.getElementById("locationError").innerText = "";
                document.getElementById("startDateError").innerText = "";
                document.getElementById("endDateError").innerText = "";
                document.getElementById("capacityError").innerText = "";
            }
        </script>
    <style>
        form {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .button-new {
            margin-top: 20px;
            padding: 10px 15px;
            font-size: 1em;
            border-radius: 4px;
            background-color: #000000;
            color: white;
            border: none;
            cursor: pointer;
        }

        .button-secondary-new {
            background-color: #f9f9f9 !important;
            color: #000000 !important;
            border: 1px solid #555 !important;
            padding: 6px 10px !important;
        }

        .button-secondary-new:hover {
            background-color: #555 !important;
            color: #f9f9f9 !important;
        }

        .tooltip {
            font-size: 0.8em;
            color: #777;
        }

    </style>
    </main>
</body>

<?php include __DIR__ . "/../components/footer.php";?>
