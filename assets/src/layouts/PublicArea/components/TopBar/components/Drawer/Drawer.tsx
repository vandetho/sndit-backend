import React from 'react';
import { Button, ButtonGroup, Divider, Grid, IconButton, SwipeableDrawer, Typography } from '@mui/material';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCircleHalfStroke, faMoon, faSun, faTimes } from '@fortawesome/free-solid-svg-icons';
import { useLanguage, useTheme } from '@context';
import { styled } from '@mui/styles';
import { Theme } from '@mui/material/styles';
import English from '@images/us.svg';
import Francais from '@images/fr.svg';
import Khmer from '@images/kh.svg';

const DrawerHeader = styled('div')(({ theme }: { theme: Theme }) => ({
    display: 'flex',
    flexDirection: 'row',
    alignItems: 'center',
    padding: theme.spacing(0, 1),
    ...theme.mixins.toolbar,
    justifyContent: 'space-between',
}));

interface DrawerProps {
    isOpen: boolean;
    onToggle: () => void;
}

const Drawer = React.memo<DrawerProps>(({ isOpen, onToggle }) => {
    const { mode, onChangeMode } = useTheme();
    const { language, onChangeLanguage } = useLanguage();

    return (
        <SwipeableDrawer anchor="right" open={isOpen} onClose={onToggle} onOpen={onToggle}>
            <DrawerHeader>
                <Typography variant="subtitle1">SETTING</Typography>
                <IconButton onClick={onToggle}>
                    <FontAwesomeIcon icon={faTimes} />
                </IconButton>
            </DrawerHeader>
            <Divider />
            <Grid container spacing={4} justifyContent="center">
                <Grid item xs={10}>
                    <Typography variant="body1">MODE</Typography>
                    <ButtonGroup fullWidth size="large" aria-label="large theme button group">
                        <Button
                            variant={mode === 'light' ? 'contained' : 'outlined'}
                            key="light"
                            sx={{ borderTopLeftRadius: 10, borderBottomLeftRadius: 10 }}
                            startIcon={<FontAwesomeIcon icon={faSun} />}
                            onClick={() => onChangeMode('light')}
                        >
                            Light
                        </Button>
                        <Button
                            variant={mode === 'system' ? 'contained' : 'outlined'}
                            key="system"
                            startIcon={<FontAwesomeIcon icon={faCircleHalfStroke} />}
                            onClick={() => onChangeMode('system')}
                        >
                            System
                        </Button>
                        <Button
                            variant={mode === 'dark' ? 'contained' : 'outlined'}
                            key="dark"
                            sx={{ borderTopRightRadius: 10, borderBottomRightRadius: 10 }}
                            endIcon={<FontAwesomeIcon icon={faMoon} />}
                            onClick={() => onChangeMode('dark')}
                        >
                            Dark
                        </Button>
                    </ButtonGroup>
                </Grid>
                <Grid item xs={10}>
                    <Typography variant="body1">LANGUAGE</Typography>
                    <ButtonGroup fullWidth size="large" aria-label="large language button group" orientation="vertical">
                        <Button
                            variant={language === 'en' ? 'contained' : 'outlined'}
                            key="english"
                            sx={{ borderTopRightRadius: 10, borderTopLeftRadius: 10 }}
                            endIcon={<img src={English} alt="English" style={{ width: 25, height: 25 }} />}
                            onClick={() => onChangeLanguage('en')}
                        >
                            English
                        </Button>
                        <Button
                            variant={language === 'kh' ? 'contained' : 'outlined'}
                            key="khmer"
                            endIcon={<img src={Khmer} alt="ភាសាខ្មែរ" style={{ width: 25, height: 25 }} />}
                            onClick={() => onChangeLanguage('kh')}
                        >
                            ភាសាខ្មែរ
                        </Button>
                        <Button
                            variant={language === 'fr' ? 'contained' : 'outlined'}
                            key="francais"
                            sx={{ borderBottomRightRadius: 10, borderBottomLeftRadius: 10 }}
                            endIcon={<img src={Francais} alt="Français" style={{ width: 25, height: 25 }} />}
                            onClick={() => onChangeLanguage('fr')}
                        >
                            Français
                        </Button>
                    </ButtonGroup>
                </Grid>
            </Grid>
        </SwipeableDrawer>
    );
});

export default Drawer;
