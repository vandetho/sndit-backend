const Encore = require('@symfony/webpack-encore');
const WorkboxPlugin = require('workbox-webpack-plugin');
const path = require('path');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore.setOutputPath('public/build/')
    .setPublicPath('/build')
    .setManifestKeyPrefix('build/')
    .addEntry('sndit_app', './assets/src/Application.tsx')
    .splitEntryChunks()
    .configureSplitChunks(function (splitChunks) {
        splitChunks.minSize = 0;
    })
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableTypeScriptLoader((typeScriptConfigOptions) => {
        typeScriptConfigOptions.transpileOnly = true;
        typeScriptConfigOptions.configFile = 'tsconfig.json';
        typeScriptConfigOptions.silent = false;
    })
    .enableForkedTypeScriptTypesChecking()
    .enableIntegrityHashes(Encore.isProduction())
    .enableReactPreset();

const config = Encore.getWebpackConfig();

config.module.rules.push({
    test: /\.worker\.js$/,
    use: { loader: 'worker-loader' },
});

config.module.rules.push({
    test: /\.m?js$/,
    exclude: /(node_modules|bower_components)/,
    use: {
        loader: 'babel-loader',
        options: {
            presets: [['@babel/preset-env'], ['@babel/preset-react'], ['@babel/preset-typescript']],
            plugins: [
                [
                    'babel-plugin-import',
                    {
                        libraryName: 'lodash',
                        libraryDirectory: '',
                        camel2DashComponentName: false,
                    },
                    'lodash',
                ],
                [
                    'babel-plugin-import',
                    {
                        libraryName: '@mui/material',
                        libraryDirectory: '',
                        camel2DashComponentName: false,
                    },
                    'core',
                ],
                [
                    'babel-plugin-import',
                    {
                        libraryName: '@mui/styles',
                        libraryDirectory: '',
                        camel2DashComponentName: false,
                    },
                    'styles',
                ],
                [
                    'babel-plugin-import',
                    {
                        libraryName: '@mui/lab',
                        libraryDirectory: '',
                        camel2DashComponentName: false,
                    },
                    'lab',
                ],
                [
                    'babel-plugin-import',
                    {
                        libraryName: '@mui/icons-material',
                        libraryDirectory: '',
                        camel2DashComponentName: false,
                    },
                    'icons',
                ],
                ['date-fns'],
                ['@babel/transform-runtime'],
                [
                    '@babel/plugin-proposal-class-properties',
                    {
                        loose: true,
                    },
                ],
                ['babel-plugin-direct-import', { modules: ['@mui/material', '@mui/icons-material', '@mui/styles'] }],
            ],
        },
    },
});

config.plugins.push(
    new WorkboxPlugin.GenerateSW({
        swDest: 'service-worker.js',
        clientsClaim: true,
        skipWaiting: true,
    }),
);

config.resolve.alias = {
    '@config': path.resolve(__dirname, './assets/src/config'),
    '@components': path.resolve(__dirname, './assets/src/components'),
    '@constants': path.resolve(__dirname, './assets/src/constants'),
    '@context': path.resolve(__dirname, './assets/src/context'),
    '@fetchers': path.resolve(__dirname, './assets/src/fetchers'),
    '@hooks': path.resolve(__dirname, './assets/src/hooks'),
    '@i18n': path.resolve(__dirname, './assets/src/i18n.ts'),
    '@images': path.resolve(__dirname, './assets/images'),
    '@lotties': path.resolve(__dirname, './assets/lotties'),
    '@interfaces': path.resolve(__dirname, './assets/src/interfaces'),
    '@theme': path.resolve(__dirname, './assets/src/theme'),
    '@types': path.resolve(__dirname, './assets/src/types'),
    '@utils': path.resolve(__dirname, './assets/src/utils'),
    '@workflow': path.resolve(__dirname, './assets/src/workflow'),
};

if (!Encore.isProduction()) {
    config.plugins.push(new BundleAnalyzerPlugin());
}

module.exports = config;
