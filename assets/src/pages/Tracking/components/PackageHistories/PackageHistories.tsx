import React from 'react';
import { Package } from '@interfaces';
import { usePackageHistoriesFetcher } from '@fetchers';
import Timeline from '@mui/lab/Timeline';
import TimelineItem from '@mui/lab/TimelineItem';
import TimelineSeparator from '@mui/lab/TimelineSeparator';
import TimelineConnector from '@mui/lab/TimelineConnector';
import TimelineContent from '@mui/lab/TimelineContent';
import TimelineDot from '@mui/lab/TimelineDot';
import { useTranslation } from 'react-i18next';
import { Paper, Typography } from '@mui/material';
import { format } from 'date-fns';
import { DATETIME_FORMAT } from '@config';

interface PackageDetailProps {
    item: Package;
}

const PackageDetailComponent: React.FunctionComponent<PackageDetailProps> = ({ item }) => {
    const { t } = useTranslation();
    const { fetch, histories } = usePackageHistoriesFetcher(item);

    React.useEffect(() => {
        fetch();
    }, [fetch]);

    return (
        <Timeline position="alternate">
            {histories.map((history, index) => (
                <TimelineItem key={`package-timeline-${history.transitionName}`}>
                    <TimelineSeparator>
                        <TimelineDot />
                        <TimelineConnector />
                    </TimelineSeparator>
                    <TimelineContent>
                        <Paper data-aos={index % 2 === 0 ? 'fade-left' : 'fade-right'} sx={{ p: 2, borderRadius: 5 }}>
                            <Typography variant="subtitle1">{t(history.transitionName)}</Typography>
                            <Typography variant="body1">{history.description}</Typography>
                            <Typography variant="caption">
                                {format(new Date(history.createdAt), DATETIME_FORMAT)}
                            </Typography>
                        </Paper>
                    </TimelineContent>
                </TimelineItem>
            ))}
        </Timeline>
    );
};

const PackageHistories = React.memo(PackageDetailComponent);

export default PackageHistories;
