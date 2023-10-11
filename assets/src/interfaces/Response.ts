export interface Response<T> {
    error: boolean;
    message: string | null | undefined;
    data: T;
}
