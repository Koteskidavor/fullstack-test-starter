import { graphqlRequest } from "../services/graphqlClient";
import type { Product } from '../types/index';

const GET_PRODUCTS = `
  query GetProducts($category: String) {
    products(category: $category) {
      id
      name
      inStock
      gallery
      category
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

export const getProducts = async (category?: string, signal?: AbortSignal) => {
  return graphqlRequest<{ products: Product[] }>(GET_PRODUCTS, { category }, signal);
}