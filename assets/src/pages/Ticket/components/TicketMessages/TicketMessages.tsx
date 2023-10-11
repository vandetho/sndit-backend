import React from 'react';
import { TicketMessage } from '@interfaces';
import { Box, Card, CardContent, CardHeader, Grid, Typography } from '@mui/material';
import { format } from 'date-fns';
import { DATETIME_FORMAT } from '@config';
import { useTheme } from '@mui/material/styles';
import { createStyles, makeStyles } from '@mui/styles';
import { useModalState } from '@hooks';
import { Lightbox } from './components';

const useStyles = makeStyles(() =>
    createStyles({
        image: {
            cursor: 'pointer',
            margin: 0,
            maxHeight: 90,
            maxWidth: 90,
            marginLeft: 15,
        },
    }),
);

interface TicketMessagesProps {
    messages: TicketMessage[];
}

const TicketMessages = React.memo<TicketMessagesProps>(({ messages }) => {
    const theme = useTheme();
    const classes = useStyles();
    const { onToggle, isOpen } = useModalState();
    const [attachments, setAttachments] = React.useState<string[]>([]);

    const onClickImage = React.useCallback(
        (attachments: string[]) => {
            setAttachments(attachments);
            onToggle();
        },
        [onToggle],
    );

    const renderAttachments = React.useCallback(
        (message: TicketMessage) => {
            if (message.attachments.length) {
                return (
                    <Box sx={{ display: 'flex', flexDirection: 'row', mt: 2 }}>
                        {message.attachments.map((attachment, index) => (
                            <img
                                src={attachment}
                                alt="Ticket message attachments"
                                key={`ticket-message-attachments-item-${index}`}
                                className={classes.image}
                                onClick={() => onClickImage(message.attachments)}
                            />
                        ))}
                    </Box>
                );
            }
            return null;
        },
        [classes.image, onToggle],
    );

    return (
        <React.Fragment>
            <Grid container>
                {messages.map((message, index) => {
                    const user = message.user
                        ? `${message.user.lastName} ${message.user.firstName}`
                        : message.internalUser
                        ? `${message.internalUser.lastName} ${message.internalUser.firstName}`
                        : 'Anonymous';
                    return (
                        <Grid item xs={12} key={`ticket-message-items-${index}`}>
                            <Card
                                data-aos={index % 2 === 0 ? 'fade-right' : 'fade-left'}
                                sx={{ borderRadius: 5, mt: 2 }}
                            >
                                <CardHeader title={user} />
                                <CardContent>
                                    <Typography variant="body1">{message.content}</Typography>
                                    {renderAttachments(message)}
                                    <Typography
                                        variant="caption"
                                        component="p"
                                        color={theme.palette.text.secondary}
                                        sx={{ mt: 2 }}
                                    >
                                        {format(new Date(message.createdAt), DATETIME_FORMAT)}
                                    </Typography>
                                </CardContent>
                            </Card>
                        </Grid>
                    );
                })}
            </Grid>
            <Lightbox isVisible={isOpen} images={attachments} onClose={onToggle} />
        </React.Fragment>
    );
});

export default TicketMessages;
