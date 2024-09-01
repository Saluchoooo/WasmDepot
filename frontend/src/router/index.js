import Vue from 'vue';
import Router from 'vue-router';
import LoginComponent from '@/components/LoginForm';
import RegisterForm from '@/components/RegisterForm.vue';
import ItemForm from '@/components/ItemForm.vue';
import ItemList from '@/components/ItemList.vue';
import ItemDetail from '@/components/ItemDetail.vue';
import MenuComponent from '@/components/MenuComponent.vue';

Vue.use(Router);

const routes = [
  {
    path: '*',
    redirect: '/menu',
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterForm,
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginComponent,
  },
  {
    path: '/menu',
    name: 'Menu',
    component: MenuComponent,
  },
  {
    path: '/newItem',
    name: 'NewItem',
    component: ItemForm,
  },
  {
    path: '/list',
    name: 'List',
    component: ItemList,
  },
  {
    path: '/items/:id',
    name: 'ItemDetail',
    component: ItemDetail,
    props: true,
  },
];

const router = new Router({
  mode: 'history', // Utilitza el mode 'history' per eliminar el hash (#) de l'URL
  base: process.env.BASE_URL,
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('token');

  if (isAuthenticated && (to.path === '/login' || to.path === '/register')) {
    next('/menu'); // Redirigeix a la pàgina de l'usuari autenticat si ja està logejat
  } else {
    next(); // Continua amb la navegació
  }
});

export default router;
