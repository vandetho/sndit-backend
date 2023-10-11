import React from 'react';

export const useTrackErrors = () => {
    const [errors, handleErrors] = React.useState<{ [key: string]: any }>({});

    const setErrors = (errsArray) => {
        const newErrors = { ...errors };
        errsArray.forEach(({ key, value }) => {
            newErrors[key] = value;
        });

        handleErrors(newErrors);
    };

    const clearErrors = () => {
        setErrors({});
    };

    return { errors, setErrors, clearErrors };
};
