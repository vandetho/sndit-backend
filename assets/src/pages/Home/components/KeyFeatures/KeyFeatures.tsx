import React from 'react';
import { styled } from '@mui/material/styles';
import { Grid, Typography } from '@mui/material';
import { useTranslation } from 'react-i18next';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faAddressBook, faMapLocationDot, faQrcode, faStore } from '@fortawesome/free-solid-svg-icons';

const PREFIX = 'KeyFeatures';

const classes = {
    mainContainer: `${PREFIX}-mainContainer`,
    container: `${PREFIX}-container`,
    boxContainer: `${PREFIX}-boxContainer`,
    boxIconContainer: `${PREFIX}-boxIconContainer`,
    boxIcon: `${PREFIX}-boxIcon`,
    boxTitle: `${PREFIX}-boxTitle`,
    boxBody: `${PREFIX}-boxBody`,
};

const Root = styled('div')(({ theme }) => ({
    [`&.${classes.mainContainer}`]: {
        backgroundColor: theme.palette.divider,
    },

    [`& .${classes.container}`]: {
        margin: '0 auto',
        paddingTop: 125,
        paddingBottom: 71,
        [theme.breakpoints.down('md')]: {
            paddingTop: 71,
            paddingLeft: 20,
            paddingRight: 20,
            width: '100%',
        },
        [theme.breakpoints.up('md')]: {
            paddingTop: 71,
            width: theme.breakpoints.values.md,
        },
        [theme.breakpoints.up('xl')]: {
            width: theme.breakpoints.values.lg,
        },
    },

    [`& .${classes.boxContainer}`]: {
        backgroundColor: theme.palette.divider,
        margin: theme.spacing(4),
    },

    [`& .${classes.boxIconContainer}`]: {
        margin: 'auto',
    },

    [`& .${classes.boxIcon}`]: {
        fontSize: 75,
        display: 'block',
        margin: 'auto',
    },

    [`& .${classes.boxTitle}`]: { fontWeight: 'bold', padding: 5 },
    [`& .${classes.boxBody}`]: { padding: 5 },
}));

interface KeyFeaturesProps {}

const KeyFeatures = React.memo<KeyFeaturesProps>(() => {
    const { t } = useTranslation();

    return (
        <Root className={classes.mainContainer}>
            <Grid container className={classes.container}>
                <Grid item md={12}>
                    <div data-aos="fade-right">
                        <Typography variant="h3" style={{ fontWeight: 'bold', paddingBottom: 70 }} align="center">
                            {t('key_features_title', { ns: 'glossary' })}
                        </Typography>
                    </div>
                </Grid>
                <Grid item md={12} spacing={4} container>
                    <Grid item lg={3} md={6} xs={12}>
                        <div data-aos="fade-down">
                            <FontAwesomeIcon icon={faStore} size="3x" />
                            <Typography variant="subtitle1" className={classes.boxTitle}>
                                {t('multiple_stores', { ns: 'glossary' })}
                            </Typography>
                            <Typography variant="subtitle1" className={classes.boxBody}>
                                {t('multiple_stores_content', { ns: 'glossary' })}
                            </Typography>
                        </div>
                    </Grid>
                    <Grid item lg={3} md={6} xs={12}>
                        <div data-aos="fade-up">
                            <FontAwesomeIcon icon={faAddressBook} size="3x" />
                            <Typography variant="subtitle1" className={classes.boxTitle}>
                                {t('address_book', { ns: 'glossary' })}
                            </Typography>
                            <Typography variant="subtitle1" className={classes.boxBody}>
                                {t('address_book_content', { ns: 'glossary' })}
                            </Typography>
                        </div>
                    </Grid>
                    <Grid item lg={3} md={6} xs={12}>
                        <div data-aos="fade-down">
                            <FontAwesomeIcon icon={faQrcode} size="3x" />
                            <Typography variant="subtitle1" className={classes.boxTitle}>
                                {t('qrcode', { ns: 'glossary' })}
                            </Typography>
                            <Typography variant="subtitle1" className={classes.boxBody}>
                                {t('qrcode_content', { ns: 'glossary' })}
                            </Typography>
                        </div>
                    </Grid>
                    <Grid item lg={3} md={6} xs={12}>
                        <div data-aos="fade-up">
                            <FontAwesomeIcon icon={faMapLocationDot} size="3x" />
                            <Typography variant="subtitle1" className={classes.boxTitle}>
                                {t('map_location_dot', { ns: 'glossary' })}
                            </Typography>
                            <Typography variant="subtitle1" className={classes.boxBody}>
                                {t('map_location_dot_content', { ns: 'glossary' })}
                            </Typography>
                        </div>
                    </Grid>
                </Grid>
            </Grid>
        </Root>
    );
});

export default KeyFeatures;
