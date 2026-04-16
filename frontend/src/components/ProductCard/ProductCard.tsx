import { Link } from "react-router-dom";
import { kebabCase } from "../../utils/kebabCase";
import { getOptimizedImageUrl } from "../../utils/getImageUrl";
import CartIcon from "../Icons/CartIcon";
import type { Product } from "../../types";
import { getCurrencySymbol, getCurrencyAmount } from "../../utils/getCurrency";
import "./ProductCard.css";

interface ProductCardProps {
    product: Product;
    onAddToCart: (product: Product) => void;
    priority?: boolean;
}
const ProductCard = ({ product, onAddToCart, priority = false }: ProductCardProps) => {
    const optimizedImage = getOptimizedImageUrl(product.gallery[0], 400);

    return (
        <Link to={`/product/${product.id}`} className="product-card-wrapper">
            <article
                className={`product-card ${!product.inStock ? "product-card__outOfStock" : ""}`}
                data-testid={`product-${kebabCase(product.name)}`}
            >
                <div className="product-card__imageWrapper">
                    <span className="product-card__link" tabIndex={-1} aria-hidden="true">
                        <img
                            src={optimizedImage}
                            alt={product.name}
                            className="product-card__image"
                            loading={priority ? undefined : "lazy"}
                            fetchPriority={priority ? "high" : "auto"}
                            width={400}
                            height={440}
                        />


                        {!product.inStock && (
                            <span className="product-card__overlayText">
                                OUT OF STOCK
                            </span>
                        )}
                    </span>

                    {product.inStock && (
                        <button
                            className="product-card__cartButton"
                            aria-label={`Add ${product.name} to cart`}
                            onClick={(e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                onAddToCart(product);
                            }}
                        >
                            <CartIcon className="product-card__cartIcon" size={20} color="white" />
                        </button>
                    )}
                </div>

                <div className="product-card__info">
                    <h2 className="product-card__name">
                        <span className="product-card__link" onClick={(e) => e.stopPropagation()}>
                            {product.name}
                        </span>
                    </h2>
                    <p className="product-card__price">
                        {getCurrencySymbol(product.prices)}{getCurrencyAmount(product.prices)}
                    </p>
                </div>
            </article>
        </Link>
    );
}

export default ProductCard;
