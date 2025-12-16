# BillTrak

## Project Title
BillTrak - A Simple Bill Tracking Web Application

## Description
BillTrak is a lightweight, web-based application designed to help users manage and track their bills and payments. It provides an intuitive interface for adding, editing, and viewing bills, along with visualizations of year-to-date spending through interactive charts.

## Features
- **Bill Management**: Add, edit, and view bills with details like date, payee, amount, payment ID, and comments
- **Payee Management**: Add and manage payees (bill recipients)
- **Yearly Overview**: View bills by year with sorting options (date, payee)
- **Visual Analytics**: Interactive pie chart showing year-to-date spending distribution
- **Dark Mode**: User-friendly dark mode toggle for comfortable viewing
- **Responsive Design**: Works seamlessly across devices
- **Data Persistence**: SQLite database for reliable data storage

## Installation

### Prerequisites
- Docker and Docker Compose installed
- Basic understanding of web applications

### Steps
1. Configure the `docker-compose.yml` file:
   ```yaml
   services:
   app:
     container_name: BillTrak
     restart: unless-stopped
     image: ghcr.io/solo-web-works/billtrak:main

     ports:
       - "8888:80" # Map port 8888 on the host to port 80 in the container.  Change the left side to change the host port.

     volumes:
       # Mount the current directory for live development
       # - .:/var/www/html
       - ./data/bills.db:/var/www/html/data/bills.db # Bind mount to persist SQLite data
      ```

2. Set up the database:
   ```bash
   cp bills-sample.db ./data/bills.db
   ```

3. Start the application using Docker:
   ```bash
   docker-compose up -d
   ```

4. Access the application at:
   ```
   http://localhost:8888
   ```

## CSV Import
- Prepare a CSV file with the headers `Date, Payee, Reference Number, Amount` (see `2025.csv` for an example layout).
- From the project root, run `php data/import.php <csv-file>` (you can pass multiple files to import them in one go).
- The importer creates missing payees automatically and skips duplicate bills that match date + payee + amount + reference number.
- Data is written into `data/bills.db`, so be sure that file exists (copy `data/bills-sample.db` to `data/bills.db` if you need a starting point).

## Usage
1. **Adding a Bill**:
   - Fill out the "Add New Payment" form with date, payee, amount, and optional details
   - Click "Add Bill" to save

2. **Adding a Payee**:
   - Use the "Add New Payee" form in the sidebar
   - Enter the payee name and click "Add Payee"

3. **Viewing Bills**:
   - Bills are automatically displayed for the selected year
   - Use the sort dropdown to organize bills by date or payee

4. **Editing a Bill**:
   - Click the "Edit" button on any bill
   - Modify the details in the popup modal and click "Save"

5. **Viewing Analytics**:
   - The YTD Chart section shows a pie chart of spending distribution
   - The YTD Summary displays total amounts by payee

## Configuration
The application can be configured through the following methods:

1. **Port Configuration**:
   Modify the `docker-compose.yml` file to change the host port:
   ```yaml
   ports:
     - "8888:80" # Change 8888 to your preferred port
   ```

2. **Database Configuration**:
   The SQLite database file is located at `data/bills.db`. This location should be bind mounted in your compose file for data persistence.

3. **Environment Variables**:
   Add environment variables to the `docker-compose.yml` file for advanced configuration:
   ```yaml
   environment:
     - PHP_ERROR_REPORTING=E_ALL
     - PHP_DISPLAY_ERRORS=On
   ```

## Contribution Guidelines
We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a new branch for your feature/bugfix
3. Commit your changes with clear, descriptive messages
4. Submit a pull request with a detailed explanation of your changes

Please ensure your code follows the existing style and includes appropriate documentation.

## Testing
The application includes basic functionality testing:

1. Start the application using Docker
2. Open the application in your browser
3. Test all features:
   - Add, edit, and delete bills
   - Add new payees
   - Verify chart updates
   - Test sorting functionality
   - Verify dark mode toggle

## Acknowledgements
- **Chart.js** for data visualization
- **Tailwind CSS** for styling
- **PHP** and **SQLite** for backend functionality
- **Docker** for containerization and easy deployment

## Roadmap
- [ ] Implement user authentication
- [ ] Add CSV import/export functionality
- [ ] Implement bill reminders and notifications
- [ ] Add advanced reporting features

For feature requests or bug reports, please open an issue on (GitHub)[https://github.com/Solo-Web-Works/BillTrak/issues].
