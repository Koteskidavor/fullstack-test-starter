export interface Category {
    name: string;
}

export interface NavbarProps {
    categories: string[];
}

interface Currency {
    label: string;
    symbol: string;
}
interface Price {
    amount: number,
    currency: Currency;
}

export interface AttributeItem {
    id: string;
    value: string;
    displayValue: string;
}

export interface Attribute {
    id: string;
    name: string;
    type: string;
    items: AttributeItem[];
}

export interface Product {
    id: string;
    name: string;
    inStock: boolean;
    gallery: string[];
    description: string;
    category: string;
    brand: string;
    prices: Price[];
    attributes: Attribute[];
}


export interface CartItemProps {
    item: CartItem;
    onQuantityChange: (cartItemId: string, delta: number) => void;
}


export type StatusMessageProps = {
    message: string;
}

export interface CartItem {
    cartItemId: string;
    product: Product;
    selectedAttributes: Record<string, string>;
    quantity: number;
}

export interface CartState {
    items: CartItem[];
    isOverlayOpen: boolean;
}

export type CartAction =
    | { type: 'ADD_TO_CART'; payload: { product: Product; selectedAttributes: Record<string, string>; showOverlay?: boolean } }
    | { type: 'REMOVE_FROM_CART'; payload: { cartItemId: string } }
    | { type: 'UPDATE_QUANTITY'; payload: { cartItemId: string; quantity: number } }
    | { type: 'ADJUST_QUANTITY'; payload: { cartItemId: string; delta: number } }
    | { type: 'TOGGLE_CART_OVERLAY' }
    | { type: 'CLEAR_CART' }
    | { type: 'SET_CART_OVERLAY'; payload: boolean }
    | { type: 'HYDRATE_CART'; payload: CartItem[] };


export interface ImageGalleryProps {
    images: string[];
    productName: string;
}

export type AttributeProps = {
    attributes: Product['attributes'];
    selectedAttributes: { [key: string]: string };
    onChange: (attrId: string, itemId: string) => void;
}