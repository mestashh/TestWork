import axios from 'axios';
import { ref  } from 'vue';
import type {Ref} from 'vue';
import { useAuth } from './useAuth';

export type Category = {
    id: number;
    name: string;
    description?: string;
};

export type CategoryInfo = {
    id: number;
    name: string;
    description?: string;
};

export type Product = {
    id: number;
    name: string;
    description: string;
    price: number | string;
    category_id: number;
    category_info?: CategoryInfo | null;
};

export type Pagination = {
    current_page: number;
    last_page: number;
    per_page?: number;
    total?: number;
};

export type ValidationErrors = {
    name?: string[];
    description?: string[];
    price?: string[];
    category_id?: string[];
    [key: string]: string[] | undefined;
};

type ProductsResponse = {
    data: Product[];
    meta: Pagination;
};

type ProductResponse = {
    data: Product;
};

type CategoriesResponse = { data: Category[] } | Category[];

type ProductPayload = {
    name: string;
    description: string;
    price: number;
    category_id: number | string;
};

export function useProducts(): {
    products: Ref<Product[]>;
    product: Ref<Product | null>;
    categories: Ref<Category[]>;
    loading: Ref<boolean>;
    errors: Ref<ValidationErrors>;
    pagination: Ref<Pagination | null>;
    fetchProducts: (
        page?: number,
        categoryId?: string | number,
        search?: string,
    ) => Promise<void>;
    fetchProduct: (id: number | string) => Promise<void>;
    fetchCategories: () => Promise<void>;
    createProduct: (payload: ProductPayload) => Promise<unknown>;
    updateProduct: (
        id: number | string,
        payload: ProductPayload,
    ) => Promise<unknown>;
    deleteProduct: (id: number | string) => Promise<unknown>;
} {
    const products = ref<Product[]>([]);
    const product = ref<Product | null>(null);
    const categories = ref<Category[]>([]);
    const loading = ref(false);
    const errors = ref<ValidationErrors>({});
    const pagination = ref<Pagination | null>(null);

    const { authHeaders } = useAuth();

    const fetchProducts = async (
        page = 1,
        categoryId: string | number = '',
        search = '',
    ): Promise<void> => {
        loading.value = true;

        try {
            const params: Record<string, string | number> = { page };

            if (
                categoryId !== '' &&
                categoryId !== null &&
                categoryId !== undefined
            ) {
                params.category_id = categoryId;
            }

            if (search.trim()) {
                params.search = search.trim();
            }

            const response = await axios.get<ProductsResponse>(
                '/api/products',
                { params },
            );

            products.value = response.data.data;
            pagination.value = response.data.meta;
        } finally {
            loading.value = false;
        }
    };

    const fetchProduct = async (id: number | string): Promise<void> => {
        loading.value = true;

        try {
            const response = await axios.get<ProductResponse>(
                `/api/products/${id}`,
            );
            product.value = response.data.data;
        } finally {
            loading.value = false;
        }
    };

    const fetchCategories = async (): Promise<void> => {
        const response = await axios.get<CategoriesResponse>('/api/categories');

        if (Array.isArray(response.data)) {
            categories.value = response.data;
        } else {
            categories.value = response.data.data ?? [];
        }
    };

    const createProduct = async (payload: ProductPayload): Promise<unknown> => {
        errors.value = {};

        return axios.post('/api/products', payload, {
            headers: authHeaders.value,
        });
    };

    const updateProduct = async (
        id: number | string,
        payload: ProductPayload,
    ): Promise<unknown> => {
        errors.value = {};

        return axios.put(`/api/products/${id}`, payload, {
            headers: authHeaders.value,
        });
    };

    const deleteProduct = async (id: number | string): Promise<unknown> => {
        return axios.delete(`/api/products/${id}`, {
            headers: authHeaders.value,
        });
    };

    return {
        products,
        product,
        categories,
        loading,
        errors,
        pagination,
        fetchProducts,
        fetchProduct,
        fetchCategories,
        createProduct,
        updateProduct,
        deleteProduct,
    };
}
