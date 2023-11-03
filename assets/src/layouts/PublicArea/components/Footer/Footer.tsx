import React from 'react';
import { styled } from '@mui/material/styles';
import clsx from 'clsx';
import { Button, Grid, IconButton, Link, Stack, Typography } from '@mui/material';
import { CURRENT_YEAR } from '@config';
import { gradients } from '@utils';
import { useTranslation } from 'react-i18next';
import { Link as RouterLink } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faEnvelope, faHome, faMapLocationDot } from '@fortawesome/free-solid-svg-icons';
import Logo from '@images/white_logo_with_text.png';
import { GitHub } from '@mui/icons-material';

const PREFIX = 'Footer';

const classes = {
    root: `${PREFIX}-root`,
    container: `${PREFIX}-container`,
    logoImage: `${PREFIX}-logoImage`,
};

const Root = styled('div')(({ theme }) => ({
    [`&.${classes.root}`]: {
        marginTop: 'auto',
        height: 200,
        background: gradients.primary,
        padding: theme.spacing(4),
        [theme.breakpoints.down('md')]: {
            height: 400,
        },
    },

    [`& .${classes.container}`]: {},

    [`& .${classes.logoImage}`]: {
        alignContent: 'center',
        height: 64,
        [theme.breakpoints.down('md')]: {
            height: 48,
        },
    },
}));

interface FooterProps {
    className?: string;
}

const Footer: React.FC<FooterProps> = (props) => {
    const { className, ...rest } = props;
    const { t } = useTranslation();

    return (
        <Root {...rest} className={clsx(classes.root, className)}>
            <Grid container spacing={2} className={classes.container}>
                <Grid item lg={4} md={6}>
                    <img src={Logo} className={classes.logoImage} alt="Sndit" />
                    <Typography variant="body1">{t('footer_content', { ns: 'glossary' })}</Typography>
                </Grid>
                <Grid item lg={4} md={6} container>
                    <Grid item xs={12}>
                        <Button
                            color="inherit"
                            component={RouterLink}
                            to="/"
                            startIcon={<FontAwesomeIcon icon={faHome} />}
                        >
                            {t('home')}
                        </Button>
                    </Grid>
                    <Button
                        color="inherit"
                        component={RouterLink}
                        to="/tracking"
                        startIcon={<FontAwesomeIcon icon={faMapLocationDot} />}
                    >
                        {t('tracking')}
                    </Button>
                    <Grid item xs={12}>
                        <Button
                            color="inherit"
                            component={RouterLink}
                            to="/contact-us"
                            startIcon={<FontAwesomeIcon icon={faEnvelope} />}
                        >
                            {t('contact_us')}
                        </Button>
                    </Grid>
                    <Grid item xs={12}>
                        <Stack direction="row" justifyContent="space-between">
                            <IconButton component="a" href="https://github.com/vandetho/sndit-backend.git">
                                <GitHub />
                            </IconButton>
                            <Stack spacing={1}>
                                <Typography variant="h6">Sponsored By: </Typography>
                                <Link href="https://kromb.io">
                                    <img
                                        src={require('@images/kromb_logo.png')}
                                        alt="kromb is a team management system"
                                    />
                                </Link>
                            </Stack>
                        </Stack>
                    </Grid>
                </Grid>
                <Grid item lg={4} md={6}>
                    <Typography variant="subtitle1">{t('contact')}</Typography>
                    {t('contact_detail', { ns: 'glossary' })
                        .split('\n')
                        .map((line, index) => (
                            <Typography variant="body1" key={`contact-line-${index}`}>
                                {line}
                            </Typography>
                        ))}
                </Grid>
                <Grid item md={12}>
                    <Typography variant="body1">{CURRENT_YEAR} &copy; Sndit All Right reserved</Typography>
                </Grid>
            </Grid>
        </Root>
    );
};

export default Footer;
