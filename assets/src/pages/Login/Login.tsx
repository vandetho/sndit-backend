import React from 'react';

interface LoginProps {}

const LoginComponent: React.FunctionComponent<LoginProps> = () => {
    return <div />;
};

const Login = React.memo(LoginComponent);

export default Login;
