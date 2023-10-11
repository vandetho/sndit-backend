import React from 'react';
import { Package, PackageHistory, ResponseSuccess } from '@interfaces';
import { axios } from '@utils';

export const usePackageHistoriesFetcher = (item: Package) => {
    const [state, setState] = React.useState<{
        histories: PackageHistory[];
        offset: number;
        limit: number;
        totalRows: number;
        isLoading: boolean;
        isLoadingMore: boolean;
        errorMessage: string | undefined;
    }>({
        histories: [],
        totalRows: 0,
        limit: 10,
        offset: 0,
        isLoading: false,
        isLoadingMore: false,
        errorMessage: undefined,
    });

    const fetch = React.useCallback(async () => {
        if (item) {
            setState((prevState) => ({ ...prevState, isLoading: true, errorMessage: undefined }));
            try {
                const {
                    data: { data },
                } = await axios.get<ResponseSuccess<{ histories: PackageHistory[]; totalRows: number }>>(
                    `/external/api/packages/${item.token}/histories`,
                    {
                        params: {
                            offset: 0,
                            limit: state.limit,
                        },
                    },
                );
                setState((prevState) => ({
                    ...prevState,
                    ...data,
                    isLoading: false,
                    offset: prevState.limit,
                }));
            } catch (error) {
                let errorMessage = String(error);
                if ((error as any).response) {
                    const {
                        response: { data },
                    } = error as any;
                    errorMessage = data.message || data.detail;
                }
                setState((prevState) => ({ ...prevState, isLoading: false, errorMessage }));
            }
        }
    }, [item, state.limit]);

    const fetchMore = React.useCallback(async () => {
        if (item && state.offset < state.totalRows) {
            setState((prevState) => ({ ...prevState, isLoadingMore: true, errorMessage: undefined }));
            try {
                const {
                    data: { data },
                } = await axios.get<ResponseSuccess<{ histories: PackageHistory[]; totalRows: number }>>(
                    `/external/api/packages/${item.token}/histories`,
                    {
                        params: {
                            offset: state.offset,
                            limit: state.limit,
                        },
                    },
                );
                setState((prevState) => ({
                    ...prevState,
                    histories: [...prevState.histories, ...data.histories],
                    totalRows: data.totalRows,
                    offset: prevState.limit + prevState.offset,
                    isLoadingMore: false,
                }));
            } catch (error) {
                let errorMessage = String(error);
                if ((error as any).response) {
                    const {
                        response: { data },
                    } = error as any;
                    errorMessage = data.message || data.detail;
                }
                setState((prevState) => ({ ...prevState, isLoading: false, errorMessage }));
            }
        }
    }, [item, state.limit, state.offset, state.totalRows]);

    return { ...state, fetch, fetchMore };
};
