import React from 'react';
import { styled } from '@mui/material/styles';
import { useLocation, useParams } from 'react-router';
import { ResponseSuccess, Ticket, TicketMessage } from '@interfaces';
import { Box, CircularProgress, Grid, Typography } from '@mui/material';
import { useTranslation } from 'react-i18next';
import { axios } from '@utils';
import Lottie from 'lottie-react';
import { TicketDetail, TicketMessages } from './components';
import { Helmet } from 'react-helmet';
import Lottie404 from '@lotties/404.json';

const PREFIX = 'Ticket';

const classes = {
    container: `${PREFIX}-container`,
};

const Root = styled('div')(({ theme }) => ({
    [`& .${classes.container}`]: {
        margin: '0 auto',
        paddingTop: 125,
        paddingBottom: 125,
        [theme.breakpoints.down('lg')]: {
            paddingTop: 71,
            paddingLeft: 20,
            paddingRight: 20,
        },
        [theme.breakpoints.up('lg')]: {
            width: theme.breakpoints.values.md,
        },
        [theme.breakpoints.up('xl')]: {
            width: theme.breakpoints.values.lg,
        },
    },
}));

interface TicketProps {}

const Ticket = React.memo<TicketProps>(() => {
    const { t } = useTranslation();
    const location = useLocation();

    const { token } = useParams<{ token: string }>();
    const [state, setState] = React.useState<{
        isLoading: boolean;
        ticket: Ticket | undefined;
        messages: TicketMessage[];
        totalRows: number;
    }>(() => {
        const { ticket } = (location.state as { ticket: Ticket }) || { ticket: undefined };
        return {
            isLoading: !(ticket && ticket.token === token),
            ticket,
            messages: [],
            totalRows: 0,
        };
    });

    React.useEffect(() => {
        if (state.ticket && state.ticket.token === token) {
            return;
        }
        axios
            .get<ResponseSuccess<Ticket>>(`/external/api/tickets/${token}`)
            .then(({ data: { data } }) => {
                setState((prevState) => ({ ...prevState, isLoading: false, ticket: data }));
            })
            .catch((reason) => {
                console.error(reason);
                setState((prevState) => ({ ...prevState, isLoading: false }));
            });
    }, [state.ticket, token]);

    React.useEffect(() => {
        if (state.ticket && state.ticket.token === token) {
            axios
                .get<ResponseSuccess<{ messages: TicketMessage[]; totalRows: number }>>(
                    `/external/api/tickets/${token}/messages`,
                )
                .then(({ data: { data } }) => {
                    setState((prevState) => ({
                        ...prevState,
                        isLoading: false,
                        messages: data.messages,
                        totalRows: data.totalRows,
                    }));
                })
                .catch((reason) => {
                    console.error(reason);
                    setState((prevState) => ({ ...prevState, isLoading: false }));
                });
        }
    }, [state.ticket, token]);

    const onSubmit = React.useCallback((message: TicketMessage) => {
        setState((prevState) => ({
            ...prevState,
            messages: [message, ...prevState.messages],
            totalRows: prevState.totalRows + 1,
        }));
    }, []);

    const renderContent = React.useCallback(() => {
        if (state.isLoading) {
            return (
                <Grid item xs={12}>
                    <Box sx={{ display: 'flex', justifyContent: 'center', marginTop: 30 }}>
                        <CircularProgress size={100} />
                    </Box>
                </Grid>
            );
        }
        if (state.ticket) {
            return (
                <Grid item xs={12} container>
                    <Grid item xs={12}>
                        <TicketDetail ticket={state.ticket} onSubmit={onSubmit} />
                    </Grid>
                </Grid>
            );
        }
        return (
            <Grid item xs={12}>
                <Box sx={{ display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
                    <Lottie
                        loop
                        animationData={Lottie404}
                        autoplay
                        rendererSettings={{
                            preserveAspectRatio: 'xMidYMid slice',
                        }}
                        height={400}
                        width={400}
                    />
                    <Typography variant="h3" component="p">
                        {t('ticket_not_found')}
                    </Typography>
                </Box>
            </Grid>
        );
    }, [onSubmit, state.isLoading, state.ticket, t]);

    return (
        <Root>
            <Helmet>
                <title>{t('ticket_page_title', { ns: 'glossary', token })}</title>
            </Helmet>
            <Grid container className={classes.container}>
                {renderContent()}
                <TicketMessages messages={state.messages} />
            </Grid>
        </Root>
    );
});

export default Ticket;
