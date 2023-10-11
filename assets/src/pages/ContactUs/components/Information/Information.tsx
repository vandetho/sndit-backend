import React from 'react';
import { CircularProgress, IconButton, InputAdornment, Paper, TextField, Typography } from '@mui/material';
import { useTranslation } from 'react-i18next';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSearch } from '@fortawesome/free-solid-svg-icons';
import { axios } from '@utils';
import { ResponseSuccess, Ticket } from '@interfaces';
import { useNavigate } from 'react-router';
import { useAlert } from '@hooks';

interface InformationProps {}

const Information = React.memo<InformationProps>(() => {
    const { t } = useTranslation();
    const navigate = useNavigate();
    const { setAlert, AlertMessage } = useAlert();
    const [state, setState] = React.useState({
        isLoading: false,
        code: '',
    });

    const onChange = React.useCallback((event: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = event.target;
        setState((prevState) => ({ ...prevState, [name]: value }));
    }, []);

    const handleSearch = React.useCallback(() => {
        setState((prevState) => ({ ...prevState, isLoading: true }));
        axios
            .get<ResponseSuccess<Ticket>>(`/external/api/tickets/${state.code}`)
            .then(({ data: { data } }) => {
                navigate(`/tickets/${data.token}`, { state: { ticket: data } });
            })
            .catch((reason) => {
                if (reason.response) {
                    const { data } = reason.response;
                    setAlert({ type: 'error', message: data.message || data.detail });
                }
                console.error(reason);
                setState((prevState) => ({ ...prevState, isLoading: false }));
            });
        setState((prevState) => ({ ...prevState, code: '' }));
    }, [navigate, setAlert, state.code]);

    const onKeyUp = React.useCallback(
        (event: React.KeyboardEvent<HTMLInputElement>) => {
            if (event.key === 'Enter') {
                handleSearch();
            }
        },
        [handleSearch],
    );

    return (
        <React.Fragment>
            <div data-aos="fade-left">
                <Typography variant="h6">{t('contact_information_title', { ns: 'glossary' })}</Typography>
                <Typography variant="body1">{t('contact_information_content', { ns: 'glossary' })}</Typography>
                <Paper
                    sx={{
                        mt: 5,
                        p: 5,
                        borderRadius: 5,
                    }}
                >
                    <TextField
                        autoFocus
                        fullWidth
                        name="code"
                        label={t('ticket_number')}
                        value={state.code}
                        variant="outlined"
                        inputMode="search"
                        onKeyUp={onKeyUp}
                        onChange={onChange}
                        InputProps={{
                            endAdornment: (
                                <InputAdornment position="end">
                                    {state.isLoading ? (
                                        <CircularProgress size={30} />
                                    ) : (
                                        <IconButton onClick={handleSearch}>
                                            <FontAwesomeIcon icon={faSearch} />
                                        </IconButton>
                                    )}
                                </InputAdornment>
                            ),
                        }}
                    />
                </Paper>
            </div>
            <AlertMessage />
        </React.Fragment>
    );
});

export default Information;
