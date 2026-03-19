export const GET_PRODUCTS = `
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