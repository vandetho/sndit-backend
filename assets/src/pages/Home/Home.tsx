import React from 'react';
import { Helmet } from 'react-helmet';
import { useTranslation } from 'react-i18next';
import { AppGallery, HeaderSection, KeyFeatures, PackageTimeline } from './components';

interface HomeProps {}

const Home = React.memo<HomeProps>(() => {
    const { t } = useTranslation();
    return (
        <React.Fragment>
            <Helmet>
                <title>{t('home_page_title', { ns: 'glossary' })}</title>
            </Helmet>
            <HeaderSection />
            <KeyFeatures />
            <PackageTimeline />
            <AppGallery />
        </React.Fragment>
    );
});

export default Home;
