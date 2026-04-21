export type ApiValidationErrors = Record<string, string[]>;

type ErrorResponseData = {
    message?: string;
    errors?: ApiValidationErrors;
};

type ErrorResponse = {
    status?: number;
    data?: ErrorResponseData;
};

type ApiLikeError = {
    response?: ErrorResponse;
};

export function getApiErrorMessage(
    error: unknown,
    fallback = 'Произошла ошибка',
): string {
    if (typeof error === 'object' && error !== null && 'response' in error) {
        const apiError = error as ApiLikeError;

        return apiError.response?.data?.message || fallback;
    }

    return fallback;
}

export function getApiErrorStatus(error: unknown): number | undefined {
    if (typeof error === 'object' && error !== null && 'response' in error) {
        const apiError = error as ApiLikeError;

        return apiError.response?.status;
    }

    return undefined;
}

export function getApiValidationErrors(error: unknown): ApiValidationErrors {
    if (typeof error === 'object' && error !== null && 'response' in error) {
        const apiError = error as ApiLikeError;

        return apiError.response?.data?.errors || {};
    }

    return {};
}
