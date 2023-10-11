import React from 'react';
import { CircularProgress, IconButton, InputAdornment, Paper, TextField, Typography } from '@mui/material';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSearch } from '@fortawesome/free-solid-svg-icons';
import { useTranslation } from 'react-i18next';

interface SearchBarProps {
    isLoading: boolean;
    onSearch: (code: string) => void;
}

const SearchBarComponent: React.FunctionComponent<SearchBarProps> = ({ isLoading, onSearch }) => {
    const { t } = useTranslation();
    const [state, setState] = React.useState({
        code: '',
    });

    const onChange = React.useCallback((event: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = event.target;
        setState((prevState) => ({ ...prevState, [name]: value }));
    }, []);

    const handleSearch = React.useCallback(() => {
        onSearch(state.code);
        setState((prevState) => ({ ...prevState, code: '' }));
    }, [onSearch, state.code]);

    const onKeyUp = React.useCallback(
        (event: React.KeyboardEvent<HTMLInputElement>) => {
            if (event.key === 'Enter') {
                handleSearch();
            }
        },
        [handleSearch],
    );

    return (
        <div data-aos="fade-in">
            <Paper
                sx={{
                    p: 5,
                    borderRadius: 5,
                }}
            >
                <TextField
                    autoFocus
                    fullWidth
                    name="code"
                    label={t('tracking_code')}
                    value={state.code}
                    variant="outlined"
                    inputMode="search"
                    onKeyUp={onKeyUp}
                    onChange={onChange}
                    InputProps={{
                        endAdornment: (
                            <InputAdornment position="end">
                                {isLoading ? (
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
            <Typography variant="h6" textAlign="center" sx={{ py: 2 }}>
                {t('tracking_code_detail', { ns: 'glossary' })}
            </Typography>
        </div>
    );
};

const SearchBar = React.memo(SearchBarComponent);

export default SearchBar;
