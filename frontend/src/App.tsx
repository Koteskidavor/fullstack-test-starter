import { useEffect, useState } from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { graphqlRequest } from './services/graphqlClient';
import { GET_CATEGORIES } from './graphql/getCategories';
import Navbar from './components/Navbar/Navbar';
import { CartProvider } from './components/CartOverlay/context/CartProvider';
import { NotificationProvider } from './components/Notification/Notification';
import CartOverlay from './components/CartOverlay/CartOverlay';
import StatusMessage from './components/StatusMessage/StatusMessage';
import type { Category } from './types';
import CategoryPage from './pages/CategoryPage/CategoryPage';
import ProductDetails from './pages/ProductDetails/ProductDetails';


function App() {
  const [categories, setCategories] = useState<Category[]>([]);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const controller = new AbortController();
    async function fetchCategories() {
      try {
        setLoading(true);
        const data = await graphqlRequest<{ categories: Category[] }>(
          GET_CATEGORIES,
          {},
          controller.signal
        );
        if (!controller.signal.aborted) {
          setCategories(data.categories);
        }
      } catch (error) {
        if (!controller.signal.aborted) {
          console.error(error);
          setError("Failed to load categories. Please try again later.");
        }
      } finally {
        if (!controller.signal.aborted) {
          setLoading(false);
        }
      }
    }
    fetchCategories();

    return () => controller.abort();
  }, []);

  if (loading) {
    return <StatusMessage message="Loading..." />
  }

  if (error) {
    return <StatusMessage message={error} />
  }

  if (!categories.length) {
    return <StatusMessage message="No categories found." />
  }

  return (
    <Router>
      <NotificationProvider>
        <CartProvider>
          <Navbar categories={categories.map((category) => category.name)} />
          <CartOverlay />
          <Routes>
            <Route path="/" element={<Navigate to={`/${categories[0].name}`} />} />
            <Route path="/:name" element={<CategoryPage />} />
            <Route path="/product/:id" element={<ProductDetails />} />
          </Routes>
        </CartProvider>
      </NotificationProvider>
    </Router>
  )
}



export default App
