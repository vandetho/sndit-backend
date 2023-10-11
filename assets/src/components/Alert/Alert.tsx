import React from 'react';
import { Alert as MuiAlert, AlertColor, Snackbar, SnackbarOrigin } from '@mui/material';
import { SnackbarCloseReason } from '@mui/material/Snackbar/Snackbar';

export interface IAlert {
    type: AlertColor;
    message: string;
}

interface AlertProps {
    anchorOrigin?: SnackbarOrigin;
    alert: IAlert | undefined;
    autoHideDuration?: number;
    open: boolean;
    variant?: 'standard' | 'filled' | 'outlined';
    onClose?: (alert: undefined) => void;
}

export const Alert: React.FC<AlertProps> = ({
    anchorOrigin = { vertical: 'top', horizontal: 'center' },
    alert,
    autoHideDuration = 3000,
    open,
    variant = 'filled',
    onClose,
}) => {
    const handleClick = React.useCallback(
        (event: React.SyntheticEvent<any> | Event, _: SnackbarCloseReason) => {
            event && event.stopPropagation();
            if (onClose) {
                onClose(undefined);
            }
        },
        [onClose],
    );

    const handleClose = React.useCallback(
        (event: React.SyntheticEvent<Element, Event>) => {
            event && event.stopPropagation();
            if (onClose) {
                onClose(undefined);
            }
        },
        [onClose],
    );

    if (alert === undefined) {
        return null;
    }

    return (
        <Snackbar anchorOrigin={anchorOrigin} open={open} autoHideDuration={autoHideDuration} onClose={handleClick}>
            <MuiAlert elevation={6} variant={variant} onClose={handleClose} severity={alert.type}>
                {alert.message}
            </MuiAlert>
        </Snackbar>
    );
};
