declare module '*.svg' {
    import React = require('react');
    export const ReactComponent: React.FC<React.SVGProps<SVGSVGElement>>;
    const src: string;
    export default src;
}

declare module '*.png' {
    const content: string;
    export default content;
}

declare module 'react-barcode' {
    namespace ReactBarcode {
        type Options = {
            width?: number;
            height?: number;
            format?: 'CODE39' | 'CODE128' | 'EAN13' | 'ITF14' | 'MSI' | 'pharmacode' | 'codabar';
            displayValue?: boolean;
            fontOptions?: string;
            font?: string;
            textAlign?: string;
            textPosition?: string;
            textMargin?: number;
            fontSize?: number;
            background?: string;
            lineColor?: string;
            margin?: number;
            marginTop?: number;
            marginBottom?: number;
            marginLeft?: number;
            marginRight?: number;
        };

        type Props = {
            value: string;
        } & ReactBarcode.Options;
    }

    const ReactBarcode: React.FunctionComponent<ReactBarcode.Props>;

    export = ReactBarcode;
}
