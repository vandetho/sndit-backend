import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import StylesEngineProvider from '@mui/material/StyledEngineProvider';
import { useMediaQuery } from '@mui/material';
import { ThemeProvider } from '@mui/material/styles';
import { useTranslation } from 'react-i18next';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';

import './i18n';
import Route from './Route';
import { LanguageContext, ThemeContext } from '@context';
import * as serviceWorkerRegistration from './serviceWorkerRegistration';
import getTheme from './theme';
import '../styles/Application.css';

interface ApplicationProps {}

const Application: React.FC<ApplicationProps> = () => {
    const { i18n } = useTranslation();
    const prefersDarkMode = useMediaQuery(`(prefers-color-scheme: dark)`);
    const [language, setLanguage] = React.useState<'en' | 'kh' | 'fr'>(() => {
        const language = localStorage.getItem('language');
        if (language) {
            return language as 'en' | 'kh' | 'fr';
        }
        return 'en';
    });
    const [mode, setMode] = React.useState<'light' | 'dark' | 'system'>(() => {
        const mode = localStorage.getItem('theme');
        if (mode) {
            return mode as 'light' | 'dark' | 'system';
        }
        return prefersDarkMode ? 'dark' : 'light';
    });

    React.useEffect(() => {
        i18n.changeLanguage(language);
    }, [i18n, language]);

    const theme = React.useMemo(() => getTheme(mode === 'dark'), [mode]);

    const onChangeMode = React.useCallback((mode: 'light' | 'dark' | 'system') => {
        localStorage.setItem('theme', mode);
        setMode(mode);
    }, []);

    const onChangeLanguage = React.useCallback(async (language: 'en' | 'kh' | 'fr') => {
        localStorage.setItem('language', language);
        setLanguage(language);
    }, []);

    return (
        <StylesEngineProvider injectFirst>
            <ThemeProvider theme={theme}>
                <LocalizationProvider dateAdapter={AdapterDateFns}>
                    <ThemeContext.Provider value={{ mode, onChangeMode }}>
                        <LanguageContext.Provider value={{ language, onChangeLanguage }}>
                            <BrowserRouter>
                                <Route />
                            </BrowserRouter>
                        </LanguageContext.Provider>
                    </ThemeContext.Provider>
                </LocalizationProvider>
            </ThemeProvider>
        </StylesEngineProvider>
    );
};

export default Application;

const root = createRoot(document.querySelector('#sndit_app') as Element);

root.render(
    <React.Suspense fallback="loading">
        <Application />
    </React.Suspense>,
);

serviceWorkerRegistration.register();
