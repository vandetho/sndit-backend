import React from 'react';
import { FormHelperText, Typography } from '@mui/material';
import i18n from '@i18n';

export const hasError = (field: string, formState: { [key: string]: any }): boolean => {
    return formState.touched[field] && formState.errors !== undefined && formState.errors[field] !== undefined;
};

export const displayErrors = (
    field: string,
    formState: { [key: string]: any },
    withFormHelper = false,
): JSX.Element[] | JSX.Element | null | undefined => {
    if (hasError(field, formState)) {
        const errors = formState.errors[field].map((text: string, index: number) => (
            <Typography variant="caption" display="block" key={`text-field-errors-${field}-${index}`} color="inherit">
                {i18n.t(text, { ns: 'validation' }) as string}
            </Typography>
        ));

        if (withFormHelper) {
            return <FormHelperText id={`outlined-${field}-helper-text`}>{errors}</FormHelperText>;
        }
        return errors;
    }
    return null;
};
