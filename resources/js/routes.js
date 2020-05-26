import Home from './components/Home';
import Currency from './components/Currency';
import NotFound from './components/errors/NotFound';

export default {
  mode: 'history',
  linkActiveClass: 'text-blue-400 no-underline hover:text-gray-100 border-b-2 border-blue-400 hover:border-blue-400',
  routes: [
    {
      path: '*',
      component: NotFound
    },
    {
      path: '/',
      component: Home
    },
    {
      path: '/currency/:currency',
      component: Currency
    },
  ]
}
