import type { Product } from '../types';

export const getCurrencySymbol = (prices: Product['prices'] | undefined) => {
    if (!prices || prices.length === 0) return '';
    return prices[0].currency.symbol;
}

export const getCurrencyAmount = (prices: Product['prices'] | undefined) => {
    if (!prices || prices.length === 0) return '0.00';
    return prices[0].amount.toFixed(2);
}

export const getCurrencyLabel = (prices: Product['prices'] | undefined) => {
    if (!prices || prices.length === 0) return '';
    return prices[0].currency.label;
}

export const getCurrencyValue = (prices: Product['prices'] | undefined) => {
    if (!prices || prices.length === 0) return 0;
    return prices[0].amount;
}