import React from 'react';
import makeStyles from '@mui/styles/makeStyles';
import { Theme, Typography, useMediaQuery } from '@mui/material';
import createStyles from '@mui/styles/createStyles';
import { useTranslation } from 'react-i18next';
import { Swiper, SwiperSlide } from 'swiper/react';
import SwiperCore, { EffectCoverflow, Navigation, Pagination } from 'swiper';
import Employees from '@images/gallery/screenshot - employees.png';
import GiveTake from '@images/gallery/screenshot - give take.png';
import HomePage from '@images/gallery/screenshot - home page.png';
import NewPackage from '@images/gallery/screenshot - add new package.png';
import Packages from '@images/gallery/screenshot - packages.png';
import Package from '@images/gallery/screenshot - package.png';
import Map from '@images/gallery/screenshot - maps.png';
import Company from '@images/gallery/screenshot - company.png';
import Template from '@images/gallery/screenshot - template.png';
import { useTheme } from '@mui/material/styles';
import 'swiper/css/bundle';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-coverflow';

const useStyles = makeStyles((theme: Theme) =>
    createStyles({
        mainContainer: {
            backgroundColor: theme.palette.divider,
            paddingBottom: theme.spacing(5),
        },
    }),
);

SwiperCore.use([Navigation, Pagination, EffectCoverflow]);

interface AppGalleryProps {}

const items = [HomePage, NewPackage, Packages, Package, GiveTake, Map, Company, Employees, Template];

const AppGalleryComponent: React.FunctionComponent<AppGalleryProps> = () => {
    const { t } = useTranslation();
    const classes = useStyles();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('md'));

    return (
        <div className={classes.mainContainer}>
            <Typography variant="h3" textAlign="center" sx={{ py: 5 }}>
                {t('gallery')}
            </Typography>
            <Swiper
                slidesPerView={isMobile ? 2 : 6}
                navigation
                pagination={{ clickable: true }}
                coverflowEffect={{
                    rotate: 50,
                    stretch: 0,
                    depth: 100,
                    modifier: 1,
                    slideShadows: false,
                }}
                centeredSlides
                style={{ height: 550 }}
            >
                {items.map((item, i) => (
                    <SwiperSlide key={`app-gallery-item-${i}`}>
                        <img src={item} alt="Sndit App Image" style={{ width: 250 }} />
                    </SwiperSlide>
                ))}
            </Swiper>
        </div>
    );
};

const AppGallery = React.memo(AppGalleryComponent);

export default AppGallery;
