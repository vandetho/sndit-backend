import { City } from '@interfaces/City';
import { User } from '@interfaces/User';
import { Company } from '@interfaces/Company';

export interface Package {
    id: number;
    name: string;
    phoneNumber?: string;
    token: string;
    marking: { [key: string]: number };
    address?: string;
    createdAt: string;
    updatedAt: string;
    note?: string;
    latitude?: number;
    longitude?: number;
    city?: City;
    roles: string[];
    user?: User;
    deliverer?: User;
    creator: User;
    company: Company;
}
