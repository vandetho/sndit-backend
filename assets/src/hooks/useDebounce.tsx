import React from 'react';

export const useDebounce = (func, wait) => {
    const timeout = React.useRef<any>(undefined);

    return React.useCallback(
        (...args) => {
            const later = () => {
                clearTimeout(timeout.current);
                func(...args);
            };

            clearTimeout(timeout.current);
            timeout.current = setTimeout(later, wait);
        },
        [func, wait],
    );
};
