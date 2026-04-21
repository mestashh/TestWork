import axios from 'axios';
import type {AxiosResponse} from 'axios';
import { computed, ref   } from 'vue';
import type {ComputedRef, Ref} from 'vue';

type LoginCredentials = {
    email: string;
    password: string;
};

type LoginResponse = {
    token: string;
};

type AuthHeaders = Record<string, string>;

const token = ref<string | null>(null);

if (typeof window !== 'undefined') {
    token.value = localStorage.getItem('token');
}

export function useAuth(): {
    token: Ref<string | null>;
    isAuthenticated: ComputedRef<boolean>;
    login: (
        credentials: LoginCredentials,
    ) => Promise<AxiosResponse<LoginResponse>>;
    logout: () => void;
    authHeaders: ComputedRef<AuthHeaders>;
    setToken: (newToken: string | null) => void;
} {
    const isAuthenticated = computed<boolean>(() => !!token.value);

    const setToken = (newToken: string | null): void => {
        token.value = newToken;

        if (typeof window !== 'undefined') {
            if (newToken) {
                localStorage.setItem('token', newToken);
            } else {
                localStorage.removeItem('token');
            }
        }
    };

    const login = async (
        credentials: LoginCredentials,
    ): Promise<AxiosResponse<LoginResponse>> => {
        const response = await axios.post<LoginResponse>(
            '/api/login',
            credentials,
        );
        setToken(response.data.token);

        return response;
    };

    const logout = (): void => {
        setToken(null);
    };

    const authHeaders = computed<AuthHeaders>(() => {
        if (!token.value) {
            return {} as AuthHeaders;
        }

        return {
            Authorization: `Bearer ${token.value}`,
        };
    });

    return {
        token,
        isAuthenticated,
        login,
        logout,
        authHeaders,
        setToken,
    };
}
