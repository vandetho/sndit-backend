import React from 'react';
import { Alert, IAlert } from '@components';

interface useAlertProps {
    duration?: number;
}

export const useAlert = ({ duration = 3000 }: useAlertProps = {}) => {
    const [alert, setAlert] = React.useState<IAlert>();

    const onClose = React.useCallback(() => setAlert(undefined), []);

    const AlertMessage = React.useCallback(
        () => <Alert open={!!alert} alert={alert} autoHideDuration={duration} onClose={onClose} />,
        [alert, duration, onClose],
    );

    return { AlertMessage, setAlert, onClose };
};
