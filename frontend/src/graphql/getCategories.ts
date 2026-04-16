import { graphqlRequest } from "../services/graphqlClient";
import type { Category } from '../types';

const GET_CATEGORIES = `
  query GetCategories {
    categories {
      name
    }
  }
`;

export const getCategories = async (signal?: AbortSignal) => {
  return graphqlRequest<{ categories: Category[] }>(GET_CATEGORIES, {}, signal);
}