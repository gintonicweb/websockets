requirejs.config({
    deps: [

    ],
    paths: {
        websockets: "../../websockets/js",
        autobahn: "../vendor/autobahn/autobahn",
        react: "../vendor/react/react",
        reactDev: "../vendor/react/react-with-addons",
        classnames: "../vendor/classnames/index",
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
