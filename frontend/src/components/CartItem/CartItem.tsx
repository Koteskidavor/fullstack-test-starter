import { memo, useCallback } from 'react';
import type { CartItemProps, Attribute, AttributeItem } from '../../types';
import { kebabCase } from '../../utils/kebabCase';
import { getCurrencySymbol, getCurrencyAmount } from '../../utils/getCurrency';
import fallbackImage from '../../assets/fallbackImage.jpg';
import './CartItem.css';


const CartItem = memo(function CartItem({ item, onQuantityChange }: CartItemProps) {
    const { product, selectedAttributes, quantity, cartItemId } = item;

    const handleIncrease = useCallback(() => {
        onQuantityChange(cartItemId, 1);
    }, [cartItemId, onQuantityChange]);

    const handleDecrease = useCallback(() => {
        onQuantityChange(cartItemId, -1);
    }, [cartItemId, onQuantityChange]);

    return (
        <div className="cart-item">
            <div className="cart-item__details">
                <div className="cart-item__name">{product.name}</div>
                <div className="cart-item__price">
                    {getCurrencySymbol(product.prices)}{getCurrencyAmount(product.prices)}
                </div>

                <div className="cart-item__attributes">
                    {product.attributes?.map((attr: Attribute) => (
                        <div key={attr.id} className="cart-item__attribute" data-testid={`cart-item-attribute-${kebabCase(attr.name)}`}>
                            <div className="cart-item__attribute-name">{attr.name}:</div>
                            <div className="cart-item__attribute-items">
                                {attr.items.map((opt: AttributeItem) => {
                                    const isSelected = selectedAttributes[attr.id] === opt.id;
                                    const isColor = attr.type === 'swatch';

                                    if (isColor) {
                                        return (
                                            <span
                                                key={opt.id}
                                                role="img"
                                                aria-label={`${attr.name}: ${opt.displayValue}${isSelected ? ' (selected)' : ''}`}
                                                className={`cart-item__attribute-btn cart-item__attribute-btn--color ${isSelected ? 'selected' : ''}`}
                                                style={{ backgroundColor: opt.value }}
                                                data-testid={`cart-item-attribute-${kebabCase(attr.name)}-${opt.displayValue}${isSelected ? '-selected' : ''}`}
                                            />
                                        );
                                    }

                                    return (
                                        <span
                                            key={opt.id}
                                            role="presentation"
                                            aria-label={`${attr.name}: ${opt.displayValue}${isSelected ? ' (selected)' : ''}`}
                                            className={`cart-item__attribute-btn cart-item__attribute-btn--text ${isSelected ? 'selected' : ''}`}
                                            data-testid={`cart-item-attribute-${kebabCase(attr.name)}-${opt.displayValue}${isSelected ? '-selected' : ''}`}
                                        >
                                            {opt.value}
                                        </span>
                                    );
                                })}
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            <div className="cart-item__controls">
                <button
                    className="cart-item__qty-btn"
                    onClick={handleIncrease}
                    data-testid="cart-item-amount-increase"
                >
                    +
                </button>
                <span className="cart-item__qty-value" data-testid="cart-item-amount">
                    {quantity}
                </span>
                <button
                    className="cart-item__qty-btn"
                    onClick={handleDecrease}
                    data-testid="cart-item-amount-decrease"
                >
                    -
                </button>
            </div>

            <div className="cart-item__image-container">
                <img
                    src={product.gallery[0]}
                    alt={product.name}
                    className="cart-item__image"
                    loading="lazy"
                    onError={(e) => {
                        e.currentTarget.src = fallbackImage;
                    }}
                />
            </div>

        </div>
    );
});

export default CartItem;
