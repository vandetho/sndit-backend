import React from 'react';
import { styled, useTheme } from '@mui/material/styles';
import { Alert, AlertTitle, Grid, Link, Stack, useMediaQuery } from '@mui/material';
import { ContactForm, Information } from './components';
import { Trans, useTranslation } from 'react-i18next';
import { Helmet } from 'react-helmet';
import { Ticket } from '@interfaces';
import { useNavigate } from 'react-router';

const PREFIX = 'ContactUs';

const classes = {
    container: `${PREFIX}-container`,
    link: `${PREFIX}-link`,
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

    [`& .${classes.link}`]: {
        cursor: 'pointer',
    },
}));

interface ContactUsProps {}

const ContactUs = React.memo<ContactUsProps>(() => {
    const { t } = useTranslation();
    const navigate = useNavigate();
    const [ticket, setTicket] = React.useState<Ticket | undefined>(undefined);
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('md'));

    const onClick = React.useCallback(() => {
        if (ticket) {
            navigate(`/tickets/${ticket.token}`, { state: { ticket } });
        }
    }, [navigate, ticket]);

    const renderMessage = React.useCallback(() => {
        if (ticket) {
            return (
                <Stack data-aos="fade-in" sx={{ width: '100%' }} spacing={2}>
                    <Alert severity="success">
                        <AlertTitle>{t('success')}</AlertTitle>
                        <Trans i18nKey="ticket_created" values={{ token: ticket.token }} />{' '}
                        <Link className={classes.link} onClick={onClick}>
                            {t('click_to_view')}
                        </Link>
                    </Alert>
                </Stack>
            );
        }
        return null;
    }, [ticket, t, onClick]);

    return (
        <Root>
            <Helmet>
                <title>{t('contact_us_page_title', { ns: 'glossary' })}</title>
            </Helmet>
            <Grid className={classes.container} container spacing={2}>
                <Grid item sm={12}>
                    {renderMessage()}
                </Grid>
                <Grid item sm={12} md={5}>
                    <ContactForm onSubmit={setTicket} />
                </Grid>
                <Grid item sm={12} md={2} sx={{ py: isMobile ? 2 : 0 }} />
                <Grid item sm={12} md={5}>
                    <Information />
                </Grid>
            </Grid>
        </Root>
    );
});

export default ContactUs;
