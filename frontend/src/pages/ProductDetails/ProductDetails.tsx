import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { graphqlRequest } from '../../services/graphqlClient';
import { GET_PRODUCT } from '../../graphql/getProduct';
import type { Product } from '../../types/index';
import { useCart } from '../../components/CartOverlay/context/CartContext';
import { parseHtml } from '../../utils/parseHtml';
import StatusMessage from '../../components/StatusMessage/StatusMessage';
import ImageGallery from '../../components/ImageGallery/ImageGallery';
import AttributeSelector from '../../components/AttributeSelector/AttributeSelector';
import { getCurrencySymbol, getCurrencyAmount } from '../../utils/getCurrency';
import './ProductDetails.css';


export default function ProductDetails() {
    const { id } = useParams<{ id: string }>();
    const [product, setProduct] = useState<Product | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [selectedAttributes, setSelectedAttributes] = useState<{ [key: string]: string }>({});
    const { dispatch } = useCart();

    useEffect(() => {
        if (!id) return;
        setLoading(true);
        setError(null);
        graphqlRequest<{ product: Product }>(GET_PRODUCT, { id })
            .then((data) => {
                setProduct(data.product);
                setSelectedAttributes({});
                setLoading(false);
            })
            .catch((err) => {
                console.error(err);
                setError("Failed to load product details.");
                setLoading(false);
            });
    }, [id]);

    if (loading) return <StatusMessage message="Loading..." />;
    if (error) return <StatusMessage message={error} />;
    if (!product) return <StatusMessage message="Product not found." />;

    const allAttributesSelected = product.attributes.every(
        (attr) => selectedAttributes[attr.id]
    );

    function handleAttributeChange(attrId: string, itemId: string) {
        setSelectedAttributes((prev) => ({ ...prev, [attrId]: itemId }));
    }

    function handleAddToCart() {
        if (!product || !allAttributesSelected) return;
        dispatch({
            type: 'ADD_TO_CART',
            payload: {
                product,
                selectedAttributes,
            },
        });
    }

    return (
        <main className="product-details">
            <ImageGallery key={product.id} images={product.gallery} productName={product.name} />
            <div className="product-details__info">
                <h1 className="product-details__brand">{product.brand}</h1>
                <h2 className="product-details__name">{product.name}</h2>

                <AttributeSelector attributes={product.attributes} selectedAttributes={selectedAttributes} onChange={handleAttributeChange} />

                <div className="product-details__price-group">
                    <p className="product-details__price-label">PRICE:</p>
                    <p className="product-details__price">
                        {getCurrencySymbol(product.prices)}{getCurrencyAmount(product.prices)}
                    </p>
                </div>

                <button
                    className={`product-details__add-btn ${!product.inStock || !allAttributesSelected ? 'product-details__add-btn--disabled' : ''}`}
                    disabled={!product.inStock || !allAttributesSelected}
                    onClick={handleAddToCart}
                    data-testid="add-to-cart"
                >
                    {product.inStock ? 'ADD TO CART' : 'OUT OF STOCK'}
                </button>

                {product.description && (
                    <div className="product-details__description" data-testid="product-description">
                        {parseHtml(product.description)}
                    </div>
                )}
            </div>
        </main>
    );
}