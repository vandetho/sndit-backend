import { User } from '@interfaces/User';
import { InternalUser } from '@interfaces/InternalUser';

export interface TicketMessage {
    id: number;
    content: string;
    attachments: string[];
    createdAt: string;
    updatedAt: string;
    user?: User;
    internalUser?: InternalUser;
}
