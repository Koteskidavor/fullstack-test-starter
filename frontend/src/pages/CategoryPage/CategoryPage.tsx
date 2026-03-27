import { useCallback, useEffect, useState, memo } from "react";
import type { Product } from "../../types";
import { useParams } from "react-router-dom";
import { graphqlRequest } from "../../services/graphqlClient";
import { GET_PRODUCTS } from "../../graphql/getProducts";
import { useCart } from "../../components/CartOverlay/context/CartContext";
import ProductCard from "../../components/ProductCard/ProductCard";
import StatusMessage from "../../components/StatusMessage/StatusMessage";
import "./CategoryPage.css";

export default memo(function CategoryPage() {
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

        const controller = new AbortController();

        setLoading(true);
        setError(null);
        setProducts([]);


        const category = name === 'all' ? '' : name;

        graphqlRequest<{ products: Product[] }>(GET_PRODUCTS, { category }, controller.signal)
            .then((data) => {
                if (!controller.signal.aborted) {
                    setProducts(data.products);
                }
            })
            .catch((err) => {
                if (!controller.signal.aborted) {
                    console.error(err);
                    setError("Failed to load products. Please try again.");
                }
            })
            .finally(() => {
                if (!controller.signal.aborted) {
                    setLoading(false);
                }
            });
    }, [name]);
    if (loading) {
        return (
            <main className="category-page">
                <h1 className="category-page__title">{name}</h1>
                <section className="product-grid" data-testid="product-list">
                    {Array.from({ length: 6 }).map((_, index) => (
                        <div key={index} className="product-card skeleton">
                            <div className="product-card__imageWrapper skeleton-img"></div>
                            <div className="product-card__info product-card__info--full">
                                <div className="skeleton-text"></div>
                                <div className="skeleton-text short"></div>
                            </div>
                        </div>
                    ))}
                </section>
            </main>
        );
    }

    if (error) return <StatusMessage message={error} />;

    return (
        <main className="category-page">
            <h1 className="category-page__title">{name}</h1>

            {products.length === 0 ? (
                <StatusMessage message="No products found in this category." />
            ) : (
                <section className="product-grid" data-testid="product-list">
                    {products.map((product, index) => (
                        <ProductCard key={product.id} product={product} onAddToCart={handleAddToCart} priority={index < 2} />
                    ))}
                </section>
            )}
        </main>
    );
});