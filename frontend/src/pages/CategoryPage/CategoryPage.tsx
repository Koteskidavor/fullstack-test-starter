import { useCallback, useEffect, useState } from "react";
import type { Product } from "../../types";
import { useParams } from "react-router-dom";
import { graphqlRequest } from "../../services/graphqlClient";
import { GET_PRODUCTS } from "../../graphql/getProducts";
import { useCart } from "../../components/CartOverlay/context/CartContext";
import ProductCard from "../../components/ProductCard/ProductCard";
import StatusMessage from "../../components/StatusMessage/StatusMessage";
import "./CategoryPage.css";

export default function CategoryPage() {
    const { name } = useParams<{ name: string }>();
    const [products, setProducts] = useState<Product[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const { addToCartWithDefaults } = useCart();

    const handleAddToCart = useCallback((product: Product) => {
        addToCartWithDefaults(product);
    }, [addToCartWithDefaults]);

    useEffect(() => {
        if (!name) return;

        setLoading(true);
        setError(null);
        setProducts([]);

        const category = name === 'all' ? undefined : name;

        graphqlRequest<{ products: Product[] }>(GET_PRODUCTS, { category })
            .then((data) => {
                setProducts(data.products);
            })
            .catch((err) => {
                console.error(err);
                setError("Failed to load products. Please try again.");
            })
            .finally(() => {
                setLoading(false);
            });
    }, [name]);

    if (loading) return <StatusMessage message="Loading products..." />;
    if (error) return <StatusMessage message={error} />;

    return (
        <main className="category-page">
            <h1 className="category-page__title">{name}</h1>

            {products.length === 0 ? (
                <StatusMessage message="No products found in this category." />
            ) : (
                <section className="product-grid" data-testid="product-list">
                    {products.map((product, index) => (
                        <ProductCard key={product.id} product={product} onAddToCart={handleAddToCart} priority={index === 0} />
                    ))}
                </section>
            )}
        </main>
    );
}