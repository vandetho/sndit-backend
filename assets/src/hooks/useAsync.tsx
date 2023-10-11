import React from 'react';

export const useAsync = ({ asyncFunction }) => {
    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState(null);
    const [result, setResult] = React.useState(null);

    const execute = React.useCallback(
        (...params) => {
            setLoading(true);
            asyncFunction(...params)
                .then((response) => {
                    setResult(response);
                    setLoading(false);
                })
                .catch((e) => {
                    setError(e);
                    setLoading(false);
                });
        },
        [asyncFunction],
    );

    return { error, result, loading, execute };
};
