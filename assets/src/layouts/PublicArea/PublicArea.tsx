import React from 'react';
import { CssBaseline, useMediaQuery } from '@mui/material';
import { Theme, useTheme } from '@mui/material/styles';
import makeStyles from '@mui/styles/makeStyles';
import clsx from 'clsx';
import { Outlet } from 'react-router';
import { Footer, Topbar } from './components';

const useStyles = makeStyles((theme: Theme) => ({
    root: {
        height: '100%',
        zIndex: 1,
        [theme.breakpoints.down('md')]: {
            height: 64,
        },
    },
    content: {},
}));

interface PublicAreaProps {
    withBreadcrumbs?: boolean;
    className?: string;
}

const PublicArea: React.FC<PublicAreaProps> = () => {
    const classes = useStyles();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('md'));

    return (
        <div
            className={clsx({
                [classes.root]: true,
            })}
        >
            <CssBaseline />
            <Topbar />
            <main className={classes.content} style={{ minHeight: `calc(100vh - ${isMobile ? 400 : 200}px)` }}>
                <Outlet />
            </main>
            <Footer />
        </div>
    );
};

export default PublicArea;
