import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import Backend from 'i18next-xhr-backend';

i18n.use(Backend)
    .use(initReactI18next)
    .init({
        debug: process.env.NODE_ENV !== 'production',
        ns: ['common', 'validation', 'glossary'],
        defaultNS: 'common',
        lng: 'en',
        fallbackLng: 'en',
        supportedLngs: ['en', 'kh', 'fr'],
        interpolation: {
            escapeValue: false, // not needed for react as it escapes by default
        },
        backend: {
            loadPath: '/locales/{{lng}}/{{ns}}.json',
        },
        react: {
            transSupportBasicHtmlNodes: true,
            transKeepBasicHtmlNodesFor: ['br', 'strong', 'i', 'p'],
            transWrapTextNodes: '',
        },
    });

export default i18n;
