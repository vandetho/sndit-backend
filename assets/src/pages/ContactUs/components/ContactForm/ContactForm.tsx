import React from 'react';
import { Button, CircularProgress, TextField } from '@mui/material';
import { useTranslation } from 'react-i18next';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { createStyles, makeStyles } from '@mui/styles';
import { axios } from '@utils';
import { faEnvelope } from '@fortawesome/free-solid-svg-icons';
import { ResponseSuccess, Ticket } from '@interfaces';
import { useAlert } from '@hooks';

const useStyles = makeStyles(() =>
    createStyles({
        button: { marginTop: 20 },
    }),
);

const DEFAULT_VALUE = {
    name: '',
    email: '',
    phoneNumber: '',
    content: '',
    dispatch: false,
    nameMessage: '',
    emailMessage: '',
    phoneNumberMessage: '',
    contentMessage: '',
};

interface ContactFormProps {
    onSubmit: (ticket: Ticket) => void;
}

const ContactForm = React.memo<ContactFormProps>(({ onSubmit }) => {
    const { t } = useTranslation();
    const classes = useStyles();
    const { setAlert, AlertMessage } = useAlert();
    const [state, setState] = React.useState(DEFAULT_VALUE);

    const onChange = React.useCallback((event: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = event.target;
        setState((prevState) => ({ ...prevState, [name]: value }));
    }, []);

    const isFormValid = React.useCallback(() => {
        let valid = true;
        let message = '';
        let nameMessage = '';
        let emailMessage = '';
        let phoneNumberMessage = '';
        let contentMessage = '';
        if (!state.name) {
            nameMessage = t('name_required', { ns: 'validation' });
            valid = false;
        }
        if (!state.email && !state.phoneNumber) {
            emailMessage = t('email_or_phone_number_required', { ns: 'validation' });
            phoneNumberMessage = t('email_or_phone_number_required', { ns: 'validation' });
            valid = false;
        }
        if (!state.content) {
            contentMessage = t('content_required', { ns: 'validation' });
            valid = false;
        }
        if (!valid) {
            setState((prevState) => ({
                ...prevState,
                message,
                nameMessage,
                contentMessage,
                emailMessage,
                phoneNumberMessage,
            }));
        }
        return valid;
    }, [state.content, state.email, state.name, state.phoneNumber, t]);

    const onSend = React.useCallback(() => {
        if (isFormValid()) {
            setState((prevState) => ({
                ...prevState,
                contentMessage: '',
                phoneNumberMessage: '',
                nameMessage: '',
                emailMessage: '',
                dispatch: true,
            }));
            axios
                .post<ResponseSuccess<Ticket>>('/external/api/tickets', {
                    name: state.name,
                    email: state.email,
                    phoneNumber: state.phoneNumber,
                    content: state.content,
                })
                .then(({ data: { data } }) => {
                    onSubmit(data);
                    setState((prevState) => ({ ...prevState, ...DEFAULT_VALUE }));
                })
                .catch((reason) => {
                    if (reason.response) {
                        const { data } = reason.response;
                        setAlert({ type: 'error', message: data.message || data.detail });
                    }
                    console.error(reason);
                    setState((prevState) => ({ ...prevState, dispatch: false }));
                });
        }
    }, [isFormValid, onSubmit, setAlert, state.content, state.email, state.name, state.phoneNumber]);

    return (
        <React.Fragment>
            <div data-aos="fade-right">
                <TextField
                    fullWidth
                    label={t('name')}
                    name="name"
                    value={state.name}
                    error={!!state.nameMessage}
                    helperText={state.nameMessage && state.nameMessage}
                    onChange={onChange}
                    variant="standard"
                />
                <TextField
                    fullWidth
                    label={t('email')}
                    name="email"
                    value={state.email}
                    error={!!state.emailMessage}
                    helperText={state.emailMessage && state.emailMessage}
                    onChange={onChange}
                    variant="standard"
                />
                <TextField
                    fullWidth
                    label={t('phone_number')}
                    name="phoneNumber"
                    value={state.phoneNumber}
                    error={!!state.phoneNumberMessage}
                    helperText={state.phoneNumberMessage && state.phoneNumberMessage}
                    onChange={onChange}
                    variant="standard"
                />
                <TextField
                    fullWidth
                    label={t('content')}
                    name="content"
                    value={state.content}
                    error={!!state.contentMessage}
                    helperText={state.contentMessage && state.contentMessage}
                    onChange={onChange}
                    multiline
                    rows={7}
                    variant="standard"
                />
                <Button
                    fullWidth
                    variant="contained"
                    className={classes.button}
                    disabled={state.dispatch}
                    onClick={onSend}
                    endIcon={state.dispatch ? <CircularProgress size={20} /> : <FontAwesomeIcon icon={faEnvelope} />}
                >
                    {t('send')}
                </Button>
            </div>
            <AlertMessage />
        </React.Fragment>
    );
});

export default ContactForm;
