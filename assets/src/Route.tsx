import React from 'react';
import { Route as RNRoute, Routes } from 'react-router-dom';
import { PublicArea } from './layouts';

const HomePage = React.lazy(() => import('./pages/Home'));
const ContactUsPage = React.lazy(() => import('./pages/ContactUs'));
const TicketPage = React.lazy(() => import('./pages/Ticket'));
const TrackingPage = React.lazy(() => import('./pages/Tracking'));

interface RouteProps {}

const Route: React.FC<RouteProps> = (): JSX.Element => (
    <Routes>
        <RNRoute path="/" element={<PublicArea />}>
            <RNRoute
                index
                element={
                    <React.Suspense fallback={<>...</>}>
                        <HomePage />
                    </React.Suspense>
                }
            />
            <RNRoute
                path="contact-us"
                element={
                    <React.Suspense fallback={<>...</>}>
                        <ContactUsPage />
                    </React.Suspense>
                }
            />
            <RNRoute
                path="/tracking"
                element={
                    <React.Suspense fallback={<>...</>}>
                        <TrackingPage />
                    </React.Suspense>
                }
            />
            <RNRoute
                path="/tickets/:token"
                element={
                    <React.Suspense fallback={<>...</>}>
                        <TicketPage />
                    </React.Suspense>
                }
            />
        </RNRoute>
    </Routes>
);

export default Route;
