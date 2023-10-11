import { Marking } from '@interfaces/Marking';

export interface Ticket {
    id: number;
    name: string;
    email: string;
    token: string;
    phoneNumber: string;
    content: string;
    marking: Marking;
    createdAt: string;
    updatedAt: string;
}
