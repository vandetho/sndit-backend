export interface InternalUser {
    id: number;
    token: string;
    phoneNumber: string;
    email: string;
    countryCode: string;
    firstName: string;
    lastName: string;
    fullName: string;
    imageFile: string | undefined;
    locale: string;
}
