import React, { createContext, useContext } from 'react';
import type { Product, CartState, CartAction } from '../../../types';

export interface CartContextValue {
    state: CartState;
    dispatch: React.Dispatch<CartAction>;
    totalItems: number;
    cartTotal: number;
    addToCartWithDefaults: (product: Product) => void;
}

export const CartContext = createContext<CartContextValue | undefined>(undefined);

export const useCart = () => {
    const context = useContext(CartContext);
    if (context === undefined) {
        throw new Error('useCart must be used within a CartProvider');
    }
    return context;
};
