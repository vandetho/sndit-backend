import React from 'react';
import {
    Button,
    Card,
    CardActions,
    CardContent,
    CardHeader,
    IconButton,
    InputAdornment,
    Paper,
    TextField,
    Typography,
} from '@mui/material';
import { styled, useTheme } from '@mui/material/styles';
import { format } from 'date-fns';
import { useTranslation } from 'react-i18next';
import { DATETIME_FORMAT } from '@config';
import { ResponseSuccess, Ticket, TicketMessage } from '@interfaces';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCircleInfo, faEnvelope, faImages, faTimes } from '@fortawesome/free-solid-svg-icons';
import { axios } from '@utils';
import { useAlert } from '@hooks';

const PREFIX = 'TicketDetail';

const classes = {
    imagePreview: `${PREFIX}-imagePreview`,
    image: `${PREFIX}-image`,
    removeButton: `${PREFIX}-removeButton`,
};

const Root = styled('div')(() => ({
    [`& .${classes.imagePreview}`]: {
        position: 'relative',
        marginRight: 15,
        fontSize: 0,
        maxWidth: 90,
        maxHeight: 90,
    },

    [`& .${classes.image}`]: {
        cursor: 'pointer',
        margin: 0,
        maxHeight: 90,
        maxWidth: 90,
    },

    [`& .${classes.removeButton}`]: {
        position: 'absolute',
        top: 1,
        right: 1,
        cursor: 'pointer',
        background: 'hsla(0,0%,100%,.5)',
        width: 25,
        height: 25,
    },
}));

const Input = styled('input')({
    display: 'none',
});

interface TicketDetailProps {
    ticket: Ticket;
    onSubmit: (message: TicketMessage) => void;
}

const TicketDetail = React.memo<TicketDetailProps>(({ ticket, onSubmit }) => {
    const { t } = useTranslation();
    const theme = useTheme();
    const { setAlert, AlertMessage } = useAlert();
    const [state, setState] = React.useState<{ images: File[]; message: string; isLoading: boolean }>({
        images: [],
        message: '',
        isLoading: false,
    });

    const marking = React.useMemo(() => {
        return Object.keys(ticket.marking).map((marking) => t(marking));
    }, [t, ticket.marking]);

    const onRemove = React.useCallback(
        (index: number) => {
            const images = [...state.images];
            images.splice(index, 1);
            setState((prevState) => ({ ...prevState, images }));
        },
        [state.images],
    );

    const renderFiles = React.useCallback(() => {
        if (state.images.length > 0) {
            return (
                <Paper data-aos="fade-in" sx={{ borderRadius: 5, p: 2, mt: 2, display: 'flex', flexDirection: 'row' }}>
                    {state.images.map((image, index) => (
                        <div key={`ticket-message-images-item-${index}`} className={classes.imagePreview}>
                            <img
                                src={URL.createObjectURL(image)}
                                alt="Ticket message attachments"
                                className={classes.image}
                            />
                            <IconButton className={classes.removeButton} onClick={() => onRemove(index)} size="small">
                                <FontAwesomeIcon color="#000000" icon={faTimes} />
                            </IconButton>
                        </div>
                    ))}
                </Paper>
            );
        }
        return null;
    }, [onRemove, state.images]);

    const handleChange = React.useCallback((event: React.ChangeEvent<HTMLInputElement>) => {
        setState((prevState) => ({ ...prevState, message: event.target.value }));
    }, []);

    const handleChangeFiles = React.useCallback((event: React.ChangeEvent<HTMLInputElement>) => {
        const images: File[] = [];
        const files = event.target.files;
        if (files) {
            for (let i = 0; i < files.length; i++) {
                const file = files.item(i);
                if (file) {
                    images.push(file);
                }
            }
        }
        setState((prevState) => ({ ...prevState, images: [...prevState.images, ...images] }));
    }, []);

    const handleSubmit = React.useCallback(() => {
        setState((prevState) => ({ ...prevState, isLoading: true }));
        const formData = new FormData();
        formData.append('sndit_ticket_message[content]', state.message);
        state.images.forEach((image, index) => {
            formData.append(`sndit_ticket_message[attachments][${index}][file]`, image, image.name);
        });

        axios
            .post<ResponseSuccess<TicketMessage>>(`/external/api/tickets/${ticket.token}/messages`, formData)
            .then(({ data: { data, message } }) => {
                setAlert({ type: 'success', message: message || '' });
                setState((prevState) => ({ ...prevState, isLoading: false, message: '', images: [] }));
                onSubmit(data);
            })
            .catch((reason) => {
                if (reason.response) {
                    const { message, detail } = reason.response.data;
                    setAlert({ type: 'error', message: message || detail });
                }
                setState((prevState) => ({ ...prevState, isLoading: false }));
            });
    }, [onSubmit, setAlert, state.images, state.message, ticket.token]);

    return (
        <Root>
            <Card sx={{ borderRadius: 5, p: 2 }}>
                <CardHeader
                    title={ticket.name}
                    subheader={`${t('status')}: ${marking}`}
                    action={
                        <IconButton aria-label="settings">
                            <FontAwesomeIcon icon={faCircleInfo} />
                        </IconButton>
                    }
                />
                <CardContent>
                    <Typography variant="body1">{ticket.content}</Typography>
                    <Typography variant="caption" component="p" color={theme.palette.text.secondary} sx={{ mt: 2 }}>
                        {format(new Date(ticket.createdAt), DATETIME_FORMAT)}
                    </Typography>
                </CardContent>
                <CardActions>
                    <label htmlFor="contained-button-file">
                        <Input
                            accept="image/*"
                            id="contained-button-file"
                            multiple
                            type="file"
                            onChange={handleChangeFiles}
                        />
                        <IconButton component="span" aria-label="upload attachments" sx={{ mx: 2 }}>
                            <FontAwesomeIcon icon={faImages} />
                        </IconButton>
                    </label>
                    <TextField
                        fullWidth
                        multiline
                        rows={3}
                        placeholder={t('send_message')}
                        value={state.message}
                        onChange={handleChange}
                        InputProps={{
                            endAdornment: (
                                <InputAdornment position="end">
                                    <Button
                                        onClick={handleSubmit}
                                        disabled={!state.message}
                                        endIcon={<FontAwesomeIcon icon={faEnvelope} />}
                                    >
                                        {t('send')}
                                    </Button>
                                </InputAdornment>
                            ),
                        }}
                    />
                </CardActions>
            </Card>
            {renderFiles()}
            <AlertMessage />
        </Root>
    );
});

export default TicketDetail;
