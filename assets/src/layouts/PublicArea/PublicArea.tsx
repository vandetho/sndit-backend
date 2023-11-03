import React from 'react';
import { styled, useTheme } from '@mui/material/styles';
import { CssBaseline, useMediaQuery } from '@mui/material';
import clsx from 'clsx';
import { Outlet } from 'react-router';
import { Footer, Topbar } from './components';

const PREFIX = 'PublicArea';

const classes = {
    root: `${PREFIX}-root`,
    content: `${PREFIX}-content`,
};

const Root = styled('div')(({ theme }) => ({
    [`&.${classes.root}`]: {
        height: '100%',
        zIndex: 1,
        [theme.breakpoints.down('md')]: {
            height: 64,
        },
    },

    [`& .${classes.content}`]: {},
}));

interface PublicAreaProps {
    withBreadcrumbs?: boolean;
    className?: string;
}

const PublicArea: React.FC<PublicAreaProps> = () => {
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('md'));

    return (
        <Root
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
        </Root>
    );
};

export default PublicArea;
