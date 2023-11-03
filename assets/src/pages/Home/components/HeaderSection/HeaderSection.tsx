import React from 'react';
import { styled, useTheme } from '@mui/material/styles';
import { Grid, Typography, useMediaQuery } from '@mui/material';
import clsx from 'clsx';
import { gradients } from '@utils';
import { useTranslation } from 'react-i18next';
import IsometricDetail from '@images/isometric_detail.png';

const PREFIX = 'HeaderSection';

const classes = {
    root: `${PREFIX}-root`,
    container: `${PREFIX}-container`,
    marginBottom: `${PREFIX}-marginBottom`,
    imageContainer: `${PREFIX}-imageContainer`,
    image: `${PREFIX}-image`,
};

const Root = styled('div')(({ theme }) => ({
    [`&.${classes.root}`]: {
        background: gradients.primary,
        [theme.breakpoints.down('md')]: {
            height: 750,
        },
        [theme.breakpoints.up('md')]: {
            height: 950,
        },
    },

    [`& .${classes.container}`]: {
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

    [`& .${classes.marginBottom}`]: {
        marginBottom: 40,
    },

    [`& .${classes.imageContainer}`]: {
        margin: 'auto',
        maxWidth,
    },

    [`& .${classes.image}`]: {
        maxWidth,
    },
}));

const maxWidth = '90%';

interface HeaderSectionProps {}

const HeaderSection = React.memo<HeaderSectionProps>(() => {
    const { t } = useTranslation();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('lg'));

    const textAlign = React.useMemo(() => (isMobile ? 'center' : 'left'), [isMobile]);

    return (
        <Root className={classes.root}>
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
        </Root>
    );
});

export default HeaderSection;
