import { memo } from "react";
import { Link } from "react-router-dom";
import { kebabCase } from "../../utils/kebabCase";
import CartIcon from "../Icons/CartIcon";
import type { Product } from "../../types";
import { getCurrencySymbol, getCurrencyAmount } from "../../utils/getCurrency";
import "./ProductCard.css";

interface ProductCardProps {
    product: Product;
    onAddToCart: (product: Product) => void;
    priority?: boolean;
}

const ProductCard = memo(function ProductCard({ product, onAddToCart, priority = false }: ProductCardProps) {
    return (
        <Link
            to={`/product/${product.id}`}
            className="product-card__link"
            data-testid={`product-${kebabCase(product.name)}`}
        >
            <article
                className={`product-card ${!product.inStock ? "product-card__outOfStock" : ""}`}
            >
                <div className="product-card__imageWrapper">
                    <img
                        src={product.gallery[0]}
                        alt={product.name}
                        className="product-card__image"
                        loading={priority ? undefined : "lazy"}
                        fetchPriority={priority ? "high" : "auto"}
                    />

                    {!product.inStock && (
                        <span className="product-card__overlayText">
                            OUT OF STOCK
                        </span>
                    )}

                    {product.inStock && (
                        <button
                            className="product-card__cartButton"
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
                    <h2 className="product-card__name">{product.name}</h2>
                    <p className="product-card__price">
                        {getCurrencySymbol(product.prices)}{getCurrencyAmount(product.prices)}
                    </p>
                </div>
            </article>
        </Link>
    );
});

export default ProductCard;