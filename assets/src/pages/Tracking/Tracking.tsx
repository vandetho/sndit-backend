import React from 'react';
import { Box, CircularProgress, Grid, Paper } from '@mui/material';
import { PackageDeliveredMap, PackageDetail, PackageHistories, SearchBar } from './components';
import { createStyles, makeStyles } from '@mui/styles';
import { Theme, useTheme } from '@mui/material/styles';
import { Helmet } from 'react-helmet';
import { useTranslation } from 'react-i18next';
import { useLocation } from 'react-router';
import { QRCodeSVG } from 'qrcode.react';
import { usePackageFetcher } from '@fetchers';
import { useAlert } from '@hooks';

const useStyles = makeStyles((theme: Theme) =>
    createStyles({
        container: {
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
    }),
);

interface TrackingProps {}

const TrackingComponent: React.FunctionComponent<TrackingProps> = () => {
    const classes = useStyles();
    const search = useLocation().search;
    const trackingNumber = new URLSearchParams(search).get('tracking_number');
    const { t } = useTranslation();
    const theme = useTheme();
    const { setAlert, AlertMessage } = useAlert();
    const { item, isLoading, errorMessage, fetch } = usePackageFetcher();

    React.useEffect(() => {
        if (errorMessage) {
            setAlert({ type: 'error', message: errorMessage });
        }
    }, [errorMessage, fetch, setAlert]);

    React.useEffect(() => {
        if (trackingNumber) {
            fetch(trackingNumber);
        }
    }, [fetch, trackingNumber]);

    const renderContent = React.useCallback(() => {
        if (isLoading) {
            return (
                <Grid item xs={12}>
                    <Box sx={{ display: 'flex', justifyContent: 'center', marginTop: 10 }}>
                        <CircularProgress size={100} />
                    </Box>
                </Grid>
            );
        }
        if (item) {
            return (
                <React.Fragment>
                    <Grid item xs={12} md={5} container>
                        <Grid item xs={12}>
                            <PackageDetail item={item} />
                        </Grid>
                        <Grid item xs={12}>
                            <div data-aos="fade-right">
                                <Paper sx={{ borderRadius: 5, py: 4 }}>
                                    <QRCodeSVG
                                        includeMargin={false}
                                        bgColor={theme.palette.background.default}
                                        size={256}
                                        fgColor={theme.palette.primary.main}
                                        level="Q"
                                        value={`package:${item.token}`}
                                        style={{
                                            display: 'block',
                                            margin: 'auto',
                                        }}
                                    />
                                </Paper>
                            </div>
                        </Grid>
                    </Grid>
                    <Grid item xs={12} md={7}>
                        <PackageHistories item={item} />
                    </Grid>
                    <Grid item xs={12}>
                        <PackageDeliveredMap item={item} />
                    </Grid>
                </React.Fragment>
            );
        }

        return null;
    }, [isLoading, item, theme.palette.background.default, theme.palette.primary.main]);

    return (
        <React.Fragment>
            <Helmet>
                <title>{t('tracking_page_title', { ns: 'glossary' })}</title>
            </Helmet>
            <Grid container className={classes.container}>
                <Grid item xs={12}>
                    <SearchBar isLoading={isLoading} onSearch={fetch} />
                </Grid>
                {renderContent()}
            </Grid>
            <AlertMessage />
        </React.Fragment>
    );
};

const Tracking = React.memo(TrackingComponent);

export default Tracking;
