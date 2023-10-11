export interface User {
    id: number;
    token: string;
    phoneNumber: string;
    countryCode: string;
    firstName: string;
    lastName: string;
    fullName: string;
    imageFile: string | undefined;
    locale: string;
}
