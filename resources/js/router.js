import { createRouter, createWebHistory } from 'vue-router';
import store from "./vuex";
import AdminLayout from "./views/admin/layout/index.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            redirect: '/login'
        },
        {
            path: "/login",
            name: "login",
            component: () => import("./views/login/index.vue")
        },
        {
            path: "/register",
            name: "register",
            component: () => import("./views/register/index.vue")
        },
        {
            path: "/verify/user/:id",
            name: "verify",
            props: true,
            component: () => import("./views/verify/index.vue")
        },
        {
            path: "/forgot-password",
            name: "forgot",
            component: () => import("./views/forgot/index.vue")
        },
        {
            path: "/reset/:token",
            name: "reset",
            component: () => import("./views/reset/index.vue")
        },
        /**
         * Admin routes
         */
        {
            path: "/admin",
            name: "admin",
            component: () => import("./views/admin/dashboard.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/components/buttons",
            name: "buttons",
            component: () => import("./views/admin/buttons.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/components/cards",
            name: "cards",
            component: () => import("./views/admin/cards.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/utilities/colors",
            name: "colors",
            component: () => import("./views/admin/colors.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/utilities/borders",
            name: "borders",
            component: () => import("./views/admin/borders.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/utilities/animations",
            name: "animations",
            component: () => import("./views/admin/animations.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/utilities/other",
            name: "other",
            component: () => import("./views/admin/other.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/pages/page-not-found",
            name: "page-not-found",
            component: () => import("./views/admin/page-not-found.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/pages/blank",
            name: "blank",
            component: () => import("./views/admin/blank.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/charts",
            name: "charts",
            component: () => import("./views/admin/charts.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/tables",
            name: "tables",
            component: () => import("./views/admin/tables.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/qris-data-form",
            name: "admin.qris-data-form",
            component: () => import("./views/admin/qris/QrisDataForm.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/reports/qris-data",
            name: "admin.reports.qris-data",
            component: () => import("./views/admin/qris/QrisDataReport.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/biller-data-form",
            name: "admin.biller-data-form",
            component: () => import("./views/admin/biller/BillerDataForm.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/reports/biller-data",
            name: "admin.reports.biller-data",
            component: () => import("./views/admin/biller/BillerDataReport.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/debit-data-form",
            name: "admin.debit-data-form",
            component: () => import("./views/admin/debit/DebitDataForm.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/reports/debit-data",
            name: "admin.reports.debit-data",
            component: () => import("./views/admin/debit/DebitDataReport.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/admin/combined-chart",
            name: "admin.combined-chart",
            component: () => import("./views/admin/CombinedChartPage.vue"),
            meta: {
                requiresAuth: true,
                layout: AdminLayout
            }
        },
        {
            path: "/chart/qris-line",
            name: "qris-line-chart",
            component: () => import("./views/admin/charts.vue")
        },
        {
            path: "/chart/qris-hana",
            name: "qris-hana-chart",
            component: () => import("./views/admin/charts.vue")
        },
        {
            path: "/chart/biller-line",
            name: "biller-line-chart",
            component: () => import("./views/admin/charts.vue")
        },
        {
            path: "/chart/biller-hana",
            name: "biller-hana-chart",
            component: () => import("./views/admin/charts.vue")
        },
        {
            path: "/chart/debit-line",
            name: "debit-line-chart",
            component: () => import("./views/admin/charts.vue")
        },
        {
            path: "/chart/debit-hana",
            name: "debit-hana-chart",
            component: () => import("./views/admin/charts.vue")
        },
        {
            path: "/:pathMatch(.*)*",
            name: "not-found",
            component: () => import("./views/admin/page-not-found.vue"),
            meta: {
                requiresAuth: true
            }
        }
    ]
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters.user) {
            next();
            return;
        }
        next("/login");
    } else {
        next();
    }
});

export default router;
