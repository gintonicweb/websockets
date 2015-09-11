requirejs.config({
    deps: [

    ],
    paths: {
        autobahn: "../vendor/autobahn/autobahn",
        react: "../vendor/react/react",
        reactDev: "../vendor/react/react-with-addons",
        classnames: "../vendor/classnames/index",
        JSXTransformer: "../vendor/jsx-requirejs-plugin/js/JSXTransformer",
        jsx: "../vendor/jsx-requirejs-plugin/js/jsx",
        text: "../vendor/requirejs-text/text",
        bootstrap: "../vendor/bootstrap/dist/js/bootstrap",
        jquery: "../vendor/jquery/dist/jquery"
    },
    shim: {
        bootstrap: [
            "jquery"
        ]
    },
    jsx: {
        fileExtension: ".jsx"
    },
    packages: [

    ]
});
