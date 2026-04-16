import { graphqlRequest } from "../services/graphqlClient";
import type { Product } from '../types';

const GET_PRODUCT = `
  query GetProduct($id: String!) {
    product(id: $id) {
      id
      name
      inStock
      gallery
      description
      category
      brand
      prices {
        amount
        currency {
          label
          symbol
        }
      }
      attributes {
        id
        name
        type
        items {
          id
          value
          displayValue
        }
      }
    }
  }
`;

export const getProduct = async (id: string, signal?: AbortSignal) => {
  return graphqlRequest<{ product: Product }>(GET_PRODUCT, { id }, signal);
}
