import React from 'react';

interface SecuredAreaProps {}

const SecuredAreaComponent: React.FunctionComponent<SecuredAreaProps> = () => {
    return <div />;
};

const SecuredArea = React.memo(SecuredAreaComponent);

export default SecuredArea;
