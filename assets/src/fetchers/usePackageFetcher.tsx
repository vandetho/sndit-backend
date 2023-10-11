import React from 'react';
import { Package, ResponseSuccess } from '@interfaces';
import { axios } from '@utils';

export const usePackageFetcher = () => {
    const [state, setState] = React.useState<{
        item: Package | undefined;
        isLoading: boolean;
        errorMessage: string | undefined;
    }>({
        item: undefined,
        isLoading: false,
        errorMessage: undefined,
    });

    const fetch = React.useCallback(async (idOrToken: string | number) => {
        setState((prevState) => ({ ...prevState, isLoading: true }));
        try {
            const {
                data: { data },
            } = await axios.get<ResponseSuccess<Package>>(`/external/api/packages/${idOrToken}`);
            setState((prevState) => ({ ...prevState, item: data, isLoading: false }));
        } catch (error) {
            if ((error as any).response) {
                const {
                    response: { data },
                } = error as any;
                setState((prevState) => ({
                    ...prevState,
                    isLoading: false,
                    errorMessage: data.message || data.detail,
                }));
                return;
            }
            console.error(error);
        }
    }, []);

    return { ...state, fetch };
};
