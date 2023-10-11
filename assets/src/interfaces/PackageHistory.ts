import { User } from '@interfaces/User';

export interface PackageHistory {
    id: number;
    transitionName: string;
    description: string;
    createdAt: string;
    user: User;
}
