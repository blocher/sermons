import './bootstrap';
// import "fomantic-ui/dist/semantic.js";
// import '../css/app.css';

$('.ui.accordion').accordion({
    exclusive: false, selector: {
        trigger: '.title, .content'
    }
});

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

// createInertiaApp({
//     title: (title) => `${title} - ${appName}`,
//     resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
//     setup({ el, App, props, plugin }) {
//         return createApp({ render: () => h(App, props) })
//             .use(plugin)
//             .use(ZiggyVue, Ziggy)
//             .mount(el);
//     },
//     progress: {
//         color: '#4B5563',
//     },
// });
