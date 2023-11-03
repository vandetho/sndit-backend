import React from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Dialog, DialogContent, DialogTitle, IconButton, Typography, useMediaQuery } from '@mui/material';
import { useTheme } from '@mui/material/styles';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faTimes } from '@fortawesome/free-solid-svg-icons';
import { useTranslation } from 'react-i18next';
import { Keyboard, Mousewheel, Navigation, Pagination } from 'swiper/modules';

import 'swiper/css/bundle';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-coverflow';

interface LightboxProps {
    isVisible: boolean;
    images: string[];
    onClose: () => void;
}

const LightboxComponent: React.FunctionComponent<LightboxProps> = ({ isVisible, images, onClose }) => {
    const { t } = useTranslation();
    const theme = useTheme();
    const fullScreen = useMediaQuery(theme.breakpoints.down('md'));

    return (
        <Dialog
            fullScreen={fullScreen}
            open={isVisible}
            onClose={onClose}
            aria-labelledby="Lightbox for message attachments"
        >
            <DialogTitle id="Lightbox for message attachments" sx={{ m: 0, p: 2 }}>
                <Typography variant="subtitle1">{t('attachments')}</Typography>
                <IconButton
                    aria-label="close"
                    onClick={onClose}
                    sx={{
                        position: 'absolute',
                        right: 8,
                        top: 8,
                        color: (theme) => theme.palette.grey[500],
                    }}
                >
                    <FontAwesomeIcon icon={faTimes} />
                </IconButton>
            </DialogTitle>
            <DialogContent
                dividers
                style={{
                    height: 650,
                    justifyContent: 'center',
                    alignItems: 'center',
                    display: 'flex',
                }}
            >
                <Swiper
                    navigation={true}
                    pagination={true}
                    mousewheel={true}
                    keyboard={true}
                    modules={[Navigation, Pagination, Mousewheel, Keyboard]}
                >
                    {images.map((item, i) => (
                        <SwiperSlide key={`ticket-message-gallery-item-${i}`} style={{ alignItems: 'center' }}>
                            <img src={item} alt="Sndit App message Image" style={{ maxWidth: 450, maxHeight: 650 }} />
                        </SwiperSlide>
                    ))}
                </Swiper>
            </DialogContent>
        </Dialog>
    );
};

const Lightbox = React.memo(LightboxComponent);

export default Lightbox;
