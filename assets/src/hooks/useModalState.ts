import React from 'react';

export const useModalState = ({ initialOpen = false } = {}) => {
    const [isOpen, setIsOpen] = React.useState(initialOpen);

    const onOpen = () => {
        setIsOpen(true);
    };

    const onClose = () => {
        setIsOpen(false);
    };

    const onToggle = () => {
        setIsOpen(!isOpen);
    };

    return { onOpen, onClose, isOpen, onToggle };
};
