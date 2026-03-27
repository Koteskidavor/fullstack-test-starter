import React, { useReducer, useEffect, useMemo, useCallback } from 'react';
import type { ReactNode } from 'react';
import { cartReducer } from './CartReducer';
import { CartContext } from './CartContext';
import type { Product, CartState } from '../../../types';

const initialState: CartState = {
    items: [],
    isOverlayOpen: false,
};

const loadState = (): CartState => {
    try {
        const serializedState = localStorage.getItem('cartState');
        if (serializedState === null) {
            return initialState;
        }
        const state = JSON.parse(serializedState);
        return { ...state, isOverlayOpen: false };
    } catch (err) {
        return initialState;
    }
};

export const CartProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
    const [state, dispatch] = useReducer(cartReducer, initialState, loadState);

    useEffect(() => {
        localStorage.setItem('cartState', JSON.stringify(state));
    }, [state]);

    const totalItems = useMemo(
        () => state.items.reduce((total, item) => total + item.quantity, 0),
        [state.items]
    );

    const cartTotal = useMemo(
        () => state.items.reduce((total, item) => {
            const price = item.product.prices?.[0]?.amount ?? 0;
            return total + price * item.quantity;
        }, 0),
        [state.items]
    );

    const addToCartWithDefaults = useCallback((product: Product) => {
        const defaultAttributes: Record<string, string> = {};
        product.attributes?.forEach(attr => {
            if (attr.items?.length > 0) {
                defaultAttributes[attr.id] = attr.items[0].id;
            }
        });
        dispatch({
            type: 'ADD_TO_CART',
            payload: { product, selectedAttributes: defaultAttributes, showOverlay: true },
        });
    }, []);

    return (
        <CartContext.Provider value={{ state, dispatch, totalItems, cartTotal, addToCartWithDefaults }}>
            {children}
        </CartContext.Provider>
    );
};