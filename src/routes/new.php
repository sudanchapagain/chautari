<?php
ob_start();
include __DIR__ . "/../components/header.php";

$user_id = $_SESSION["user_id"] ?? null;
$db = getDbConnection();

$query = "SELECT is_organizer FROM users WHERE user_id = $1";
$result = pg_query_params($db, $query, [$user_id]);
$user = pg_fetch_assoc($result);

if (!$user["is_organizer"]) {
    $updateQuery = "UPDATE users SET is_organizer = TRUE WHERE user_id = $1";
    pg_query_params($db, $updateQuery, [$user_id]);
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"] ?? "";
    $location = $_POST["location"] ?? "";
    $start_date = $_POST["start_date"] ?? "";
    $end_date = $_POST["end_date"] ?? "";
    $price = isset($_POST["price"]) && $_POST["price"] !== "" ? floatval($_POST["price"]) : null;
    $faqs = $_POST["faqs"] ?? [];
    $category = $_POST["category"] ?? null;
    $capacity = $_POST["capacity"] ?? null;
    $terms_and_conditions = $_POST["terms_and_conditions"] ?? null;
    $description = $_POST["description"] ?? "";
    $promo_code = isset($_POST["promo_code"]) && !empty($_POST["promo_code"]["code"]) ? $_POST["promo_code"] : null;
    $ticket_types = $_POST["ticket_types"] ?? [];

    $uploadDir = __DIR__ . '/../../public/uploads/events/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $event_image_url = null;
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['event_image'];
        $fileName = time() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($file['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Invalid file type. Only JPG, PNG and GIF images are allowed.";
        } elseif ($file['size'] > 5000000) {
            $errors[] = "File is too large. Maximum size is 5MB.";
        } else {
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $event_image_url = '/uploads/events/' . $fileName;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    try {
        pg_query($db, "BEGIN");

        $eventQuery = "INSERT INTO events (title, location, organizer_id, description, capacity, terms_and_conditions, is_approved)
                        VALUES ($1, $2, $3, $4, $5, $6, FALSE)
                        RETURNING event_id";
        $eventResult = pg_query_params($db, $eventQuery, [
            $title,
            $location,
            $user_id,
            $description,
            $capacity,
            $terms_and_conditions,
        ]);
        $event_id = pg_fetch_result($eventResult, 0, "event_id");

        if ($event_image_url) {
            $imageQuery = "INSERT INTO event_images (event_id, image_url, image_type) VALUES ($1, $2, $3)";
            pg_query_params($db, $imageQuery, [$event_id, $event_image_url, 'main']);
        }

        $dateQuery = "INSERT INTO event_dates (event_id, start_date, end_date) VALUES ($1, $2, $3)";
        pg_query_params($db, $dateQuery, [$event_id, $start_date, $end_date]);

        if ($price !== null) {
            $ticketQuery = "INSERT INTO tickets (event_id, ticket_type, ticket_price) VALUES ($1, $2, $3)";
            foreach ($ticket_types as $ticket) {
                pg_query_params($db, $ticketQuery, [$event_id, $ticket['type'], $ticket['price']]);
            }
        }

        if ($category) {
            $categoryQuery = "INSERT INTO event_category_mapping (event_id, category_id) VALUES ($1, $2)";
            pg_query_params($db, $categoryQuery, [$event_id, $category]);
        }

        foreach ($faqs as $faq) {
            if (!empty($faq["question"]) && !empty($faq["answer"])) {
                $faqQuery = "INSERT INTO faqs (event_id, question, answer) VALUES ($1, $2, $3)";
                pg_query_params($db, $faqQuery, [$event_id, $faq["question"], $faq["answer"]]);
            }
        }

        if ($promo_code) {
            $promoQuery = "INSERT INTO promocodes (event_id, promocode, discount_percentage) VALUES ($1, $2, $3)";
            pg_query_params($db, $promoQuery, [$event_id, $promo_code['code'], $promo_code['discount']]);
        }

        pg_query($db, "COMMIT");

        header("Location: /dashboard");
        exit();
    } catch (Exception $e) {
        pg_query($db, "ROLLBACK");
        $errors[] = "Error creating event: " . $e->getMessage();
    }
}

$categoriesQuery = 'SELECT category_id, name FROM event_categories';
$categoriesResult = pg_query($db, $categoriesQuery);
$categories = pg_fetch_all($categoriesResult);
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
                    <li class="error-message"><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

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
                </div>
                <div>
                    <label for="end_date">End Date and Time</label>
                    <input type="datetime-local" name="end_date" id="end_date" required>
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

            <label for="description">Event Description</label>
            <textarea name="description" id="description" style="height: 200px;" required></textarea>
            <br><br>

            <label for="capacity">Event Capacity</label>
            <input type="number" name="capacity" id="capacity" required min="1">
            <span class="tooltip">Specify the maximum number of attendees.</span>
            <div id="capacityError" class="error-message"></div>
            <br>

            <label for="terms_and_conditions">Terms and Conditions</label>
            <textarea name="terms_and_conditions" id="terms_and_conditions" style="height: 100px;"></textarea>
            <br><br>

            <label for="category">Event Category</label>
            <select name="category" id="category">
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <label for="event_image">Event Image</label>
            <input type="file" name="event_image" id="event_image" accept="image/*">
            <span class="tooltip">Upload an image to represent the event.</span>
            <br><br>

            <h3>FAQs</h3>
            <div id="faqs">
                <div class="faq">
                    <label class="faq-label" for="faqs0">Question
                        <input type="text" name="faqs[0][question]" required width="100%">
                    </label>
                    <label class="faq-label" for="faqs0">Answer
                        <input type="text" name="faqs[0][answer]" required width="100%">
                    </label>
                </div>
            </div>
            <button class="button-new button-secondary-new" type="button" onclick="addFaq()">Add More FAQ</button>
            <br><br><br>

            <h3>Ticket Types</h3>
            <span class="tooltip">Leave the price empty if it's free.</span>
            <div id="ticket_types">
                <div class="ticket new-event-date-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <label for="ticket_type_0">Ticket Type
                        <input type="text" name="ticket_types[0][type]" required>
                    </label>
                    <label for="ticket_price_0">Ticket Price
                        <input type="number" name="ticket_types[0][price]" step="0.01" min="0">
                    </label>
                </div>
            </div>
            <button class="button-new button-secondary-new" type="button" onclick="addTicketType()">Add Ticket Type</button>
            <br><br>

            <h3>Promo Code</h3>
            <div class="ticket new-event-date-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label for="promo_code">Code</label>
                    <input type="text" name="promo_code[code]" id="promo_code_code">
                </div>
                <div>
                    <label for="promo_discount">Promo Discount (%)</label>
                    <input type="number" name="promo_code[discount]" id="promo_discount" step="0.01" min="0" max="100">
                </div>
            </div>
            <br><br>

            <button class="button-new" type="submit">Create Event</button>
        </form>

        <script>
            let faqCount = 1;
            let ticketCount = 1;

            function addFaq() {
                const faqDiv = document.createElement('div');
                faqDiv.className = 'faq';
                faqDiv.innerHTML = `
                    <label class="faq-label">Question:
                        <input type="text" name="faqs[${faqCount}][question]" required>
                    </label>
                    <label class="faq-label">Answer:
                        <input type="text" name="faqs[${faqCount}][answer]" required>
                    </label>
                `;
                document.getElementById('faqs').appendChild(faqDiv);
                faqCount++;
            }

            function addTicketType() {
                const ticketDiv = document.createElement('div');
                ticketDiv.className = 'ticket';
                ticketDiv.innerHTML = `
                <div class="ticket new-event-date-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <label for="ticket_type_${ticketCount}">Ticket Type:
                        <input type="text" name="ticket_types[${ticketCount}][type]" required>
                    </label>
                    <label for="ticket_price_${ticketCount}">Ticket Price:
                        <input type="number" name="ticket_types[${ticketCount}][price]" required step="0.01" min="0">
                    </label>
                </div>
                `;
                document.getElementById('ticket_types').appendChild(ticketDiv);
                ticketCount++;
            }

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

        .faq-label > input {
            height: 40px;
        }

    </style>
    </main>
</body>

<?php include __DIR__ . "/../components/footer.php"; ?>
