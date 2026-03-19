import type { CartState, CartAction, CartItem } from '../../../types';

export const generateCartItemId = (productId: string, selectedAttributes: Record<string, string>): string => {
    const sortedAttributes = Object.entries(selectedAttributes)
        .sort(([keyA], [keyB]) => keyA.localeCompare(keyB))
        .map(([key, value]) => `${key}:${value}`)
        .join('|');
    return `${productId}-${sortedAttributes}`;
};

export const cartReducer = (state: CartState, action: CartAction): CartState => {
    switch (action.type) {
        case 'ADD_TO_CART': {
            const { product, selectedAttributes } = action.payload;
            const cartItemId = generateCartItemId(product.id, selectedAttributes);
            const existingItemIndex = state.items.findIndex(item => item.cartItemId === cartItemId);

            if (existingItemIndex > -1) {
                const newItems = [...state.items];
                newItems[existingItemIndex] = {
                    ...newItems[existingItemIndex],
                    quantity: newItems[existingItemIndex].quantity + 1
                };
                return {
                    ...state,
                    items: newItems,
                    isOverlayOpen: action.payload.showOverlay ?? true
                };
            } else {
                const newItem: CartItem = {
                    cartItemId,
                    product,
                    selectedAttributes,
                    quantity: 1
                };
                return {
                    ...state,
                    items: [...state.items, newItem],
                    isOverlayOpen: action.payload.showOverlay ?? true
                };
            }
        }

        case 'REMOVE_FROM_CART': {
            return {
                ...state,
                items: state.items.filter(item => item.cartItemId !== action.payload.cartItemId)
            };
        }

        case 'UPDATE_QUANTITY': {
            const { cartItemId, quantity } = action.payload;
            if (quantity <= 0) {
                return {
                    ...state,
                    items: state.items.filter(item => item.cartItemId !== cartItemId)
                };
            }
            return {
                ...state,
                items: state.items.map(item =>
                    item.cartItemId === cartItemId ? { ...item, quantity } : item
                )
            };
        }


        case 'TOGGLE_CART_OVERLAY': {
            return {
                ...state,
                isOverlayOpen: !state.isOverlayOpen
            };
        }

        case 'SET_CART_OVERLAY': {
            return {
                ...state,
                isOverlayOpen: action.payload
            };
        }

        case 'ADJUST_QUANTITY': {
            const { cartItemId, delta } = action.payload;
            const existing = state.items.find(item => item.cartItemId === cartItemId);
            if (!existing) return state;
            const newQuantity = existing.quantity + delta;
            if (newQuantity <= 0) {
                return { ...state, items: state.items.filter(item => item.cartItemId !== cartItemId) };
            }
            return {
                ...state,
                items: state.items.map(item =>
                    item.cartItemId === cartItemId ? { ...item, quantity: newQuantity } : item
                )
            };
        }

        case 'CLEAR_CART': {
            return {
                ...state,
                items: []
            };
        }

        case 'HYDRATE_CART': {
            return {
                ...state,
                items: action.payload
            };
        }

        default:
            return state;
    }
};
