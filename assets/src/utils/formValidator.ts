import validate from 'validate.js';

export default (
    attributes: { [key: string]: any },
    constraints: { [key: string]: any },
): { [key: string]: string[] } | undefined => {
    return validate(attributes, constraints, { fullMessages: false });
};
