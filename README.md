<div align="center">
    <h1>Event Management System</h1>
    <p>A simple event management (only discovery) system built with PHP and MariaDB.</p>
</div>

## Structure

`/assets`: This folder contains all your static files like CSS, JavaScript, and images.

`/includes`: This directory holds reusable PHP components such as `header.php`, `footer.php`, `navbar.php`, and `db.php` (for database connection). These files will be included in other PHP pages to maintain a consistent look and feel across your site.

`/templates`: This directory contains the dynamic pages of your site. These pages will include logic to fetch and display content from the database.

`/static`: This folder contains static pages like `about.php`, `privacy.php`, `contact.php`, `login.php`, and `signup.php`. These pages are mostly static but might include forms that send data to PHP handlers.

`/handlers`: This directory includes PHP scripts that handle form submissions and other requests, such as login, signup, and event handling.

`/config`: This directory contains configuration files such as `config.php`, where you store settings like database credentials.

`index.php`: The main entry point for your site. It could serve as the home page, and you can include different sections here.

