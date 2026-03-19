import { useCallback, useRef, useState } from 'react';
import { useCart } from './context/CartContext';
import CartItem from '../CartItem/CartItem';
import { createOrder } from '../../graphql/createOrder';
import { useNotify } from '../Notification/NotificationContext';
import { useScrollLock } from '../../utils/useScrollLock';
import './CartOverlay.css';

const CartOverlay: React.FC = () => {
    const { state, dispatch, totalItems, cartTotal } = useCart();
    const { notify } = useNotify();
    const overlayRef = useRef<HTMLDivElement>(null);
    const [isPlacingOrder, setIsPlacingOrder] = useState(false);

    useScrollLock(state.isOverlayOpen);

    const handleBackdropClick = useCallback((e: React.MouseEvent<HTMLDivElement>) => {
        if (e.target === e.currentTarget) {
            dispatch({ type: 'TOGGLE_CART_OVERLAY' });
        }
    }, [dispatch]);

    const handleItemQuantityChange = useCallback((cartItemId: string, delta: number) => {
        dispatch({ type: 'ADJUST_QUANTITY', payload: { cartItemId, delta } });
    }, [dispatch]);

    const handlePlaceOrder = useCallback(async () => {
        if (state.items.length === 0) return;

        setIsPlacingOrder(true);
        try {
            await createOrder(state.items);
            dispatch({ type: 'CLEAR_CART' });
            dispatch({ type: 'TOGGLE_CART_OVERLAY' });
            notify("Order placed successfully!");
        } catch (error) {
            console.error("Failed to place order:", error);
            notify("Failed to place order. Please try again.", "error");
        } finally {
            setIsPlacingOrder(false);
        }
    }, [state.items, dispatch, notify]);

    if (!state.isOverlayOpen) return null;

    return (
        <div className="cart-backdrop" onClick={handleBackdropClick}>
            <div className="cart-overlay" ref={overlayRef} data-testid="cart-overlay">
                <div className="cart-overlay__header">
                    <span className="cart-overlay__title">
                        <strong>My Bag:</strong> {totalItems} {totalItems === 1 ? 'Item' : 'Items'}
                    </span>
                </div>

                <div className="cart-overlay__items">
                    {state.items.length === 0 ? (
                        <p className="cart-overlay__empty">Your cart is empty.</p>
                    ) : (
                        state.items.map((item) => (
                            <CartItem
                                key={item.cartItemId}
                                item={item}
                                onQuantityChange={handleItemQuantityChange}
                            />
                        ))
                    )}
                </div>

                <div className="cart-overlay__footer">
                    <div className="cart-overlay__total">
                        <span className="cart-overlay__total-label">Total</span>
                        <span className="cart-overlay__total-amount" data-testid="cart-total">
                            ${cartTotal.toFixed(2)}
                        </span>
                    </div>

                    <div className="cart-overlay__actions">
                        <button
                            className="cart-overlay__btn cart-overlay__btn--checkout"
                            onClick={handlePlaceOrder}
                            disabled={state.items.length === 0 || isPlacingOrder}
                        >
                            {isPlacingOrder ? 'PLACING ORDER...' : 'PLACE ORDER'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CartOverlay;
