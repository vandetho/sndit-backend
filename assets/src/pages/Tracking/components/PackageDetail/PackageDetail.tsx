import React from 'react';
import { Package } from '@interfaces';
import { Card, CardContent, Grid, Typography } from '@mui/material';
import { useTranslation } from 'react-i18next';
import { format } from 'date-fns';
import { DATETIME_FORMAT } from '@config';

interface PackageDetailProps {
    item: Package;
}

const PackageDetailComponent: React.FunctionComponent<PackageDetailProps> = ({ item }) => {
    const { t } = useTranslation();

    const renderMarking = React.useCallback(() => {
        return Object.keys(item.marking).map((marking) => (
            <Typography key={`tracking-package-marking-${marking}`} variant="h6">
                {t(marking)}
            </Typography>
        ));
    }, [item.marking, t]);

    return (
        <div data-aos="fade-right">
            <Card sx={{ borderRadius: 5 }}>
                <CardContent>
                    <Grid container>
                        <Grid item xs={6}>
                            <Typography variant="subtitle1">{t('name')}</Typography>
                        </Grid>
                        <Grid item xs={6}>
                            <Typography variant="h6">{item.name}</Typography>
                        </Grid>
                        {item.phoneNumber && (
                            <Grid item xs={12}>
                                <Typography variant="subtitle1">{item.phoneNumber}</Typography>
                            </Grid>
                        )}
                        {item.address && (
                            <Grid item xs={12}>
                                <Typography variant="h6">{item.address}</Typography>
                            </Grid>
                        )}
                        {item.city && (
                            <Grid item xs={12}>
                                <Typography variant="h6">{item.city.name}</Typography>
                            </Grid>
                        )}
                        <Grid item xs={6}>
                            <Typography variant="subtitle1">{t('company')}</Typography>
                        </Grid>
                        <Grid item xs={6}>
                            <Typography variant="h6">{item.company?.name}</Typography>
                        </Grid>
                        <Grid item xs={12}>
                            {renderMarking()}
                        </Grid>
                        <Grid item xs={6}>
                            <Typography variant="subtitle1">{t('created_at')}</Typography>
                        </Grid>
                        <Grid item xs={6}>
                            <Typography variant="subtitle1">{t('last_updated_at')}</Typography>
                        </Grid>
                        <Grid item xs={6}>
                            <Typography variant="h6">{format(new Date(item.createdAt), DATETIME_FORMAT)}</Typography>
                        </Grid>
                        <Grid item xs={6}>
                            <Typography variant="h6">{format(new Date(item.updatedAt), DATETIME_FORMAT)}</Typography>
                        </Grid>
                        <Grid item xs={12}>
                            <Typography variant="subtitle1">{t('deliverer_phone_number')}</Typography>
                        </Grid>
                        <Grid item xs={12}>
                            <Typography variant="h6">{item.deliverer?.phoneNumber}</Typography>
                        </Grid>
                    </Grid>
                </CardContent>
            </Card>
        </div>
    );
};

const PackageDetail = React.memo(PackageDetailComponent);

export default PackageDetail;
