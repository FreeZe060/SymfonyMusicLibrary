const Encore = require('@symfony/webpack-encore');

Encore
    // Répertoire de sortie des fichiers compilés
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Entrée de votre CSS ou JS
    .addEntry('style', './assets/css/style.css')

    // Activer ou désactiver les runtime chunks
    .enableSingleRuntimeChunk()

    // Activer PostCSS
    .enablePostCssLoader()

    // ... Ajoutez d'autres configurations si nécessaire

    ;

module.exports = Encore.getWebpackConfig();
