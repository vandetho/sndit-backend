import { createTheme, Theme } from '@mui/material/styles';

import getPalette from './palette';

const getTheme = (isDark: boolean): Theme =>
    createTheme({
        palette: getPalette(isDark),
        typography: {
            fontFamily: [
                'Rubik',
                '"Helvetica Neue"',
                '-apple-system',
                'BlinkMacSystemFont',
                '"Segoe UI"',
                'Roboto',
                'Arial',
                'sans-serif',
                '"Apple Color Emoji"',
                '"Segoe UI Emoji"',
                '"Segoe UI Symbol"',
            ].join(','),
        },
    });

export default getTheme;
