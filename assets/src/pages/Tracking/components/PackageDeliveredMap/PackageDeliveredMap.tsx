import React from 'react';
import { Package } from '@interfaces';
import { MapContainer, Marker, Popup, TileLayer } from 'react-leaflet';
import { useTranslation } from 'react-i18next';
import { Typography } from '@mui/material';

interface PackageDeliveredMapProps {
    item: Package;
}

const PackageDeliveredMap = React.memo<PackageDeliveredMapProps>(({ item }) => {
    const { t } = useTranslation();

    if (item.latitude && item.longitude) {
        return (
            <React.Fragment>
                <Typography variant="h6" textAlign="center" sx={{ py: 4 }}>
                    {t('delivery_location')}
                </Typography>
                <div data-aos="fade-in">
                    <MapContainer
                        center={{ lat: item.latitude, lng: item.longitude }}
                        zoom={15}
                        scrollWheelZoom={false}
                    >
                        <TileLayer
                            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                        />
                        <Marker position={{ lat: item.latitude, lng: item.longitude }}>
                            <Popup>
                                <br />
                                Latitude: {item.latitude}
                                <br />
                                Longitude: {item.longitude}
                            </Popup>
                        </Marker>
                    </MapContainer>
                </div>
            </React.Fragment>
        );
    }
    return null;
});

export default PackageDeliveredMap;
