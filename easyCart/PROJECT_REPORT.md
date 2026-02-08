# EasyCart Project Report

## 1. What it is made for
EasyCart is a comprehensive, custom-built e-commerce web application designed to facilitate online shopping. It serves as a robust platform for users to browse a catalog of products, manage their shopping cart, and securely place orders. Built on a PHP MVC (Model-View-Controller) architecture with a PostgreSQL database, it is engineered to be a scalable and maintainable solution for digital commerce, demonstrating modern web development practices including AJAX methodology, secure session management, and responsive design.

## 2. Key Features
- **MVC Architecture**: Clean separation of logic (Controllers), data (Models), and presentation (Views) for maintainability.
- **User Authentication**: Secure Login and Signup systems with session management and password hashing.
- **Guest Shopping**: Ability for non-logged-in users to add items to a cart, which seamlessly merges with their account upon login.
- **Dynamic Shopping Cart**: AJAX-powered cart operations (add, update, remove, clear) allowing for instant UI updates without page reloads.
- **Product Management**: Detailed product catalog with filtering, categorization, and rich product detail pages.
- **Checkout Process**: Multi-step checkout flow including address entry, shipping method selection, and order finalization.
- **Order System**: comprehensive order tracking, including order history, detailed status (Pending, Processing, Shipped), and invoice generation.
- **Discount & Coupon System**: Logic to apply dynamic coupon codes (e.g., SAVE10, SAVE20) that calculate percentage-based discounts on the fly.
- **Responsive "Premium" Design**: A modern, aesthetically pleasing user interface utilizing glassmorphism, smooth animations, and a mobile-responsive layout.

## 3. File & Folder Structure
```
root/
├── app/                    # Core application logic
│   ├── controllers/        # Handles incoming requests and logic
│   ├── core/               # Framework core classes (Router, DB, etc.)
│   ├── models/             # Database interactions and business logic
│   └── views/              # HTML templates and display logic
├── public/                 # Web server entry point
│   └── assets/             # Publicly accessible static files (CSS, JS, Images)
├── config/                 # Configuration files
├── database/               # Database setup, schema, and seed scripts
├── includes/               # Legacy helper functions and utilities
└── index.php               # Root entry point (redirects to public)
```

## 4. File Descriptions

### **Root Directory**
*   **index.php**: The main entry point that redirects requests to the `public/` directory for security.
*   **README.md**: Documentation file containing project setup instructions and overview.
*   **.htaccess**: Web server configuration for URL rewriting and access control.

### **app/core/**
*   **App.php**: The bootstrap class that parses URLs and routes requests to the appropriate Controller and Method.
*   **Controller.php**: Base class for all controllers, providing methods to load Models and Views.
*   **Database.php**: Singleton class managing the PostgreSQL database connection using PDO.
*   **Session.php**: Wrapper class for handling PHP sessions, including flash messages and user login state.
*   **Pagination.php**: Utility class for calculating and rendering pagination links for lists (products, orders).

### **app/controllers/**
*   **HomeController.php**: Handles the logic for the landing page, including fetching featured products.
*   **ProductController.php**: Manages product listing, filtering, looking up product details, and category views.
*   **CartController.php**: Manages all shopping cart operations (add, remove, update) and renders the cart page.
*   **CheckoutController.php**: Handles the checkout process, including cost calculations, address validation, and order placement.
*   **AuthController.php**: Manages user registration, login, logout, and session authentication logic.
*   **OrderController.php**: Controls the display of user order history, order details, and invoice generation.
*   **ApiController.php**: Handles AJAX requests for dynamic actions like updating cart quantities, applying coupons, and calculating shipping.
*   **AdminController.php**: (Future/Admin) Logic for administrative dashboards and management.
*   **DashboardController.php**: Controls the user's private dashboard view.

