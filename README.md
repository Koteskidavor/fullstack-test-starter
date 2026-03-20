# Scandiweb Assingment

A modern, high-performance e-commerce platform built with a PHP GraphQL backend and a React/Vite/TypeScript frontend. This project is designed as a robust starter for scalable e-commerce applications, focusing on performance, maintainability, and a premium user experience.

## 🚀 Tech Stack

### Frontend
- **React 19**: Utilizing the latest React features for efficient rendering.
- **Vite**: Ultra-fast build tool and development server.
- **TypeScript**: Ensuring type safety and better developer experience.
- **React Router DOM 7**: Advanced client-side routing.
- **Vanilla CSS**: Clean, performant styling without the overhead of heavy CSS-in-JS libraries.

### Backend
- **PHP 8.0+**: Robust and reliable server-side execution.
- **GraphQL (webonyx/graphql-php)**: Flexible API for efficient data fetching.
- **FastRoute**: High-performance routing for the PHP entry points.
- **Composer**: Dependency management for PHP.

---

## 🛠️ Setup & Installation

### Backend Setup
1. Ensure you have PHP 8.0 or higher installed.
2. Install Composer if you haven't already.
3. Run `composer install` in the project root to install backend dependencies.
4. Set up your database (MySQL) and import the `data.sql` file.
5. Configure your environment variables in a `.env` file (refer to `.env.example` if available).
6. Start your PHP server (e.g., via Apache/XAMPP or `php -S localhost:8000 -t public`).

### Frontend Setup
1. Navigate to the `frontend/` directory.
2. Run `npm install` to install frontend dependencies.
3. Run `npm run dev` to start the Vite development server.
4. Access the application at `http://localhost:5173`.

---

## 🏗️ Architecture Decisions

### Why this stack?
- **GraphQL over REST**: Chosen for its ability to allow the frontend to request exactly the data it needs, reducing payload sizes and avoiding over-fetching.
- **React Context API**: We opted for the Context API over Redux to keep state management lightweight and intuitive, avoiding unnecessary boilerplate while still providing global state accessibility.
- **PHP for Backend**: Leverages the power and maturity of PHP while modernizing it with a GraphQL interface and structured Model/Resolver architecture.

### Folder Structure Reasoning
The project is split into two main sections:
- **`src/` (Backend)**: Contains the PHP logic.
  - `Models/`: Domain entities and data logic.
  - `Resolvers/`: Logic that bridges GraphQL queries to the Models.
  - `Controller/`: Entry point (GraphQL handler).
  - `Factories/`: Responsible for instantiating complex model objects.
- **`frontend/src/` (Frontend)**:
  - `components/`: Reusable UI elements (ProductCard, CartItem, etc.).
  - `pages/`: High-level views (CategoryPage, ProductDetails).
  - `services/`: API interaction layer.
  - `graphql/`: GraphQL query definitions.
  - `types/`: Global TypeScript definitions.

---

## 🧠 State Management Approach

We use **React Context API** combined with the **`useReducer`** hook for global state. This allows for predictable state transitions without the complexity of external libraries.

- **CartContext**: Manages the shopping cart, including adding/removing items, updating quantities, and persisting attribute selections (like size and color).
- **NotificationContext**: A lightweight system for displaying user feedback (success/error messages) across the application.

---

## 🎨 Design Patterns

- **MVC (Model-View-Controller)**: The backend follows a structured MVC approach, keeping data logic (Models) separate from API handling (Controller).
- **Resolver Pattern**: Decouples the GraphQL schema from the underlying database models, making the API easier to evolve.
- **Factory Pattern**: Used in the backend to manage the instantiation of different product types and attributes.
- **Custom Hooks**: Frontend logic like scroll locking or data fetching is abstracted into reusable custom hooks.
- **Service Pattern**: API calls are centralized in a service layer, making them easy to test and maintain.
