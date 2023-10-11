import React from 'react';

export const ThemeContext = React.createContext<{
    mode: 'light' | 'dark' | 'system';
    onChangeMode: (mode: 'light' | 'dark' | 'system') => void;
}>({
    mode: 'dark',
    onChangeMode(): void {},
});

export const useTheme = () => {
    return React.useContext(ThemeContext);
};
