document.addEventListener('DOMContentLoaded', () => {
    window.devLoader = {};
    document.querySelectorAll('[data-faisan-component]').forEach(async (el) => {
        const componentName = el.getAttribute('data-faisan-component');
        const response = await fetch(`../../components/${componentName}`);
        let html = await response.text();
        //REMOVE CODE INJECTED BY LIVE SERVER 
        const liveServerRegex = /<!-- Code injected by live-server -->([\s\S]*?)<\/script>/g;
        html = html.replace(liveServerRegex, "");
        //GET ALL DATA ATTRIBUTES
        const dataAttributes = el.dataset;
        const params = {};
        Object.keys(dataAttributes).forEach(key => {
            const normalizedKey = key.replace(/^faisan/, '').toLowerCase();
            params[normalizedKey] = dataAttributes[key];
        });

        //REPLACE ALL IF STATEMENTS
        const ifRegex = /<!--\s*IF\s*\(\s*([\w\d_-]+)\s*\)\s*:\s*-->([\s\S]*?)[\t\n\r]*<!--\s*ENDIF\s*-->/gi;
        html = html.replace(ifRegex, (match, variableName, innerContent) => {
            const varName = variableName.trim();
            const value = params[varName.toLowerCase()];
            if (value && value.trim() !== "" && value !== `{{${varName}}}`) {
                return innerContent;
            }
            return "";
        });

        //REPLACE ALL DATA ATTRIBUTES IN THE HTML
        Object.keys(params).forEach((key) => {
            const value = params[key];
            html = html.replaceAll(`{{${key}}}`, value);
        });
        el.outerHTML = html;
    });

    setTimeout(() => {
        if (window.devLoader.callback) {
            window.devLoader.callback();
        }
    }, 1000);

});