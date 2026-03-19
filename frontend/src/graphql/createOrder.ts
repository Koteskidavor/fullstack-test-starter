import { graphqlRequest } from '../services/graphqlClient';
import type { CartItem } from '../types/index';
import { getCurrencyLabel, getCurrencySymbol, getCurrencyValue } from '../utils/getCurrency';

const CREATE_ORDER = `
  mutation CreateOrder($items: [OrderItemInput!]!) {
    createOrder(items: $items) {
      id
      message
    }
  }
`;

export const createOrder = async (items: CartItem[]) => {
  const variables = {
    items: items.map(item => ({
      product_id: item.product.id,
      quantity: item.quantity,
      price_amount: getCurrencyValue(item.product.prices),
      currency_label: getCurrencyLabel(item.product.prices),
      currency_symbol: getCurrencySymbol(item.product.prices),
      attributes: Object.entries(item.selectedAttributes).map(([id, value]) => ({
        id,
        value
      }))
    }))
  };

  return graphqlRequest<{ createOrder: { id: string; message: string } }>(
    CREATE_ORDER,
    variables
  );
}