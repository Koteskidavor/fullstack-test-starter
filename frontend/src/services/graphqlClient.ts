const API_URL = 'http://localhost/fullstack-test-starter/public/index.php';

export async function graphqlRequest<T>(query: string, variables?: Record<string, unknown>): Promise<T> {
    const response = await fetch(API_URL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            query,
            variables,
        }),
    });
    if (!response.ok) {
        throw new Error(`Network error: ${response.statusText}`);
    }

    const json = await response.json();

    if (json.errors) {
        throw new Error(json.errors[0].message || "GraphQL Error");
    }

    return json.data;
}