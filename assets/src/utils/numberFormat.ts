export const currencyFormat = (value: number, currency = 'USD', locales = 'en-US'): string => {
    return new Intl.NumberFormat(locales, { style: 'currency', currency: currency }).format(value);
};

export const numberFormat = (value: number, locales = 'en-US'): string => {
    return new Intl.NumberFormat(locales).format(value);
};
