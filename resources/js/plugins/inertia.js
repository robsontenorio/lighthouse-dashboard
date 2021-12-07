import Vue from 'vue'
import { InertiaApp } from '@inertiajs/inertia-vue'
import DefaultLayout from '../layouts/default.vue'

Vue.use(InertiaApp)

const render = h => h(InertiaApp, {
    props: {
        initialPage: JSON.parse(app.dataset.page),
        resolveComponent: (name) => {
            return import(`../pages/${name}`).then(module => {
                if (!module.default.layout) {
                    module.default.layout = DefaultLayout
                }
                return module.default
            })
        },
    },
})

export default render