### **app/models/**
*   **UserModel.php**: Handles database operations for users (create, find by email, verify credentials).
*   **ProductModel.php**: Retrieves product data, categories, and inventory from the database.
*   **CartModel.php**: Manages cart data storage (DB/Session), cost calculations, and merging guest carts.
*   **OrderModel.php**: Handles creating new orders, saving order line items, and retrieving order history.
*   **CouponModel.php**: Validates coupon codes and calculates discount amounts based on defined rules.
*   **ShippingModel.php**: Calculates shipping costs based on cart value and selected method.
*   **CategoryModel.php**: Retrieves product categories and brands.
*   **BrandModel.php**: Manages brand-related data.
*   **DiscountModel.php**: Base logic for general discount rules.

### **app/views/**
*   **layouts/header.php**: Common top navigation bar, referencing CSS and external fonts.
*   **layouts/footer.php**: Common footer section with scripts and links.
*   **home/index.php**: The main landing page template displaying banners and featured items.
*   **products/index.php**: The product listing page with grid view and sidebar filters.
*   **products/detail.php**: Single product view showing images, variants, description, and "Add to Cart" button.
*   **cart/index.php**: The shopping cart page displaying items, quantities, summary, and coupon input.
*   **checkout/index.php**: The checkout form for entering shipping details and reviewing the final order.
*   **auth/login.php**: User login form template.
*   **auth/signup.php**: User registration form template.
*   **orders/index.php**: List view of a user's past orders.
*   **orders/detail.php**: Detailed view of a specific order showing items, savings, and status.
*   **orders/invoice.php**: Printable layout for an order invoice.
*   **user/dashboard.php**: User's account overview page.
*   **partials/pagination.php**: Reusable HTML component for pagination controls.

### **public/**
*   **index.php**: The front controller that initializes the `App` core class to handle the request.
*   **router.php**: A helper script for built-in PHP server routing.
*   **assets/css/style.css**: The main stylesheet defining the application's "Premium" look, variables, and responsive rules.
*   **assets/js/script.js**: General frontend JavaScript for UI interactions (sliders, mobile menu).
*   **assets/js/cart-ajax.js**: Specific JavaScript for handling asynchronous cart operations and toasts.

### **config/**
*   **database.php**: Contains database connection parameters (host, dbname, user, password).

### **database/**
*   **schema.sql**: The SQL script that authenticates the database structure (tables for users, products, orders, etc.).
*   **migrate.php**: Script to run migrations and set up the database schema.
*   **Setup/Seed Scripts** (*.php): Scripts like `products.php`, `orders.php` used to populate the database with initial dummy data.

## 5. Flow of Data and User Experience

### **User Experience (UX) Flow**
1.  **Browsing**: The user lands on the Home page, viewing featured collections. They navigate to the Products page, filtering by category or sorting by price.
2.  **Selection**: Clicking a product reveals the Detail page. The user selects variants (e.g., Color, Size) and clicks "Add to Cart".
3.  **Cart Interaction**: A toast notification confirms success. The cart badge updates immediately. The user can view the Cart to adjust quantities or apply a coupon code like "SAVE10".
4.  **Authentication**: If the user is a guest, they can shop freely. Upon proceeding to Checkout, they are prompted to Login or Signup. Their guest cart items are automatically merged into their user account.
5.  **Checkout**: The user enters their shipping address. Shipping costs and taxes are calculated dynamically. The user confirms the order.
6.  **Post-Purchase**: The user is redirected to the Order Detail page or Dashboard. They can view their Order History, track status, or print an invoice.

### **Data Flow (Technical)**
1.  **Request**: Browser sends an HTTP request (e.g., `GET /product/1`).
2.  **Routing**: `public/index.php` bootstraps `App.php`. `App` parses the URL and calls `ProductController::show(1)`.
3.  **Controller Logic**: `ProductController` asks `ProductModel` to fetch data for product ID 1.
4.  **Database Interaction**: `ProductModel` executes a SQL query via `Database` class and returns an array of data.
5.  **Response Preparation**: `ProductController` loads the `views/products/detail.php` view, passing the product data to it.
6.  **Rendering**: The View generates the HTML, embedding the dynamic data, and sends it back to the browser.
7.  **AJAX Variation**: For actions like "Update Cart", the `cart-ajax.js` sends a fetch request to `ApiController`. The Controller processes the logic, updates the DB/Session, and returns a JSON response, which the JavaScript uses to update the DOM without a reload.
