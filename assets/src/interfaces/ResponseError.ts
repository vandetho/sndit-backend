import { Response } from './Response';

export interface ResponseError<T> extends Response<T> {
    code: number;
}
