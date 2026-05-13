
---

# 🚗 Car Showroom Management System

A full-stack web application for managing car showroom inventory, users, and purchase requests with an admin dashboard, authentication system, and modern UI features.



### 🏠 Dashboard

![Dashboard](images/dashboard.png)

### 🚘 Cars List

![Cars](images/cars.png)

### ➕ Add Car

![Add Car](images/add-car.png)

### 🛒 Purchase Requests

![Purchases](images/purchases.png)

---

## ✨ Features

### 🔐 Authentication System

* User registration system
* Secure login/logout
* Session-based access control

### 🚗 Car Management

* Add new cars
* Edit car details
* Delete cars
* View car details
* Upload car images

### 🛒 Purchase System

* Users can request to buy cars
* Store buyer name and phone number
* Admin can track all purchase requests

### 📊 Dashboard

* Total cars count
* Users statistics
* Purchase tracking
* Latest cars overview
* Interactive charts (Chart.js)

### 🌙 UI Features

* Dark Mode support
* Responsive design (Bootstrap 5)
* Modern admin dashboard UI
* Search functionality

---

## 🧠 Tech Stack

* PHP (Core)
* MySQL Database
* HTML5 / CSS3
* Bootstrap 5
* JavaScript (Vanilla)
* Chart.js

---

## 🗄️ Database Structure

### Cars Table

```sql
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(255),
price DECIMAL(10,2),
image VARCHAR(255),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

### Users Table

```sql
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(100),
password VARCHAR(255)
```

### Purchases Table

```sql
id INT AUTO_INCREMENT PRIMARY KEY,
car_id INT,
buyer_name VARCHAR(255),
phone VARCHAR(50),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

---

## 🚀 Installation

1. Clone repository:

```bash
git clone https://github.com/your-username/car-showroom.git
```

2. Move project to:

```
htdocs (XAMPP)
```

3. Import database:

```
car_chowroom.sql
```

4. Start Apache & MySQL

5. Open:

```
http://localhost/car-showroom
```

---

## 🔐 Default Login

```
Username: admin
Password: admin123
```

---

## 📁 Project Structure

```
car-showroom/
│
├── config/
├── controllers/
├── views/
│   ├── auth/
│   └── cars/
├── uploads/
├── cars_api.php
├── dashboard.php
├── login.php
└── register.php
```

---

## 🧩 Future Improvements

* Multi-image upload per car
* Role-based admin/user system
* Payment gateway integration
* API version (Laravel / Node.js upgrade)
* Advanced analytics dashboard

---

## 👨‍💻 Developer

* Developed by: **Your Name**
* Role: Full Stack Developer
* Stack: PHP + MySQL + Bootstrap

---

## ⭐ If you like this project

Give it a ⭐ on GitHub and follow for more projects.

-- 👍
