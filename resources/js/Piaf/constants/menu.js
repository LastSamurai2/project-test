import {adminRoot} from "./config";
import {UserRole} from "../utils/auth.roles";

const data = [
    {
        id: 'dashboard',
        icon: 'iconsminds-shop-4',
        label: "menu.dashboard",
        to: `/${adminRoot}`
    },
    {
        id: "bizami.data",
        icon: "iconsminds-receipt-4",
        label: "menu.bizami-data",
        subs: [
            {
                icon: "simple-icon-list",
                label: "menu.bizami-products",
                to: 'bizami.product',
                isHandy: true
            },
            {
                icon: "simple-icon-organization",
                label: "menu.bizami-warehouses",
                to: 'bizami.warehouse',
            },
            {
                icon: "simple-icon-bag",
                label: "menu.bizami-warehouse_states",
                to: 'bizami.warehouse-state',
            },
            {
                icon: "simple-icon-docs",
                label: "menu.bizami-sale_documents",
                to: 'bizami.sale-document',
            }
        ]
    },
    {
        id: "bizami",
        icon: "iconsminds-calculator",
        label: "menu.bizami",
        subs: [
            {
                icon: "simple-icon-list",
                label: "menu.bizami-simulations",
                to: 'bizami.simulations',
            },
            {
                icon: "simple-icon-energy",
                label: "menu.bizami-algorithms",
                to: 'bizami.algorithm',
                isHandy: true
            }
        ]
    },
    {
        id: "system",
        icon: "iconsminds-gears",
        label: "menu.system",
        subs: [
            {
                icon: "simple-icon-control-play",
                label: "menu.dataflow",
                to: 'dataflows.report',
                isHandy: true
            },
            {
                icon: "simple-icon-user",
                label: "menu.users",
                to: 'user',
                isHandy: true
            }
        ],

    },
 /*   {
        id: "second-menu",
        icon: "iconsminds-chemical",
        label: "menu.second-menu",
        to: `${adminRoot}/second-menu`,
        subs: [{
            icon: "simple-icon-paper-plane",
            label: "menu.second",
            to: `${adminRoot}/second-menu/second`,
        },
        ]
    },
     {
         id: "pages",
         icon: "iconsminds-digital-drawing",
         label: "menu.pages",
         to: "/user/login",
         subs: [{
             icon: "simple-icon-user-following",
             label: "menu.login",
             to: "/user/login",
             newWindow: true
         }, {
             icon: "simple-icon-user-follow",
             label: "menu.register",
             to: "/user/register",
             newWindow: true
         }, {
             icon: "simple-icon-user-unfollow",
             label: "menu.forgot-password",
             to: "/user/forgot-password",
             newWindow: true
         },
             {
                 icon: "simple-icon-user-following",
                 label: "menu.reset-password",
                 to: "/user/reset-password",
                 newWindow: true
             }
         ]
     },
     {
         id: "single",
         icon: "iconsminds-three-arrow-fork",
         label: "menu.single",
         to: `${adminRoot}/single`,
     },
     {
         id: "docs",
         icon: "iconsminds-library",
         label: "menu.docs",
         to: "https://piaf-vue-docs.coloredstrategies.com/",
         newWindow: true
     }*/
];
export default data;
