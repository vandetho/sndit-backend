import React from 'react';
import clsx from 'clsx';
import makeStyles from '@mui/styles/makeStyles';
import { Button, Grid, Theme, Typography } from '@mui/material';
import { CURRENT_YEAR } from '@config';
import { gradients } from '@utils';
import { useTranslation } from 'react-i18next';
import { Link as RouterLink } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faEnvelope, faHome, faMapLocationDot } from '@fortawesome/free-solid-svg-icons';
import Logo from '@images/white_logo_with_text.png';

const useStyles = makeStyles((theme: Theme) => ({
    root: {
        marginTop: 'auto',
        height: 200,
        background: gradients.primary,
        padding: theme.spacing(4),
        [theme.breakpoints.down('md')]: {
            height: 400,
        },
    },
    container: {},
    logoImage: {
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
    const classes = useStyles();

    return (
        <div {...rest} className={clsx(classes.root, className)}>
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
        </div>
    );
};

export default Footer;
