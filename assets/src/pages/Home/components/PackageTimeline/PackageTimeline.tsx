import React from 'react';
import { styled } from '@mui/material/styles';
import { Box, Button, Grid, Step, StepLabel, Stepper, Typography } from '@mui/material';
import { useTranslation } from 'react-i18next';

const PREFIX = 'PackageTimeline';

const classes = {
    container: `${PREFIX}-container`,
};

const StyledGrid = styled(Grid)(({ theme }) => ({
    [`&.${classes.container}`]: {
        margin: '0 auto',
        paddingTop: 125,
        paddingBottom: 125,
        [theme.breakpoints.down('md')]: {
            paddingTop: 71,
        },
        [theme.breakpoints.up('md')]: {
            width: theme.breakpoints.values.md,
            paddingTop: 71,
        },
        [theme.breakpoints.up('lg')]: {
            width: theme.breakpoints.values.lg,
        },
    },
}));

interface PackageTimelineProps {}

const PackageTimeline = React.memo<PackageTimelineProps>(() => {
    const { t } = useTranslation();
    const [activeStep, setActiveStep] = React.useState(0);

    const steps = React.useMemo(
        () => [
            t('create_package', { ns: 'glossary' }),
            t('give_to_deliverer', { ns: 'glossary' }),
            t('on_delivery', { ns: 'glossary' }),
            t('delivered', { ns: 'glossary' }),
        ],
        [t],
    );

    React.useEffect(() => {
        const interval = setInterval(() => {
            setActiveStep((prevActiveStep) => (prevActiveStep === steps.length - 1 ? 0 : prevActiveStep + 1));
        }, 5000);

        return () => {
            clearInterval(interval);
        };
    }, [steps.length]);

    const handleNext = React.useCallback(() => {
        setActiveStep((prevActiveStep) => (prevActiveStep === steps.length - 1 ? 0 : prevActiveStep + 1));
    }, [steps.length]);

    const handleBack = React.useCallback(() => {
        setActiveStep((prevActiveStep) => (prevActiveStep === 0 ? steps.length - 1 : prevActiveStep - 1));
    }, [steps.length]);

    const handleReset = React.useCallback(() => {
        setActiveStep(0);
    }, []);

    const renderActiveContent = React.useCallback(() => {
        const content = [
            t('create_package_detail', { ns: 'glossary' }),
            t('give_to_deliverer_content', { ns: 'glossary' }),
            t('on_delivery_content', { ns: 'glossary' }),
            t('delivered_content', { ns: 'glossary' }),
        ];

        return content[activeStep];
    }, [activeStep, t]);

    return (
        <StyledGrid container className={classes.container}>
            <Grid item md={12}>
                <div data-aos="fade-down">
                    <Box
                        sx={{
                            marginBottom: 5,
                        }}
                    >
                        <Typography variant="h3" style={{ fontWeight: 'bold', paddingBottom: 70 }} align="center">
                            {t('package_timeline_title', { ns: 'glossary' })}
                        </Typography>
                    </Box>
                </div>
                <div data-aos="fade-up">
                    <Stepper activeStep={activeStep} alternativeLabel>
                        {steps.map((label) => (
                            <Step key={label}>
                                <StepLabel>{label}</StepLabel>
                            </Step>
                        ))}
                    </Stepper>
                    {activeStep === steps.length ? (
                        <React.Fragment>
                            <div data-aos="fade-right">
                                <Box sx={{ my: 5, px: 10 }}>
                                    <Typography variant="body1">
                                        {t('delivered_content', { ns: 'glossary' })}
                                    </Typography>
                                </Box>
                            </div>
                            <Box sx={{ display: 'flex', flexDirection: 'row', pt: 2, px: 5 }}>
                                <Box sx={{ flex: '1 1 auto' }} />
                                <Button onClick={handleReset}>{t('reset')}</Button>
                            </Box>
                        </React.Fragment>
                    ) : (
                        <React.Fragment>
                            <div data-aos="fade-right">
                                <Box sx={{ my: 5, px: 10 }}>
                                    <Typography variant="body1">{renderActiveContent()}</Typography>
                                </Box>
                            </div>
                            <Box sx={{ display: 'flex', flexDirection: 'row', pt: 2, px: 5 }}>
                                <Button color="inherit" disabled={activeStep === 0} onClick={handleBack} sx={{ mr: 1 }}>
                                    {t('back')}
                                </Button>
                                <Box sx={{ flex: '1 1 auto' }} />
                                <Button onClick={handleNext}>
                                    {activeStep === steps.length - 1 ? t('finish') : t('next')}
                                </Button>
                            </Box>
                        </React.Fragment>
                    )}
                </div>
            </Grid>
        </StyledGrid>
    );
});

export default PackageTimeline;
