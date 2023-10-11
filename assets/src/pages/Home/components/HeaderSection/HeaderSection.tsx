import React from 'react';
import { Grid, Typography, useMediaQuery } from '@mui/material';
import { Theme, useTheme } from '@mui/material/styles';
import createStyles from '@mui/styles/createStyles';
import makeStyles from '@mui/styles/makeStyles';
import clsx from 'clsx';
import { gradients } from '@utils';
import { useTranslation } from 'react-i18next';
import IsometricDetail from '@images/isometric_detail.png';

const maxWidth = '90%';
const useStyles = makeStyles((theme: Theme) =>
    createStyles({
        root: {
            background: gradients.primary,
            [theme.breakpoints.down('md')]: {
                height: 750,
            },
            [theme.breakpoints.up('md')]: {
                height: 950,
            },
        },
        container: {
            margin: '0 auto',
            paddingTop: 125,
            [theme.breakpoints.down('md')]: {
                paddingTop: 71,
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
        marginBottom: {
            marginBottom: 40,
        },
        imageContainer: {
            margin: 'auto',
            maxWidth,
        },
        image: {
            maxWidth,
        },
    }),
);

interface HeaderSectionProps {}

const HeaderSection = React.memo<HeaderSectionProps>(() => {
    const classes = useStyles();
    const { t } = useTranslation();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('lg'));

    const textAlign = React.useMemo(() => (isMobile ? 'center' : 'left'), [isMobile]);

    return (
        <div className={classes.root}>
            <Grid container spacing={2} justifyContent="space-between" className={classes.container}>
                <Grid item sm={12} className={clsx({ [classes.marginBottom]: isMobile })}>
                    <div data-aos="fade-right">
                        <Typography
                            variant="h3"
                            style={{ fontWeight: 'bold', padding: 10, color: 'white' }}
                            align={textAlign}
                        >
                            {t('top_section_title', { ns: 'glossary' })}
                        </Typography>
                        <Typography
                            variant="h6"
                            style={{ fontWeight: 'bold', padding: 10, color: 'white' }}
                            align={textAlign}
                        >
                            {t('top_section_detail', { ns: 'glossary' })}
                        </Typography>
                    </div>
                </Grid>
                <Grid item xs={12} container justifyContent="center">
                    <div data-aos="fade-down" className={classes.imageContainer}>
                        <img src={IsometricDetail} alt="Sndit App" className={classes.image} />
                    </div>
                </Grid>
            </Grid>
        </div>
    );
});

export default HeaderSection;
