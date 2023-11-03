import React from 'react';
import { styled, useTheme } from '@mui/material/styles';
import { Link as RouterLink } from 'react-router-dom';
import { AppBar as MuiAppBar, Button, IconButton, Link, Toolbar, useMediaQuery, useScrollTrigger } from '@mui/material';
import { useTranslation } from 'react-i18next';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import clsx from 'clsx';
import { gradients } from '@utils';
import { faCog, faEnvelope, faHome, faMapLocationDot } from '@fortawesome/free-solid-svg-icons';
import { useModalState } from '@hooks';
import { Drawer } from './components';
import Logo from '@images/white_logo_with_text.png';
import { GitHub } from '@mui/icons-material';

const PREFIX = 'top-bar';

const classes = {
    root: `${PREFIX}-root`,
    flexGrow: `${PREFIX}-flexGrow`,
    toolbar: `${PREFIX}-toolbar`,
    logoImage: `${PREFIX}-logoImage`,
};

const AppBar = styled(MuiAppBar)(({ theme }) => ({
    [`& .${classes.root}`]: {
        boxShadow: 'none',
        border: 'none',
    },
    [`& .${classes.flexGrow}`]: {
        flexGrow: 1,
    },
    [`& .${classes.toolbar}`]: {
        height: 80,
        [theme.breakpoints.down('lg')]: {
            height: 64,
        },
        [theme.breakpoints.up('lg')]: {
            margin: '0 auto',
            width: theme.breakpoints.values.md,
        },
        [theme.breakpoints.up('xl')]: {
            margin: '0 auto',
            width: theme.breakpoints.values.lg,
        },
    },
    [`& .${classes.logoImage}`]: {
        alignContent: 'center',
        height: 64,
        [theme.breakpoints.down('md')]: {
            height: 48,
        },
    },
}));

interface Props {
    window?: () => Window;
    children: React.ReactElement;
}

function ElevationScroll(props: Props) {
    const { children, window } = props;

    const trigger = useScrollTrigger({
        disableHysteresis: true,
        threshold: 0,
        target: window ? window() : undefined,
    });

    return React.cloneElement(children, {
        elevation: trigger ? 4 : 0,
        style: {
            background: gradients.primary,
        },
    });
}

interface TopBarProps {
    className?: string;
}

const TopBar: React.FC<TopBarProps> = (props) => {
    const { className, ...rest } = props;
    const { t } = useTranslation();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('md'));
    const { isOpen, onToggle } = useModalState();

    return (
        <ElevationScroll {...props}>
            <AppBar {...rest} className={clsx(classes.root, className)} color="primary">
                <Toolbar className={classes.toolbar}>
                    <Link component={RouterLink} to="/" color="inherit">
                        <img src={Logo} alt="Sndit" className={classes.logoImage} />
                    </Link>
                    <div className={classes.flexGrow} />
                    <IconButton component="a" href="https://github.com/vandetho/sndit-backend.git">
                        <GitHub />
                    </IconButton>
                    <Button color="inherit" component={RouterLink} to="/" startIcon={<FontAwesomeIcon icon={faHome} />}>
                        {isMobile ? '' : t('home')}
                    </Button>
                    <Button
                        color="inherit"
                        component={RouterLink}
                        to="/tracking"
                        startIcon={<FontAwesomeIcon icon={faMapLocationDot} />}
                    >
                        {isMobile ? '' : t('tracking')}
                    </Button>
                    <Button
                        color="inherit"
                        component={RouterLink}
                        to="/contact-us"
                        startIcon={<FontAwesomeIcon icon={faEnvelope} />}
                    >
                        {isMobile ? '' : t('contact_us')}
                    </Button>
                    <Button color="inherit" onClick={onToggle} startIcon={<FontAwesomeIcon icon={faCog} />}>
                        {isMobile ? '' : t('setting')}
                    </Button>
                    <Drawer isOpen={isOpen} onToggle={onToggle} />
                </Toolbar>
            </AppBar>
        </ElevationScroll>
    );
};

export default TopBar;
