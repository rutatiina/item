
const ItemsForm = () => import('./components/l-limitless-bs4/Form.vue')
const ItemsIndex = () => import('./components/l-limitless-bs4/Index.vue')

const routes = [

    {
        path: '/items:parameters?',
        components: {
            default: ItemsIndex,
            //'sidebar-left': ComponentSidebarLeft,
            //'sidebar-right': ComponentSidebarRight
        },
        meta: {
            title: 'Contacts',
            metaTags: [
                {
                    name: 'description',
                    content: 'Items i.e. product, service ...'
                },
                {
                    property: 'og:description',
                    content: 'Items i.e. product, service ...'
                }
            ]
        }
    },
    {
        path: '/items/create',
        components: {
            default: ItemsForm,
            //'sidebar-left': ComponentSidebarLeft,
            //'sidebar-right': ComponentSidebarRight
        },
        meta: {
            title: 'Items :: Create',
            metaTags: [
                {
                    name: 'description',
                    content: 'Create Item i.e. product, service ...'
                },
                {
                    property: 'og:description',
                    content: 'Create Item i.e. product, service ...'
                }
            ]
        }
    },
    {
        path: '/items/:id/edit',
        components: {
            default: ItemsForm
        },
        meta: {
            title: 'Items :: Update',
            metaTags: [
                {
                    name: 'description',
                    content: 'Update Item'
                },
                {
                    property: 'og:description',
                    content: 'Update Item'
                }
            ]
        }
    },

]

export default routes
