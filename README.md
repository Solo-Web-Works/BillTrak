# BillTrak

BillTrak is a web application designed to help users manage and track their bills. It provides features to add, edit, and view bills, as well as visualize year-to-date (YTD) amounts using charts.

## Features

- Add new bills and payees
- Edit existing bills
- View bills by year and sort them
- Display YTD amounts and visualize them using a pie chart
- Dark mode toggle for better user experience

## Technologies Used

- HTML, Tailwind CSS, JavaScript
- Chart.js for data visualization
- Docker for containerization

## Usage

1. Copy the `bills-sample.db` file to `bills.db` in a folder docker can access. This will be the SQLite database used to store bill data.

2. Start the container using `docker compose up -d` with the following `docker-compose.yml` configuration:
```yaml
services:
app:
  container_name: BillTrak
  restart: unless-stopped
  image: ghcr.io/solo-web-works/billtrak:main

  ports:
    - "8888:80" # Map port 8080 on the host to port 80 in the container.  Change the left side to change the host port.

  volumes:
   # - .:/var/www/html # Mount the current directory for live development
    - ./data/bills.db:/var/www/html/data/bills.db # Persist SQLite data
```
3. Access the application at http://localhost:8888

## Roadmap

- Add feature to import bills from CSV files
- Add feature to export bills to CSV files
- Add authentication and user accounts
- Add notifications for upcoming bills
- Got an idea? Open an issue!

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any improvements or bug fixes.
