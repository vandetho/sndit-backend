import React from 'react';

export const LanguageContext = React.createContext<{
    language: 'en' | 'kh' | 'fr';
    onChangeLanguage: (language: 'en' | 'kh' | 'fr') => void;
}>({
    language: 'en',
    onChangeLanguage(): void {},
});

export const useLanguage = () => {
    return React.useContext(LanguageContext);
};
