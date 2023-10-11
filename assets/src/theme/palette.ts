import { red } from '@mui/material/colors';
import { PaletteOptions } from '@mui/material/styles';

const text = '#FFFFFF';
const primaryText = '#FFFFFF';
const primaryDark = '#CE5E77';
export const primaryMain = '#F16E8C';
const primaryLight = '#F38099';
const secondaryText = '#FFFFFF';
const secondaryDark = '#E13E33';
export const secondaryMain = '#EF4136';
const secondaryLight = '#F8493E';

const getPalette = (isDark: boolean): PaletteOptions => {
    if (isDark) {
        return {
            primary: {
                contrastText: primaryText,
                dark: primaryDark,
                main: primaryMain,
                light: primaryLight,
            },
            secondary: {
                contrastText: secondaryText,
                dark: secondaryDark,
                main: secondaryMain,
                light: secondaryLight,
            },
            error: {
                contrastText: text,
                dark: red[900],
                main: red[700],
                light: red[500],
            },
            background: {
                default: '#121212',
                paper: '#121212',
            },
            text: {
                primary: '#FFFFFF',
                secondary: 'rgba(255, 255, 255, 0.7)',
                disabled: 'rgba(255, 255, 255, 0.5)',
            },
            action: {
                active: '#FFFFFF',
                hover: 'rgba(255, 255, 255, 0.08)',
                selected: 'rgba(255, 255, 255, 0.16)',
                disabled: 'rgba(255, 255, 255, 0.3)',
                disabledBackground: 'rgba(255, 255, 255, 0.12)',
            },
            divider: 'rgba(255, 255, 255, 0.12)',
            mode: 'dark',
        };
    }
    return {
        primary: {
            contrastText: primaryText,
            dark: primaryDark,
            main: primaryMain,
            light: primaryLight,
        },
        secondary: {
            contrastText: secondaryText,
            dark: secondaryDark,
            main: secondaryMain,
            light: secondaryLight,
        },
        error: {
            contrastText: text,
            dark: red[900],
            main: red[700],
            light: red[500],
        },
        background: {
            default: '#FFFFFF',
            paper: '#FFFFFF',
        },
        text: {
            primary: 'rgba(0, 0, 0, 0.87)',
            secondary: 'rgba(0, 0, 0, 0.6)',
            disabled: 'rgba(0, 0, 0, 0.38)',
        },
        action: {
            active: 'rgba(0, 0, 0, 0.54)',
            hover: 'rgba(0, 0, 0, 0.04)',
            selected: 'rgba(0, 0, 0, 0.08)',
            disabled: 'rgba(0, 0, 0, 0.26)',
            disabledBackground: 'rgba(0, 0, 0, 0.12)',
        },
        divider: 'rgba(0, 0, 0, 0.12)',
        mode: 'light',
    };
};

export default getPalette;
